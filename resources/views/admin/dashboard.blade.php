@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<h3 class="mb-4">Dashboard Admin</h3>
<div class="row g-3">
    @php
        $cards = [
            ['Total Produk', $stats['products'], 'primary'],
            ['Total Kategori', $stats['categories'], 'secondary'],
            ['Total Pelanggan', $stats['customers'], 'info'],
            ['Total Pesanan', $stats['orders'], 'dark'],
            ['Menunggu Pembayaran', $stats['pending'], 'warning'],
            ['Sudah Dibayar', $stats['paid'], 'success'],
        ];
    @endphp
    @foreach($cards as [$label, $value, $color])
        <div class="col-6 col-md-4">
            <div class="card border-{{ $color }} shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="fs-2 fw-bold">{{ $value }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection