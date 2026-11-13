<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
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
        ];

        return view('admin.dashboard', compact('stats'));
    }
}