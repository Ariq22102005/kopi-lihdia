@extends('layouts.app')
@section('title', 'Menu')

@section('content')
@php
    $icons = ['kopi susu' => 'cup', 'kopi klasik' => 'bean', 'non-kopi' => 'leaf', 'makanan' => 'cookie'];
    $firstName = auth()->check() ? \Illuminate\Support\Str::before(auth()->user()->name, ' ') : '';
    $suggest = $products->pluck('name')->take(8)->values();
@endphp

<div class="row g-4">
    {{-- Kolom katalog --}}
    <div class="col-lg-8 col-xl-9">

        {{-- Banner sapaan (disembunyikan saat mencari atau memfilter supaya hasil langsung terlihat) --}}
        @if($search === '' && ! $activeCategory)
            <section class="kl-hero kl-hero-greet mb-4" aria-label="Sapaan">
                <div class="kl-hero-text">
                    <span class="kl-eyebrow kl-eyebrow-light" data-greet data-name="{{ $firstName }}"><x-icon name="leaf" :size="16" /> <span>Halo{{ $firstName ? ', '.$firstName : '' }}</span></span>
                    <h1>Mau ngopi apa<br><span class="kl-swap" data-swap data-words="{{ json_encode(['hari ini?', 'bareng teman?', 'sambil kerja?', 'pas hujan?', 'buat semangat?']) }}">hari ini?</span></h1>
                    <p>Pilih yang cocok sama mood-mu. Kami siapin, kamu tinggal santai.</p>
                    <div class="kl-hero-actions">
                        <a href="#menu" class="btn btn-light btn-lg">Pesan sekarang</a>
                        <span class="kl-hero-note"><x-icon name="shield" :size="18" /> Pembayaran aman</span>
                    </div>
                </div>
                <div class="kl-hero-img" style="background-image: url('{{ asset('img/hero.jpg') }}')" role="img" aria-label="Kemasan dan topi KopiLihdia"></div>
            </section>
        @endif

        {{-- Pencarian --}}
        <form method="GET" action="{{ route('products.index') }}" class="mb-3" role="search">
            @if($activeCategory)
                <input type="hidden" name="kategori" value="{{ $activeCategory }}">
            @endif
            <div class="kl-search">
                <x-icon name="search" :size="20" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Cari menu favoritmu"
                       data-typing="{{ json_encode($suggest) }}"
                       aria-label="Cari menu" class="form-control border-0 shadow-none bg-transparent" autocomplete="off">
                @if($search !== '')
                    <a href="{{ route('products.index', array_filter(['kategori' => $activeCategory])) }}"
                       class="btn btn-sm btn-link text-muted">Hapus</a>
                @endif
                <button class="btn btn-dark">Cari</button>
            </div>
        </form>

        {{-- Filter kategori --}}
        <nav id="menu" class="kl-chips mb-3" aria-label="Kategori menu">
            <a href="{{ route('products.index', array_filter(['q' => $search])) }}"
               class="kl-chip @if(! $activeCategory) active @endif @if($totalAvailable === 0) is-empty @endif"
               @if(! $activeCategory) aria-current="page" @endif>
                <x-icon name="grid" :size="18" /> Semua menu <small>{{ $totalActive }}</small>
            </a>
            @foreach($categories as $c)
                <a href="{{ route('products.index', array_filter(['kategori' => $c->slug, 'q' => $search])) }}"
                   class="kl-chip @if($activeCategory === $c->slug) active @endif @if($c->available_count === 0) is-empty @endif"
                   @if($activeCategory === $c->slug) aria-current="page" @endif
                   @if($c->available_count === 0) title="Lagi habis semua" @endif>
                    <x-icon :name="$icons[\Illuminate\Support\Str::lower($c->name)] ?? 'cup'" :size="18" />
                    {{ $c->name }} <small>{{ $c->products_count }}</small>
                </a>
            @endforeach
            <button type="button" class="kl-chip kl-chip-fav" data-fav-filter aria-pressed="false">
                <x-icon name="heart" :size="18" /> Favorit <small data-fav-count>0</small>
            </button>
        </nav>

        @if($search !== '')
            <p class="text-muted mb-3">Ketemu {{ $products->total() }} menu buat "<strong class="text-body">{{ $search }}</strong>"</p>
        @endif

        {{-- Daftar produk --}}
        <div class="row g-3" id="product-grid">
            @forelse($products as $product)
                <div class="col-6 col-md-4 col-xxl-3" data-reveal style="--d: {{ $loop->index % 4 }}">
                    @include('products._card', ['product' => $product, 'cartQty' => $cartQty])
                </div>
            @empty
                <div class="col-12">
                    <div class="kl-empty">
                        <span class="kl-empty-ic"><x-icon name="search" :size="32" /></span>
                        <h5>Yah, belum ketemu</h5>
                        <p class="mb-3">Coba kata kunci lain, atau lihat semua menu dulu.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-dark">Lihat semua menu</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="kl-empty" id="fav-empty" hidden>
            <span class="kl-empty-ic"><x-icon name="heart" :size="32" /></span>
            <h5>Belum ada favorit</h5>
            <p class="mb-0">Tekan ikon hati di menu yang kamu suka, nanti muncul di sini.</p>
        </div>

        <div class="mt-4">{{ $products->links('pagination::bootstrap-5') }}</div>
    </div>

    {{-- Panel pesanan (desktop). Di HP diganti bar keranjang di atas tab bar. --}}
    <div class="col-lg-4 col-xl-3 d-none d-lg-block">
        <aside class="kl-sidebar" aria-label="Pesanan Anda">
            <div class="kl-panel" id="mini-cart">
                @include('cart._mini', ['items' => $cartItems, 'subtotal' => $subtotal])
            </div>
        </aside>
    </div>
</div>
@endsection
