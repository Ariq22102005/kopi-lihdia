<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products'   => Product::count(),
            'categories' => Category::count(),
            'customers'  => User::where('role', 'customer')->count(),
            'orders'     => Order::count(),
            'pending'    => Order::where('payment_status', Order::PAYMENT_PENDING)->count(),
            'paid'       => Order::where('payment_status', Order::PAYMENT_PAID)->count(),
            'reservations' => Reservation::where('status', Reservation::STATUS_PENDING)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}