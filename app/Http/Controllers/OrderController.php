<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Request $request, Order $order)
    {
        // Customer hanya boleh melihat pesanan miliknya sendiri
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items');

        return view('orders.show', compact('order'));
    }
}
