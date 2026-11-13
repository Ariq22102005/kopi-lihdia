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

    public function store(Request $request, Product $product)
    {
        abort_if($request->user()->isAdmin(), 403, 'Admin tidak dapat berbelanja.');
        abort_unless($product->is_active, 404);

        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);
        $quantity = $validated['quantity'] ?? 1;

        if ($product->stock <= 0) {
            return back()->with('error', "{$product->name} sedang habis.");
        }

        $item = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        $newQuantity = ($item?->quantity ?? 0) + $quantity;

        // Cegah pembelian melebihi stok
        if ($newQuantity > $product->stock) {
            return back()->with('error', "Stok {$product->name} hanya tersisa {$product->stock}.");
        }

        CartItem::updateOrCreate(
            ['user_id' => $request->user()->id, 'product_id' => $product->id],
            ['quantity' => $newQuantity]
        );

        return back()->with('success', "{$product->name} ditambahkan ke keranjang.");
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
            return back()->with('error', "Stok {$product->name} hanya tersisa {$product->stock}.");
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return back();
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === $request->user()->id, 403);

        $cartItem->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
