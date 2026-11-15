<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <meta name="theme-color" content="#1c5133">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --kl-green: #1c5133;
            --kl-green-dark: #143d27;
            --kl-cream: #f3f1de;
            --kl-soft: #dbe7d3;
            --kl-line: rgba(28, 81, 51, .35);
        }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: var(--kl-cream); color: #1d2b22; }
        a { color: var(--kl-green); }
        .card { border-radius: 16px; }

        /* Tombol bawaan Bootstrap disesuaikan dengan tema */
        .btn-dark { background: var(--kl-green); border-color: var(--kl-green); }
        .btn-dark:hover, .btn-dark:focus, .btn-dark:active { background: var(--kl-green-dark) !important; border-color: var(--kl-green-dark) !important; }
        .btn-outline-dark { color: var(--kl-green); border-color: var(--kl-green); }
        .btn-outline-dark:hover { background: var(--kl-green); border-color: var(--kl-green); color: #fff; }

        /* Header */
        .kl-header { padding: 12px 0; position: sticky; top: 0; z-index: 1020; background: rgba(243, 241, 222, .94);
                     backdrop-filter: blur(8px); border-bottom: 1px solid rgba(28, 81, 51, .08); }
        .kl-logo { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; color: var(--kl-green);
                   font-weight: 800; letter-spacing: .6px; text-transform: uppercase; font-size: 1.1rem; }
        .kl-logo-mark { width: 38px; height: 38px; border: 2px solid var(--kl-green); border-radius: 50%;
                        display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; }
        .kl-date { color: var(--kl-green); font-weight: 600; }
        .kl-pill { display: inline-flex; align-items: center; gap: 8px; background: #fff; color: var(--kl-green);
                   border: 0; border-radius: 999px; padding: 10px 20px; font-weight: 600; text-decoration: none;
                   transition: .15s; cursor: pointer; font-size: .95rem; }
        .kl-pill:hover { background: var(--kl-soft); color: var(--kl-green-dark); }
        .kl-pill.active, .kl-pill-solid { background: var(--kl-green); color: #fff; }
        .kl-pill-solid:hover { background: var(--kl-green-dark); color: #fff; }
        .kl-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--kl-green); color: #fff;
                     display: inline-flex; align-items: center; justify-content: center; font-weight: 700;
                     text-decoration: none; border: 2px solid #fff; }
        .kl-dot { background: #e5484d; color: #fff; border-radius: 999px; font-size: .7rem; padding: 2px 7px; font-weight: 700; }

        /* Pencarian */
        .kl-search { background: #fff; border: 1.5px solid var(--kl-green); border-radius: 999px;
                     padding: 6px 8px 6px 20px; display: flex; align-items: center; gap: 8px; }
        .kl-search input { font-weight: 500; }

        /* Kartu kategori */
        .kl-cat { position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;
                  min-height: 150px; text-decoration: none; background: #fff; color: #1d2b22;
                  border: 1.5px solid rgba(28, 81, 51, .2); border-radius: 28px; padding: 16px 18px; transition: .15s; height: 100%; }
        .kl-cat:hover { border-color: var(--kl-green); transform: translateY(-2px); color: #1d2b22; }
        .kl-cat.active, .kl-cat.active:hover { background: var(--kl-green); color: #fff; border-color: var(--kl-green); }
        .kl-cat-name { font-weight: 700; font-size: 1.15rem; }
        .kl-cat-count { font-size: .85rem; opacity: .7; }
        .kl-cat-icon { position: absolute; right: -4px; bottom: -14px; font-size: 5rem; opacity: .14; line-height: 1; pointer-events: none; }
        .kl-cat.active .kl-cat-icon { opacity: .3; }
        .kl-tag { align-self: flex-start; display: inline-block; font-size: .75rem; font-weight: 600; border: 1px solid currentColor;
                  border-radius: 999px; padding: 3px 12px; }
        .kl-tag-warn { background: #ff7a7a; border-color: #ff7a7a; color: #fff; }

        /* Kartu produk (juga dipakai sebagai panel di halaman lain) */
        .kl-product { position: relative; background: #fff; border: 1.5px solid var(--kl-line); border-radius: 30px;
                      padding: 16px; height: 100%; display: flex; flex-direction: column; transition: .15s; }
        .kl-product[data-product-card]:hover { border-color: var(--kl-green); box-shadow: 0 8px 24px rgba(28, 81, 51, .1); }
        .kl-product-img { aspect-ratio: 1 / 1; border-radius: 22px; background: var(--kl-soft); overflow: hidden;
                          display: flex; align-items: center; justify-content: center; font-size: 3rem; }
        .kl-product-img img { width: 100%; height: 100%; object-fit: cover; }
        .kl-product-name { font-weight: 700; color: #1d2b22; text-decoration: none; line-height: 1.25; }
        .kl-price { color: #5b6b60; font-weight: 600; }
        .kl-badge { position: absolute; top: 24px; left: 24px; z-index: 2; }
        .kl-incart { position: absolute; top: 12px; right: 14px; min-width: 26px; height: 26px; padding: 0 6px; border-radius: 999px;
                     background: var(--kl-soft); color: var(--kl-green); font-weight: 700; font-size: .8rem;
                     display: flex; align-items: center; justify-content: center; z-index: 2; }
        .kl-add { width: 48px; height: 48px; border-radius: 50%; border: 1.5px solid var(--kl-green); background: #fff;
                  color: var(--kl-green); font-size: 1.6rem; line-height: 1; display: inline-flex; align-items: center;
                  justify-content: center; text-decoration: none; transition: .15s; flex-shrink: 0; cursor: pointer; padding: 0; }
        .kl-add:hover, .kl-add-on { background: var(--kl-green); color: #fff; }
        .kl-add:disabled { opacity: .35; pointer-events: none; }

        /* Panel pesanan di sisi kanan */
        .kl-sidebar { position: sticky; top: 92px; }
        .kl-panel { background: #fff; border: 1.5px solid var(--kl-line); border-radius: 30px; padding: 20px;
                    max-height: calc(100vh - 112px); overflow-y: auto; }
        .kl-count-pill { background: var(--kl-soft); color: var(--kl-green); font-weight: 700; font-size: .8rem;
                         padding: 4px 12px; border-radius: 999px; }
        .kl-mini-item { display: flex; gap: 12px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #eef0e6; }
        .kl-mini-item:last-of-type { border-bottom: 0; }
        .kl-mini-thumb { width: 54px; height: 54px; border-radius: 16px; background: var(--kl-soft); overflow: hidden;
                         display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.4rem; }
        .kl-mini-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .kl-mini-name { font-weight: 700; font-size: .92rem; }
        .kl-mini-qty { display: inline-flex; align-items: center; gap: 10px; }
        .kl-qty-btn { width: 26px; height: 26px; border-radius: 50%; border: 1.5px solid var(--kl-green); background: #fff;
                      color: var(--kl-green); font-weight: 700; line-height: 1; padding: 0; cursor: pointer;
                      display: inline-flex; align-items: center; justify-content: center; }
        .kl-qty-btn:hover { background: var(--kl-green); color: #fff; }
        .kl-qty-btn:disabled { opacity: .35; pointer-events: none; }

        /* Kontrol jumlah di halaman keranjang */
        .kl-qty { display: inline-flex; align-items: center; gap: 8px; }
        .kl-qty .btn { width: 34px; height: 34px; border-radius: 50%; padding: 0; line-height: 1; }

        /* Bar keranjang di HP */
        .kl-mobile-bar { position: fixed; left: 12px; right: 12px; bottom: 12px; z-index: 1030; background: var(--kl-green);
                         color: #fff; border-radius: 999px; padding: 14px 22px; text-decoration: none; font-weight: 700;
                         display: flex; justify-content: space-between; align-items: center; box-shadow: 0 8px 24px rgba(0,0,0,.2); }
        .kl-mobile-bar:hover { color: #fff; background: var(--kl-green-dark); }
        @media (max-width: 991.98px) { body { padding-bottom: 84px; } }

        /* Placeholder produk tanpa foto: ikon logo */
        .kl-ph { width: 46%; max-width: 120px; opacity: .35; }
        .kl-mini-thumb .kl-ph { width: 62%; }

        /* Banner utama di katalog */
        .kl-hero { position: relative; overflow: hidden; display: flex; background: var(--kl-green); color: #fff;
                   border-radius: 32px; min-height: 210px; }
        .kl-hero-text { padding: 32px 36px; flex: 1 1 60%; display: flex; flex-direction: column; justify-content: center; gap: 10px; z-index: 1; }
        .kl-hero-text h2 { font-weight: 800; font-size: clamp(1.4rem, 2.6vw, 2.1rem); line-height: 1.15; margin: 0; }
        .kl-hero-text p { margin: 0; opacity: .85; max-width: 440px; }
        .kl-hero-img { flex: 0 0 34%; background-size: cover; background-position: center 62%; }
        .kl-hero .btn-light { color: var(--kl-green); font-weight: 700; align-self: flex-start; }

        /* Halaman masuk dan daftar */
        .kl-auth { background: #fff; border: 1.5px solid var(--kl-line); border-radius: 32px; overflow: hidden; }
        .kl-auth-side { background: var(--kl-green); color: #fff; padding: 40px 32px; display: flex; flex-direction: column;
                        align-items: center; justify-content: center; text-align: center; gap: 14px; min-height: 100%; }
        .kl-auth-side img { width: 78%; max-width: 260px; }
        .kl-auth-side p { opacity: .85; margin: 0; font-size: .95rem; }
        .kl-auth .form-control { border-radius: 999px; padding: 12px 20px; border-color: rgba(28, 81, 51, .35); }
        .kl-auth .form-control:focus { border-color: var(--kl-green); box-shadow: 0 0 0 .2rem rgba(28, 81, 51, .15); }
        .kl-auth .btn-dark { padding: 12px; font-weight: 700; }

        /* Footer */
        .kl-footer { background: var(--kl-green); color: rgba(255, 255, 255, .85); padding: 36px 0 28px; margin-top: 48px; }
        .kl-footer a { color: #fff; text-decoration: none; }
        .kl-footer a:hover { text-decoration: underline; }

        /* Notifikasi singkat */
        .kl-toasts { position: fixed; top: 16px; left: 50%; transform: translateX(-50%); z-index: 2000;
                     display: flex; flex-direction: column; gap: 8px; align-items: center; pointer-events: none; }
        .kl-toast { background: var(--kl-green); color: #fff; padding: 10px 20px; border-radius: 999px; font-weight: 600;
                    font-size: .9rem; box-shadow: 0 8px 24px rgba(0,0,0,.2); transition: .3s; }
        .kl-toast-error { background: #c0392b; }
        .kl-toast.hide { opacity: 0; transform: translateY(-8px); }
    </style>
</head>
@php
    $user = auth()->user();
    $isAdmin = $user?->isAdmin() ?? false;
    $isCustomer = $user && ! $isAdmin;

    $cartItems = $isCustomer ? $user->cartItems()->with('product')->get() : collect();
    $cartCount = (int) $cartItems->sum('quantity');
    $cartTotal = $cartItems->sum(fn ($i) => $i->product->price * $i->quantity);
@endphp
<body data-login-url="{{ route('login') }}"
      @if($isCustomer) data-mini-url="{{ route('cart.mini') }}" @endif>

<header class="kl-header">
    <div class="container d-flex flex-wrap align-items-center gap-3">
        <a href="{{ route('products.index') }}" class="kl-logo" aria-label="{{ config('app.name') }}">
            <img src="{{ asset('img/logo-horizontal-green.png') }}" alt="{{ config('app.name') }}" height="40">
        </a>
        <span class="kl-date d-none d-xl-inline">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>

        <button class="btn btn-light rounded-pill ms-auto d-lg-none" type="button"
                data-bs-toggle="collapse" data-bs-target="#nav" aria-label="Menu">☰</button>

        <div class="collapse d-lg-flex align-items-lg-center gap-2 ms-lg-auto flex-column flex-lg-row w-100 w-lg-auto mt-2 mt-lg-0" id="nav">
            <a href="{{ route('products.index') }}" @class(['kl-pill', 'active' => request()->routeIs('products.*')])>Menu</a>

            @guest
                <a href="{{ route('login') }}" class="kl-pill">Masuk</a>
                <a href="{{ route('register') }}" class="kl-pill kl-pill-solid">Daftar</a>
            @else
                @if($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" @class(['kl-pill', 'active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                    <a href="{{ route('admin.categories.index') }}" @class(['kl-pill', 'active' => request()->routeIs('admin.categories.*')])>Kategori</a>
                    <a href="{{ route('admin.products.index') }}" @class(['kl-pill', 'active' => request()->routeIs('admin.products.*')])>Kelola Produk</a>
                    <a href="{{ route('admin.orders.index') }}" @class(['kl-pill', 'active' => request()->routeIs('admin.orders.*')])>Pesanan</a>
                @else
                    <a href="{{ route('orders.index') }}" @class(['kl-pill', 'active' => request()->routeIs('orders.*')])>Pesanan Saya</a>
                    <a href="{{ route('cart.index') }}" @class(['kl-pill', 'active' => request()->routeIs('cart.*', 'checkout.*')])>
                        🛒 Keranjang
                        <span class="kl-dot" data-cart-count @if($cartCount === 0) style="display:none" @endif>{{ $cartCount }}</span>
                    </a>
                @endif

                <a href="{{ route('profile.edit') }}" class="kl-avatar" title="{{ $user->name }}">
                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}
                </a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="kl-pill">Keluar</button>
                </form>
            @endguest
        </div>
    </div>
</header>

<main class="container py-4">
    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
    @endif
    @yield('content')
</main>

<footer class="kl-footer">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-md-6">
                <img src="{{ asset('img/logo-horizontal-white.png') }}" alt="{{ config('app.name') }}" height="38" class="mb-2">
                <p class="mb-0 small">Kopi dan camilan pilihan, dipesan online dan dibayar mudah lewat Midtrans.</p>
            </div>
            <div class="col-md-6 text-md-end small">
                <a href="{{ route('products.index') }}">Menu</a>
                @auth
                    @unless($isAdmin)
                        &nbsp;&middot;&nbsp; <a href="{{ route('orders.index') }}">Pesanan Saya</a>
                    @endunless
                @else
                    &nbsp;&middot;&nbsp; <a href="{{ route('login') }}">Masuk</a>
                @endauth
                <div class="mt-2 opacity-75">&copy; {{ now()->year }} {{ config('app.name') }}. Tugas Besar Praktikum Pemrograman E-Commerce.</div>
            </div>
        </div>
    </div>
</footer>

<div id="kl-toasts" class="kl-toasts"></div>

@if($isCustomer && request()->routeIs('products.*'))
    <a href="{{ route('cart.index') }}" id="mobile-cart-bar" class="kl-mobile-bar d-lg-none"
       @if($cartCount === 0) style="display:none" @endif>
        <span>🛒 <span data-cart-count>{{ $cartCount }}</span> item</span>
        <span data-bar-total>Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
        <span>Lihat &rarr;</span>
    </a>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/shop.js') }}" defer></script>
@stack('scripts')
</body>
</html>
