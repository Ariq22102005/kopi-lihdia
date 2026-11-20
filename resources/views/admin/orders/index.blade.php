@extends('layouts.admin')
@section('title', 'Pesanan')

@section('content')
<div class="kl-pagehead"><div><h1>Pesanan</h1><p>Pantau pembayaran dan proses pesanan pelanggan.</p></div></div>

<form method="GET" action="{{ route('admin.orders.index') }}" class="ad-card ad-toolbar">
    <input type="search" name="q" value="{{ $search }}" class="form-control ad-grow" placeholder="Cari nomor pesanan atau nama penerima" aria-label="Cari pesanan">
    <select name="payment" aria-label="Status bayar" class="form-select">
        <option value="">Semua status bayar</option>
        @foreach(['pending' => 'Menunggu Pembayaran', 'paid' => 'Sudah Dibayar', 'expired' => 'Kedaluwarsa', 'failed' => 'Gagal'] as $v => $l)
            <option value="{{ $v }}" @selected(request('payment') === $v)>{{ $l }}</option>
        @endforeach
    </select>
    <select name="status" class="form-select" aria-label="Status pesanan">
        <option value="">Semua status pesanan</option>
        @foreach(['pending' => 'Menunggu', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $v => $l)
            <option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>
        @endforeach
    </select>
    <button class="btn btn-dark"><x-icon name="search" :size="16" /> Filter</button>
</form>

<div class="ad-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr><th>No. Pesanan</th><th>Pelanggan</th><th>Tanggal</th><th>Total</th><th>Status</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="fw-semibold text-nowrap">{{ $order->order_number }}</td>
                    <td>
                        {{ $order->user->name }}
                        <div class="small text-muted">{{ $order->recipient_name }}</div>
                    </td>
                    <td class="small text-nowrap">{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td class="text-nowrap fw-semibold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td><div class="d-flex flex-column gap-1 align-items-start">@include('orders._status', ['order' => $order])</div></td>
                    <td class="text-end">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">Detail <x-icon name="chevron-right" :size="14" /></a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">Belum ada pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
@endsection
