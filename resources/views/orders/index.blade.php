@extends('layouts.app')
@section('title', 'Pesanan Saya')

@section('content')
<h3 class="fw-bold mb-4">Pesanan Saya</h3>

@if($orders->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem">🧾</div>
        <p class="text-muted mt-2">Belum ada pesanan.</p>
        <a href="{{ route('products.index') }}" class="btn btn-dark rounded-pill px-4">Lihat Menu</a>
    </div>
@else
    @foreach($orders as $order)
        <a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-reset">
            <div class="kl-product mb-3" style="border-radius:24px;">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <div class="fw-bold">{{ $order->order_number }}</div>
                        <div class="text-muted small">
                            {{ $order->created_at->format('d M Y, H:i') }} &middot; {{ $order->items_count }} jenis menu
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold" style="color: var(--kl-green)">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-1 flex-wrap mt-2">@include('orders._status', ['order' => $order])</div>
            </div>
        </a>
    @endforeach

    <div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
@endif
@endsection
