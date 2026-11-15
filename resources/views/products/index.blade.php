@extends('layouts.app')
@section('title', 'Menu')

@section('content')
@php
    $icons = ['kopi susu' => '🧋', 'kopi klasik' => '☕', 'non-kopi' => '🍵', 'makanan' => '🥐'];
@endphp

<div class="row g-4">
    {{-- Kolom katalog --}}
    <div class="col-lg-8 col-xl-9">

        {{-- Banner (disembunyikan saat mencari atau memfilter supaya hasil langsung terlihat) --}}
        @if($search === '' && ! $activeCategory)
            <div class="kl-hero mb-4">
                <div class="kl-hero-text">
                    <h2>Seduh semangatmu bersama {{ config('app.name') }}</h2>
                    <p>Kopi susu, kopi klasik, non-kopi, dan camilan. Pilih menunya, bayar mudah lewat Midtrans.</p>
                    <a href="#menu" class="btn btn-light rounded-pill px-4 mt-1">Lihat menu</a>
                </div>
                <div class="kl-hero-img d-none d-sm-block" style="background-image: url('{{ asset('img/hero.jpg') }}')"></div>
            </div>
        @endif

        {{-- Pencarian --}}
        <form method="GET" action="{{ route('products.index') }}" class="mb-4">
            @if($activeCategory)
                <input type="hidden" name="kategori" value="{{ $activeCategory }}">
            @endif
            <div class="kl-search">
                <span class="kl-search-icon">🔍</span>
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari menu favoritmu..."
                       class="form-control border-0 shadow-none bg-transparent" autocomplete="off">
                @if($search !== '')
                    <a href="{{ route('products.index', array_filter(['kategori' => $activeCategory])) }}"
                       class="btn btn-sm btn-link text-muted text-decoration-none">Hapus</a>
                @endif
                <button class="btn btn-dark rounded-pill px-4">Cari</button>
            </div>
        </form>

        {{-- Filter kategori --}}
        <div id="menu" class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
            <div class="col">
                <a href="{{ route('products.index', array_filter(['q' => $search])) }}"
                   class="kl-cat @if(! $activeCategory) active @endif">
                    <span class="kl-tag @if($totalAvailable === 0) kl-tag-warn @endif">
                        {{ $totalAvailable === 0 ? 'Stok habis' : 'Tersedia' }}
                    </span>
                    <div>
                        <div class="kl-cat-name">Semua Menu</div>
                        <div class="kl-cat-count">{{ $totalActive }} item</div>
                    </div>
                    <span class="kl-cat-icon">☕</span>
                </a>
            </div>
            @foreach($categories as $c)
                <div class="col">
                    <a href="{{ route('products.index', array_filter(['kategori' => $c->slug, 'q' => $search])) }}"
                       class="kl-cat @if($activeCategory === $c->slug) active @endif">
                        <span class="kl-tag @if($c->available_count === 0) kl-tag-warn @endif">
                            {{ $c->available_count === 0 ? 'Stok habis' : 'Tersedia' }}
                        </span>
                        <div>
                            <div class="kl-cat-name">{{ $c->name }}</div>
                            <div class="kl-cat-count">{{ $c->products_count }} item</div>
                        </div>
                        <span class="kl-cat-icon">{{ $icons[\Illuminate\Support\Str::lower($c->name)] ?? '☕' }}</span>
                    </a>
                </div>
            @endforeach
        </div>

        @if($search !== '')
            <p class="text-muted">Hasil untuk "<strong>{{ $search }}</strong>" ({{ $products->total() }} menu)</p>
        @endif

        {{-- Daftar produk --}}
        <div class="row g-3">
            @forelse($products as $product)
                <div class="col-6 col-md-4 col-xxl-3">
                    @include('products._card', ['product' => $product, 'cartQty' => $cartQty])
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center text-muted py-5">
                        <div style="font-size:3rem">🔎</div>
                        Menu tidak ditemukan.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $products->links('pagination::bootstrap-5') }}</div>
    </div>

    {{-- Panel pesanan (desktop). Di HP diganti bar keranjang di bagian bawah layar. --}}
    <div class="col-lg-4 col-xl-3 d-none d-lg-block">
        <div class="kl-sidebar">
            <div class="kl-panel" id="mini-cart">
                @include('cart._mini', ['items' => $cartItems, 'subtotal' => $subtotal])
            </div>
        </div>
    </div>
</div>
@endsection
