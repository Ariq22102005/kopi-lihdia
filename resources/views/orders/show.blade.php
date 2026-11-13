@extends('layouts.app')
@section('title', 'Pesanan ' . $order->order_number)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-1">Pesanan {{ $order->order_number }}</h3>
        <div class="text-muted small">{{ $order->created_at->format('d M Y, H:i') }}</div>
    </div>
    <div class="d-flex gap-1 flex-wrap">@include('orders._status', ['order' => $order])</div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="kl-product" style="border-radius:24px;">
            <h5 class="fw-bold mb-3">Item Pesanan</h5>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Menu</th><th class="text-end">Harga</th><th class="text-center">Jml</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <hr>
            <div class="d-flex justify-content-between"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between"><span>Ongkos kirim</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between fs-5 fw-bold mt-2">
                <span>Total</span>
                <span style="color: var(--kl-green)">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="kl-product mb-3" style="border-radius:24px;">
            <h5 class="fw-bold mb-3">Pengiriman</h5>
            <div class="fw-semibold">{{ $order->recipient_name }}</div>
            <div>{{ $order->recipient_phone }}</div>
            <div class="mt-2">{{ $order->shipping_address }}</div>
            <div>{{ $order->city }} {{ $order->postal_code }}</div>
            @if($order->notes)
                <div class="mt-2 text-muted small">Catatan: {{ $order->notes }}</div>
            @endif
        </div>

        @if($order->payment_status === 'pending')
            {{-- Tahap 8: diganti tombol bayar Midtrans --}}
            <button class="btn btn-dark rounded-pill py-2 w-100" disabled>Bayar (aktif di Tahap 8)</button>
        @endif
    </div>
</div>
@endsection
