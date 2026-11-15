<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()
            ->cartItems()
            ->with('product.category')
            ->get();

        // Total selalu dihitung di server dari harga di database
        $total = $items->sum(fn ($item) => $item->product->price * $item->quantity);

        return view('cart.index', compact('items', 'total'));
    }

    /**
     * Data panel pesanan (HTML + jumlah) untuk pembaruan tanpa reload halaman.
     */
    public function mini(Request $request)
    {
        $items = $request->user()->cartItems()->with('product')->get();
        $subtotal = $items->sum(fn ($i) => $i->product->price * $i->quantity);

        return response()->json([
            'html'           => view('cart._mini', compact('items', 'subtotal'))->render(),
            'count'          => (int) $items->sum('quantity'),
            'subtotal_label' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'quantities'     => (object) $items->pluck('quantity', 'product_id')->all(),
        ]);
    }

    public function store(Request $request, Product $product)
    {
        abort_if($request->user()->isAdmin(), 403, 'Admin tidak dapat berbelanja.');
        abort_unless($product->is_active, 404);

        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);
        $quantity = $validated['quantity'] ?? 1;

        if ($product->stock <= 0) {
            return $this->respond($request, false, "{$product->name} sedang habis.");
        }

        $item = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        $newQuantity = ($item?->quantity ?? 0) + $quantity;

        // Cegah pembelian melebihi stok
        if ($newQuantity > $product->stock) {
            return $this->respond($request, false, "Stok {$product->name} hanya tersisa {$product->stock}.");
        }

        CartItem::updateOrCreate(
            ['user_id' => $request->user()->id, 'product_id' => $product->id],
            ['quantity' => $newQuantity]
        );

        return $this->respond($request, true, "{$product->name} ditambahkan ke keranjang.");
    }

    public function update(Request $request, CartItem $cartItem)
    {
        // Hanya pemilik keranjang yang boleh mengubah
        abort_unless($cartItem->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $product = $cartItem->product;

        if ($validated['quantity'] > $product->stock) {
            return $this->respond($request, false, "Stok {$product->name} hanya tersisa {$product->stock}.");
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return $this->respond($request, true, '');
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === $request->user()->id, 403);

        $cartItem->delete();

        return $this->respond($request, true, 'Produk dihapus dari keranjang.');
    }

    /**
     * Jawaban JSON untuk permintaan AJAX, atau redirect biasa untuk form tanpa JavaScript.
     */
    private function respond(Request $request, bool $ok, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => $ok, 'message' => $message], $ok ? 200 : 422);
        }

        if ($message === '') {
            return back();
        }

        return back()->with($ok ? 'success' : 'error', $message);
    }
}
