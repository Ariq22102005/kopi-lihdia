<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <meta name="theme-color" content="#1c5133">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/kopilihdia.css') }}?v=20" rel="stylesheet">
    <script>
        document.documentElement.classList.add('js');
        // jaring pengaman: kalau skrip gagal dimuat, jangan biarkan konten tersembunyi
        setTimeout(function () { if (!window.__klLoaded) document.documentElement.classList.add('kl-safe'); }, 3500);
    </script>
    @stack('head')
</head>
@php
    $user = auth()->user();
    $isAdmin = $user?->isAdmin() ?? false;
    $isCustomer = $user && ! $isAdmin;
    $isHome = request()->routeIs('home');

    $cartItems = $isCustomer ? $user->cartItems()->with('product')->get() : collect();
    $cartCount = (int) $cartItems->sum('quantity');
    $cartTotal = $cartItems->sum(fn ($i) => $i->product->price * $i->quantity);
@endphp
<body data-login-url="{{ route('login') }}"
      @if($isCustomer) data-mini-url="{{ route('cart.mini') }}" @endif>

@include('partials.icons')
<a href="#konten" class="skip-link">Lewati ke konten</a>

<header @class(['kl-header', 'kl-header--dark' => $isHome]) data-header>
    <div class="container kl-header-in">
        <a href="{{ route('home') }}" class="kl-brand" aria-label="{{ config('app.name') }}, ke beranda">
            <img src="{{ asset('img/logo-horizontal-green.png') }}" alt="{{ config('app.name') }}" class="kl-logo-on-light">
            <img src="{{ asset('img/logo-horizontal-white.png') }}" alt="" class="kl-logo-on-dark" aria-hidden="true">
        </a>

        {{-- Menu (desktop). Di HP pakai tab bar di bawah. --}}
        <nav class="kl-nav d-none d-lg-flex" aria-label="Navigasi utama" data-nav>
            @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}" @class(['kl-link', 'active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                <a href="{{ route('admin.categories.index') }}" @class(['kl-link', 'active' => request()->routeIs('admin.categories.*')])>Kategori</a>
                <a href="{{ route('admin.products.index') }}" @class(['kl-link', 'active' => request()->routeIs('admin.products.*')])>Produk</a>
                <a href="{{ route('admin.orders.index') }}" @class(['kl-link', 'active' => request()->routeIs('admin.orders.*')])>Pesanan</a>
                <a href="{{ route('admin.reservations.index') }}" @class(['kl-link', 'active' => request()->routeIs('admin.reservations.*')])>Reservasi</a>
            @else
                <a href="{{ route('home') }}" @class(['kl-link', 'active' => request()->routeIs('home')])>Beranda</a>
                <a href="{{ route('products.index') }}" @class(['kl-link', 'active' => request()->routeIs('products.*')])>Menu</a>
                <a href="{{ route('home') }}#reservasi" class="kl-link">Reservasi</a>
                @if($isCustomer)
                    <a href="{{ route('orders.index') }}" @class(['kl-link', 'active' => request()->routeIs('orders.*')])>Pesanan</a>
                @endif
            @endif
            <span class="kl-ink" aria-hidden="true"></span>
        </nav>

        <div class="kl-actions">
            @guest
                <a href="{{ route('login') }}" class="kl-textbtn">Masuk</a>
                <a href="{{ route('products.index') }}" class="kl-cta">Pesan sekarang</a>
            @else
                @if($isCustomer)
                    <a href="{{ route('cart.index') }}" data-cart-target aria-label="Keranjang"
                       @class(['kl-iconlink', 'kl-hide-sm', 'active' => request()->routeIs('cart.*', 'checkout.*')])>
                        <x-icon name="cart" :size="22" />
                        <span class="kl-dot" data-cart-count @if($cartCount === 0) style="display:none" @endif>{{ $cartCount }}</span>
                    </a>
                @endif

                <div class="kl-account" data-account>
                    <button type="button" class="kl-avatar" aria-haspopup="menu" aria-expanded="false" aria-label="Menu akun {{ $user->name }}" data-account-btn>
                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}
                    </button>
                    <div class="kl-menu" role="menu" aria-label="Akun">
                        <div class="kl-menu-head">
                            <strong>{{ $user->name }}</strong>
                            <small>{{ $user->email }}</small>
                            @if($isAdmin)<span class="kl-role">Admin</span>@endif
                        </div>
                        <a href="{{ route('profile.edit') }}" role="menuitem" style="--i:0"><x-icon name="user" :size="18" /> Profil saya</a>
                        @if($isAdmin)
                            <a href="{{ route('home') }}" role="menuitem" style="--i:1"><x-icon name="home" :size="18" /> Lihat toko</a>
                        @else
                            <a href="{{ route('orders.index') }}" role="menuitem" style="--i:1"><x-icon name="receipt" :size="18" /> Pesanan saya</a>
                        @endif
                        <hr>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem" class="is-danger" style="--i:2"><x-icon name="logout" :size="18" /> Keluar</button>
                        </form>
                    </div>
                </div>
            @endguest
        </div>
    </div>
</header>

@yield('hero')

<main id="konten" @class(['container', 'kl-main', 'kl-main--home' => $isHome])>
    @if(session('success'))
        <div class="alert alert-success" role="status"><x-icon name="check" :size="20" /><div>{{ session('success') }}</div></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" role="alert"><x-icon name="alert" :size="20" /><div>{{ session('error') }}</div></div>
    @endif
    @yield('content')
</main>

<footer class="kl-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <img src="{{ asset('img/logo-horizontal-white.png') }}" alt="{{ config('app.name') }}" height="38" class="mb-3">
                <p class="mb-0 small" style="max-width: 38ch">Kopi dan camilan pilihan, dipesan online dan dibayar mudah lewat Midtrans.</p>
            </div>
            <div class="col-6 col-lg-3 offset-lg-1">
                <h6>Jelajahi</h6>
                <ul>
                    <li><a href="{{ route('home') }}">Beranda</a></li>
                    <li><a href="{{ route('products.index') }}">Semua menu</a></li>
                    @auth
                        @unless($isAdmin)
                            <li><a href="{{ route('orders.index') }}">Pesanan saya</a></li>
                            <li><a href="{{ route('cart.index') }}">Keranjang</a></li>
                        @endunless
                    @else
                        <li><a href="{{ route('login') }}">Masuk</a></li>
                        <li><a href="{{ route('register') }}">Daftar</a></li>
                    @endauth
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h6>Pembayaran</h6>
                <ul>
                    <li class="d-flex align-items-center gap-2"><x-icon name="shield" :size="16" /> Diproses aman oleh Midtrans</li>
                    <li class="d-flex align-items-center gap-2"><x-icon name="receipt" :size="16" /> Invoice tiap pesanan</li>
                </ul>
            </div>
        </div>
        <div class="kl-footer-bar">&copy; {{ now()->year }} {{ config('app.name') }}. Tugas Besar Praktikum Pemrograman E-Commerce.</div>
    </div>
</footer>

<div id="kl-toasts" class="kl-toasts" role="status" aria-live="polite"></div>

{{-- Bar keranjang (HP), muncul di atas tab bar saat ada isi --}}
@if($isCustomer && request()->routeIs('products.*'))
    <a href="{{ route('cart.index') }}" id="mobile-cart-bar" class="kl-mobile-bar d-lg-none"
       @if($cartCount === 0) style="display:none" @endif>
        <span><x-icon name="cart" :size="18" /> <span><span data-cart-count>{{ $cartCount }}</span> item</span></span>
        <span data-bar-total>Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
        <span>Lihat <x-icon name="chevron-right" :size="16" /></span>
    </a>
@endif

{{-- Tab bar HP --}}
<nav class="kl-tabbar d-lg-none" aria-label="Navigasi bawah">
    @unless($isAdmin)
        <a href="{{ route('home') }}" @class(['kl-tab', 'active' => request()->routeIs('home')])>
            <x-icon name="home" :size="22" /> Beranda
        </a>
        <a href="{{ route('products.index') }}" @class(['kl-tab', 'active' => request()->routeIs('products.*')])>
            <x-icon name="cup" :size="22" /> Menu
        </a>
    @endunless
    @if($isAdmin)
        <a href="{{ route('admin.dashboard') }}" @class(['kl-tab', 'active' => request()->routeIs('admin.dashboard')])><x-icon name="grid" :size="22" /> Dashboard</a>
        <a href="{{ route('admin.products.index') }}" @class(['kl-tab', 'active' => request()->routeIs('admin.products.*', 'admin.categories.*')])><x-icon name="box" :size="22" /> Produk</a>
        <a href="{{ route('admin.orders.index') }}" @class(['kl-tab', 'active' => request()->routeIs('admin.orders.*')])><x-icon name="receipt" :size="22" /> Pesanan</a>
        <a href="{{ route('admin.reservations.index') }}" @class(['kl-tab', 'active' => request()->routeIs('admin.reservations.*')])><x-icon name="calendar" :size="22" /> Reservasi</a>
    @elseif($isCustomer)
        <a href="{{ route('orders.index') }}" @class(['kl-tab', 'active' => request()->routeIs('orders.*')])><x-icon name="receipt" :size="22" /> Pesanan</a>
        <a href="{{ route('cart.index') }}" data-cart-target @class(['kl-tab', 'active' => request()->routeIs('cart.*', 'checkout.*')])>
            <x-icon name="cart" :size="22" /> Keranjang
            <span class="kl-dot" data-cart-count @if($cartCount === 0) style="display:none" @endif>{{ $cartCount }}</span>
        </a>
    @else
        <a href="{{ route('login') }}" @class(['kl-tab', 'active' => request()->routeIs('login')])><x-icon name="lock" :size="22" /> Masuk</a>
    @endif
    @auth
        <a href="{{ route('profile.edit') }}" @class(['kl-tab', 'active' => request()->routeIs('profile.*')])><x-icon name="user" :size="22" /> Akun</a>
    @else
        <a href="{{ route('register') }}" @class(['kl-tab', 'active' => request()->routeIs('register')])><x-icon name="user" :size="22" /> Daftar</a>
    @endauth
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/shop.js') }}?v=3" defer></script>
<script src="{{ asset('js/fun.js') }}?v=1" defer></script>
<script src="{{ asset('js/nav.js') }}?v=1" defer></script>
@stack('scripts')
</body>
</html>
