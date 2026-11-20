@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
@php
    $hour = (int) now()->format('H');
    $greet = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
    $groups = [
        'Katalog' => [
            ['Total Produk', $stats['products'], 'box', null, route('admin.products.index')],
            ['Total Kategori', $stats['categories'], 'layers', null, route('admin.categories.index')],
        ],
        'Penjualan' => [
            ['Total Pesanan', $stats['orders'], 'receipt', null, route('admin.orders.index')],
            ['Sudah Dibayar', $stats['paid'], 'check', null, route('admin.orders.index', ['payment' => 'paid'])],
            ['Menunggu Pembayaran', $stats['pending'], 'clock', 'is-warn', route('admin.orders.index', ['payment' => 'pending'])],
        ],
        'Pelanggan' => [
            ['Total Pelanggan', $stats['customers'], 'users', null, null],
            ['Reservasi Menunggu', $stats['reservations'], 'calendar', 'is-warn', route('admin.reservations.index', ['status' => 'pending'])],
        ],
    ];
@endphp

<section class="ad-hello">
    <div>
        <h1>{{ $greet }}, {{ \Illuminate\Support\Str::of(auth()->user()->name)->before(' ') }}!</h1>
        <p>{{ now()->translatedFormat('l, d F Y') }} &middot; ringkasan toko KopiLihdia hari ini.</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-light"><x-icon name="plus" :size="18" /> Tambah produk</a>
</section>

<div class="ad-sec">Perlu perhatian</div>
<div class="row g-3">
    <div class="col-lg-6">
        @if($stats['pending'] > 0)
            <a href="{{ route('admin.orders.index', ['payment' => 'pending']) }}" class="ad-todo text-decoration-none">
                <x-icon name="clock" :size="22" /> {{ $stats['pending'] }} pesanan menunggu pembayaran
                <span class="btn btn-sm btn-outline-secondary bg-white">Lihat</span>
            </a>
        @else
            <div class="ad-todo is-ok"><x-icon name="check" :size="22" /> Tidak ada pesanan yang menunggu pembayaran</div>
        @endif
    </div>
    <div class="col-lg-6">
        @if($stats['reservations'] > 0)
            <a href="{{ route('admin.reservations.index', ['status' => 'pending']) }}" class="ad-todo text-decoration-none">
                <x-icon name="calendar" :size="22" /> {{ $stats['reservations'] }} reservasi menunggu konfirmasi
                <span class="btn btn-sm btn-outline-secondary bg-white">Konfirmasi</span>
            </a>
        @else
            <div class="ad-todo is-ok"><x-icon name="check" :size="22" /> Semua reservasi sudah dikonfirmasi</div>
        @endif
    </div>
</div>

@foreach($groups as $title => $cards)
    <div class="ad-sec">{{ $title }}</div>
    <div class="ad-stats">
        @foreach($cards as [$label, $value, $icon, $mod, $href])
            @if($href)<a href="{{ $href }}" class="ad-stat {{ $mod }}">@else<div class="ad-stat {{ $mod }}">@endif
                <span class="ad-stat-ic"><x-icon :name="$icon" :size="24" /></span>
                <div>
                    <div class="ad-stat-n">{{ $value }}</div>
                    <div class="ad-stat-l">{{ $label }}</div>
                </div>
                @if($href)<span class="ad-go"><x-icon name="chevron-right" :size="18" /></span>@endif
            @if($href)</a>@else</div>@endif
        @endforeach
    </div>
@endforeach

<div class="ad-sec">Pintasan</div>
<div class="ad-quick">
    <a href="{{ route('admin.products.create') }}"><x-icon name="plus" :size="20" /> Tambah produk</a>
    <a href="{{ route('admin.categories.create') }}"><x-icon name="plus" :size="20" /> Tambah kategori</a>
    <a href="{{ route('admin.orders.index') }}"><x-icon name="receipt" :size="20" /> Kelola pesanan</a>
    <a href="{{ route('admin.reservations.index') }}"><x-icon name="calendar" :size="20" /> Kelola reservasi</a>
</div>
@endsection
