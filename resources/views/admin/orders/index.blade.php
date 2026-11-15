@extends('layouts.app')
@section('title', 'Pesanan')

@section('content')
<h3 class="fw-bold mb-3">Pesanan</h3>

<form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari nomor pesanan / nama penerima">
    </div>
    <div class="col-md-3">
        <select name="payment" class="form-select">
            <option value="">Semua status bayar</option>
            @foreach(['pending' => 'Menunggu Pembayaran', 'paid' => 'Sudah Dibayar', 'expired' => 'Kedaluwarsa', 'failed' => 'Gagal'] as $v => $l)
                <option value="{{ $v }}" @selected(request('payment') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Semua status pesanan</option>
            @foreach(['pending' => 'Menunggu', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $v => $l)
                <option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button class="btn btn-dark">Filter</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr><th>No. Pesanan</th><th>Pelanggan</th><th>Tanggal</th><th>Total</th><th>Status</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="fw-semibold">{{ $order->order_number }}</td>
                    <td>
                        {{ $order->user->name }}
                        <div class="small text-muted">{{ $order->recipient_name }}</div>
                    </td>
                    <td class="small">{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td><div class="d-flex flex-column gap-1 align-items-start">@include('orders._status', ['order' => $order])</div></td>
                    <td class="text-end">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
@endsection
