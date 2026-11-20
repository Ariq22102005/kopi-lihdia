<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Menu pilihan: produk aktif yang stoknya masih ada
        $products = Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->take(24)
            ->get();

        $categories = $products->pluck('category')->filter()->unique('id')->values();

        // Penanda jumlah di kartu produk (hanya customer yang login)
        $user = $request->user();
        $cartQty = ($user && ! $user->isAdmin())
            ? $user->cartItems()->pluck('quantity', 'product_id')->all()
            : [];

        // Data form reservasi
        $rsvCfg = config('kopilihdia.reservation');
        $now = now($rsvCfg['timezone']);
        $slots = Reservation::slots();
        $minDate = $now->toDateString();
        $maxDate = $now->copy()->addDays((int) $rsvCfg['days_ahead'])->toDateString();

        return view('home.index', compact(
            'products', 'categories', 'cartQty', 'rsvCfg', 'slots', 'minDate', 'maxDate'
        ));
    }
}
