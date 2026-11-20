@extends('layouts.app')
@section('title', 'Pesanan ' . $order->order_number)

@section('content')
@php
    $closed = in_array($order->payment_status, ['expired', 'failed'], true) || $order->order_status === 'cancelled';
    $stage = 0;
    if ($order->payment_status === 'paid') {
        $stage = ['processing' => 2, 'shipped' => 3, 'completed' => 4][$order->order_status] ?? 1;
    }
    $track = [['Dibuat', 'receipt'], ['Dibayar', 'wallet'], ['Diproses', 'cup'], ['Dikirim', 'truck'], ['Selesai', 'check']];
@endphp

<a href="{{ route('orders.index') }}" class="kl-back"><x-icon name="chevron-left" :size="18" /> Riwayat pesanan</a>
<div class="kl-pagehead">
    <div>
        <h1>Pesanan {{ $order->order_number }}</h1>
        <p>{{ $order->created_at->format('d M Y, H:i') }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">@include('orders._status', ['order' => $order])</div>
</div>

@unless($closed)
    <div class="kl-card mb-4" aria-label="Progres pesanan">
        <div class="kl-track">
            @foreach($track as $i => [$label, $icon])
                <div class="kl-track-item @if($i < $stage || ($stage === 4 && $i === 4)) done @elseif($i === $stage) now @endif">
                    <span class="kl-track-dot"><x-icon :name="$icon" :size="16" /></span>
                    <div>{{ $label }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endunless

<div class="row g-4">
    <div class="col-lg-7">
        <div class="kl-card">
            <h2 class="kl-card-title"><x-icon name="cup" :size="20" /> Item Pesanan</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Menu</th><th class="text-end">Harga</th><th class="text-center">Jml</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->product_name }}</td>
                            <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <div class="kl-summary-row"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                <div class="kl-summary-row"><span>Ongkos kirim</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                <div class="kl-summary-total"><span>Total</span><span>Rp {{ number_format($order->total, 0, ',', '.') }}</span></div>
            </div>
            <a href="{{ route('orders.invoice', $order) }}" target="_blank" rel="noopener" class="btn btn-outline-dark mt-3"><x-icon name="receipt" :size="18" /> Lihat invoice</a>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="kl-card mb-3">
            <h2 class="kl-card-title"><x-icon name="pin" :size="20" /> Pengiriman</h2>
            <div class="fw-semibold">{{ $order->recipient_name }}</div>
            <div>{{ $order->recipient_phone }}</div>
            <div class="mt-2">{{ $order->shipping_address }}</div>
            <div>{{ $order->city }} {{ $order->postal_code }}</div>
            @if($order->notes)
                <div class="mt-2 text-muted small">Catatan: {{ $order->notes }}</div>
            @endif
        </div>

        <div class="kl-card">
            <h2 class="kl-card-title"><x-icon name="wallet" :size="20" /> Pembayaran</h2>

            @if($order->payment_status === 'pending')
                <p class="text-muted small mb-3">
                    Selesaikan pembayaran melalui Midtrans. Setelah membayar, status akan diperbarui otomatis.
                </p>
                <button id="pay-button" class="btn btn-dark btn-lg w-100"><x-icon name="card" :size="20" /> Bayar sekarang</button>
                <form method="POST" action="{{ route('payments.check', $order) }}" class="mt-2">
                    @csrf
                    <button class="btn btn-outline-dark w-100">Cek status pembayaran</button>
                </form>
            @elseif($order->payment_status === 'paid')
                <div class="alert alert-success mb-2"><x-icon name="check" :size="20" /><div>Pembayaran berhasil diterima.</div></div>
                <div class="small text-muted">
                    Metode: {{ $order->payment_type ? strtoupper(str_replace('_', ' ', $order->payment_type)) : '-' }}<br>
                    Dibayar: {{ $order->paid_at?->format('d M Y, H:i') ?? '-' }}
                </div>
            @elseif($order->payment_status === 'expired')
                <div class="alert alert-secondary mb-0"><x-icon name="clock" :size="20" /><div>Waktu pembayaran habis. Pesanan dibatalkan dan stok dikembalikan.</div></div>
            @else
                <div class="alert alert-danger mb-0"><x-icon name="alert" :size="20" /><div>Pembayaran gagal. Pesanan dibatalkan dan stok dikembalikan.</div></div>
            @endif
        </div>
    </div>
</div>
@endsection

@if($order->payment_status === 'pending')
@push('scripts')
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const tokenUrl = @json(route('payments.token', $order));
    const checkUrl = @json(route('payments.check', $order));
    const btn = document.getElementById('pay-button');

    const post = (url) => fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    });
    const checkAndReload = () => post(checkUrl).finally(() => window.location.reload());

    btn.addEventListener('click', async () => {
        btn.disabled = true;
        try {
            const res = await post(tokenUrl);
            const data = await res.json();
            if (!res.ok) {
                alert(data.message || 'Gagal memulai pembayaran.');
                btn.disabled = false;
                return;
            }
            window.snap.pay(data.token, {
                onSuccess: checkAndReload,
                onPending: checkAndReload,
                onError: checkAndReload,
                onClose: () => { btn.disabled = false; }
            });
        } catch (e) {
            alert('Terjadi kesalahan jaringan.');
            btn.disabled = false;
        }
    });
})();
</script>
@endpush
@endif
