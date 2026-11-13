<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class CheckoutController extends Controller
{
    // Ongkos kirim flat (disederhanakan untuk tugas ini)
    private const SHIPPING_COST = 10000;

    public function index(Request $request)
    {
        abort_if($request->user()->isAdmin(), 403, 'Admin tidak dapat berbelanja.');

        $items = $request->user()->cartItems()->with('product')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $subtotal = $items->sum(fn ($item) => $item->product->price * $item->quantity);
        $shipping = self::SHIPPING_COST;
        $total = $subtotal + $shipping;

        return view('checkout.index', compact('items', 'subtotal', 'shipping', 'total'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_if($user->isAdmin(), 403, 'Admin tidak dapat berbelanja.');

        // Catatan: total TIDAK diterima dari browser, hanya data penerima
        $data = $request->validate([
            'recipient_name'  => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'city'            => ['required', 'string', 'max:100'],
            'postal_code'     => ['required', 'digits:5'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ], [
            'recipient_phone.regex' => 'Nomor telepon tidak valid.',
            'postal_code.digits'    => 'Kode pos harus 5 digit angka.',
        ]);

        try {
            $order = DB::transaction(function () use ($user, $data) {
                $cartItems = $user->cartItems()->get();

                if ($cartItems->isEmpty()) {
                    throw new RuntimeException('Keranjang masih kosong.');
                }

                // Kunci baris produk supaya tidak ada dua checkout yang berebut stok
                $products = Product::whereIn('id', $cartItems->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Validasi ulang stok dan hitung subtotal dari harga di database
                $subtotal = 0;
                foreach ($cartItems as $cartItem) {
                    $product = $products->get($cartItem->product_id);

                    if (! $product || ! $product->is_active) {
                        throw new RuntimeException('Salah satu menu sudah tidak tersedia.');
                    }
                    if ($product->stock < $cartItem->quantity) {
                        throw new RuntimeException(
                            "Stok {$product->name} tidak mencukupi (tersisa {$product->stock})."
                        );
                    }

                    $subtotal += $product->price * $cartItem->quantity;
                }

                $order = Order::create([
                    'user_id'          => $user->id,
                    'order_number'     => $this->generateOrderNumber(),
                    'recipient_name'   => $data['recipient_name'],
                    'recipient_phone'  => $data['recipient_phone'],
                    'shipping_address' => $data['shipping_address'],
                    'city'             => $data['city'],
                    'postal_code'      => $data['postal_code'],
                    'notes'            => $data['notes'] ?? null,
                    'subtotal'         => $subtotal,
                    'shipping_cost'    => self::SHIPPING_COST,
                    'total'            => $subtotal + self::SHIPPING_COST,
                    'payment_status'   => Order::PAYMENT_PENDING,
                    'order_status'     => Order::STATUS_PENDING,
                ]);

                foreach ($cartItems as $cartItem) {
                    $product = $products->get($cartItem->product_id);

                    // Snapshot nama dan harga saat pembelian
                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'price'        => $product->price,
                        'quantity'     => $cartItem->quantity,
                        'subtotal'     => $product->price * $cartItem->quantity,
                    ]);

                    // Stok langsung dipesan (dikembalikan jika pembayaran gagal/kedaluwarsa, Tahap 8)
                    $product->decrement('stock', $cartItem->quantity);
                }

                $user->cartItems()->delete();

                return $order;
            });
        } catch (RuntimeException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat. Silakan lanjutkan pembayaran.');
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'KL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
