@extends('layouts.admin')
@section('title', 'Pesanan ' . $order->order_number)

@section('content')
<a href="{{ route('admin.orders.index') }}" class="kl-back"><x-icon name="chevron-left" :size="18" /> Semua pesanan</a>

<div class="kl-pagehead">
    <div>
        <h1>{{ $order->order_number }}</h1>
        <p>{{ $order->created_at->format('d M Y, H:i') }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">@include('orders._status', ['order' => $order])</div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="ad-card ad-card-pad mb-3">
            <h2 class="ad-card-title"><x-icon name="receipt" :size="18" /> Item Pesanan</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Menu</th><th class="text-end">Harga</th><th class="text-center">Jml</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->product_name }}</td>
                            <td class="text-end text-nowrap">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end text-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="kl-summary-row mt-3"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
            <div class="kl-summary-row"><span>Ongkos kirim</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
            <div class="kl-summary-total"><span>Total pembayaran</span><span>Rp {{ number_format($order->total, 0, ',', '.') }}</span></div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="ad-card ad-card-pad mb-3">
            <h2 class="ad-card-title"><x-icon name="user" :size="18" /> Pelanggan</h2>
            <div class="fw-semibold">{{ $order->user->name }}</div>
            <div class="small text-muted">{{ $order->user->email }}</div>
            <hr>
            <div class="small text-muted">Dikirim ke</div>
            <div class="fw-semibold">{{ $order->recipient_name }}</div>
            <div>{{ $order->recipient_phone }}</div>
            <div class="mt-1">{{ $order->shipping_address }}</div>
            <div>{{ $order->city }} {{ $order->postal_code }}</div>
            @if($order->notes)
                <div class="mt-2 small text-muted">Catatan: {{ $order->notes }}</div>
            @endif
        </div>

        <div class="ad-card ad-card-pad mb-3">
            <h2 class="ad-card-title"><x-icon name="shield" :size="18" /> Pembayaran</h2>
            <div class="mb-2">@include('orders._status', ['order' => $order])</div>
            <div class="ad-kv small"><span class="text-muted">Metode</span><span>{{ $order->payment_type ? strtoupper(str_replace('_', ' ', $order->payment_type)) : '-' }}</span></div>
            <div class="ad-kv small"><span class="text-muted">Dibayar</span><span>{{ $order->paid_at?->format('d M Y, H:i') ?? '-' }}</span></div>
            <div class="alert alert-light border small mt-3 mb-0">
                Status pembayaran hanya diperbarui otomatis berdasarkan verifikasi Midtrans dan tidak dapat diubah dari dashboard.
            </div>
        </div>

        <div class="ad-card ad-card-pad">
            <h2 class="ad-card-title"><x-icon name="clock" :size="18" /> Status Pemrosesan</h2>

            @if($canUpdate)
                <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf @method('PATCH')
                    @php
                        $labels = ['processing' => 'Diproses', 'shipped' => 'Dikirim', 'completed' => 'Selesai'];
                    @endphp
                    <label class="form-label" for="o-status">Ubah menjadi</label>
                    <select id="o-status" name="order_status" class="form-select mb-3 @error('order_status') is-invalid @enderror">
                        @foreach($nextOptions as $opt)
                            <option value="{{ $opt }}">{{ $labels[$opt] ?? $opt }}</option>
                        @endforeach
                    </select>
                    @error('order_status')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                    <button class="btn btn-dark w-100">Perbarui Status</button>
                </form>
            @elseif($order->payment_status !== 'paid')
                <div class="text-muted small">Menunggu pembayaran terverifikasi sebelum pesanan dapat diproses.</div>
            @else
                <div class="text-muted small">Status pesanan sudah final.</div>
            @endif
        </div>
    </div>
</div>
@endsection
