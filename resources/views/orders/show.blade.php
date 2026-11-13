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

        <div class="kl-product" style="border-radius:24px;">
            <h5 class="fw-bold mb-3">Pembayaran</h5>

            @if($order->payment_status === 'pending')
                <p class="text-muted small mb-3">
                    Selesaikan pembayaran melalui Midtrans. Setelah membayar, status akan diperbarui otomatis.
                </p>
                <button id="pay-button" class="btn btn-dark rounded-pill py-2 w-100">Bayar Sekarang</button>
                <form method="POST" action="{{ route('payments.check', $order) }}" class="mt-2">
                    @csrf
                    <button class="btn btn-outline-dark rounded-pill py-2 w-100">Cek Status Pembayaran</button>
                </form>
            @elseif($order->payment_status === 'paid')
                <div class="alert alert-success mb-2">Pembayaran berhasil diterima.</div>
                <div class="small text-muted">
                    Metode: {{ $order->payment_type ? strtoupper(str_replace('_', ' ', $order->payment_type)) : '-' }}<br>
                    Dibayar: {{ $order->paid_at?->format('d M Y, H:i') ?? '-' }}
                </div>
            @elseif($order->payment_status === 'expired')
                <div class="alert alert-secondary mb-0">Waktu pembayaran habis. Pesanan dibatalkan dan stok dikembalikan.</div>
            @else
                <div class="alert alert-danger mb-0">Pembayaran gagal. Pesanan dibatalkan dan stok dikembalikan.</div>
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
