@extends('layouts.app')
@section('title', 'Pesanan ' . $order->order_number)

@section('content')
<a href="{{ route('admin.orders.index') }}" class="text-decoration-none">&larr; Semua pesanan</a>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4 mt-2">
    <div>
        <h3 class="fw-bold mb-1">{{ $order->order_number }}</h3>
        <div class="text-muted small">{{ $order->created_at->format('d M Y, H:i') }}</div>
    </div>
    <div class="d-flex gap-1 flex-wrap">@include('orders._status', ['order' => $order])</div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm mb-3"><div class="card-body">
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
                <span>Total pembayaran</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div></div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm mb-3"><div class="card-body">
            <h5 class="fw-bold mb-3">Pelanggan</h5>
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
        </div></div>

        <div class="card shadow-sm mb-3"><div class="card-body">
            <h5 class="fw-bold mb-3">Pembayaran</h5>
            <div class="mb-2">Status: @include('orders._status', ['order' => $order])</div>
            <div class="small text-muted">
                Metode: {{ $order->payment_type ? strtoupper(str_replace('_', ' ', $order->payment_type)) : '-' }}<br>
                Dibayar: {{ $order->paid_at?->format('d M Y, H:i') ?? '-' }}
            </div>
            <div class="alert alert-light border small mt-3 mb-0">
                Status pembayaran hanya diperbarui otomatis berdasarkan verifikasi Midtrans dan tidak dapat diubah dari dashboard.
            </div>
        </div></div>

        <div class="card shadow-sm"><div class="card-body">
            <h5 class="fw-bold mb-3">Status Pemrosesan</h5>

            @if($canUpdate)
                <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf @method('PATCH')
                    @php
                        $labels = ['processing' => 'Diproses', 'shipped' => 'Dikirim', 'completed' => 'Selesai'];
                    @endphp
                    <label class="form-label">Ubah menjadi</label>
                    <select name="order_status" class="form-select mb-3 @error('order_status') is-invalid @enderror">
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
        </div></div>
    </div>
</div>
@endsection
