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

        $categories = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $totalActive = Product::where('is_active', true)->count();

        return view('products.index', compact(
            'products', 'categories', 'totalActive', 'search', 'activeCategory'
        ));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load('category');

        $related = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'related'));
    }
}
