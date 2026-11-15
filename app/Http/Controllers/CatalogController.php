<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $activeCategory = $request->query('kategori');

        $products = Product::with('category')
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($activeCategory, function ($query) use ($activeCategory) {
                $query->whereHas('category', fn ($c) => $c->where('slug', $activeCategory));
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::withCount([
            'products' => fn ($q) => $q->where('is_active', true),
            'products as available_count' => fn ($q) => $q->where('is_active', true)->where('stock', '>', 0),
        ])->orderBy('name')->get();

        $totalActive = Product::where('is_active', true)->count();
        $totalAvailable = Product::where('is_active', true)->where('stock', '>', 0)->count();

        [$cartItems, $cartQty, $subtotal] = $this->cartData($request);

        return view('products.index', compact(
            'products', 'categories', 'totalActive', 'totalAvailable',
            'search', 'activeCategory', 'cartItems', 'cartQty', 'subtotal'
        ));
    }

    public function show(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load('category');

        $related = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        [, $cartQty] = $this->cartData($request);

        return view('products.show', compact('product', 'related', 'cartQty'));
    }

    /**
     * Isi keranjang untuk panel pesanan dan penanda jumlah di kartu produk.
     * Hanya untuk customer yang sedang login.
     */
    private function cartData(Request $request): array
    {
        $user = $request->user();

        if (! $user || $user->isAdmin()) {
            return [collect(), [], 0];
        }

        $items = $user->cartItems()->with('product')->get();
        $qty = $items->pluck('quantity', 'product_id')->all();
        $subtotal = $items->sum(fn ($i) => $i->product->price * $i->quantity);

        return [$items, $qty, $subtotal];
    }
}
