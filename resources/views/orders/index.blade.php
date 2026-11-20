@extends('layouts.app')
@section('title', 'Pesanan Saya')

@section('content')
<div class="kl-pagehead">
    <div>
        <h1>Pesanan Saya</h1>
        <p>Pantau pembayaran dan pengiriman pesananmu.</p>
    </div>
</div>

@if($orders->isEmpty())
    <div class="kl-empty py-5">
        <span class="kl-empty-ic"><x-icon name="receipt" :size="34" /></span>
        <h5>Belum ada pesanan</h5>
        <p class="mb-3">Pesanan yang kamu buat akan muncul di sini.</p>
        <a href="{{ route('products.index') }}" class="btn btn-dark px-4">Lihat menu</a>
    </div>
@else
    @foreach($orders as $order)
        <a href="{{ route('orders.show', $order) }}" class="kl-order">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                <div>
                    <div class="fw-bold">{{ $order->order_number }}</div>
                    <div class="text-muted small">
                        {{ $order->created_at->format('d M Y, H:i') }} &middot; {{ $order->items_count }} jenis menu
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold" style="color: var(--kl-green)">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    <x-icon name="chevron-right" :size="18" class="text-muted" />
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap mt-3">@include('orders._status', ['order' => $order])</div>
        </a>
    @endforeach

    <div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
@endif
@endsection
