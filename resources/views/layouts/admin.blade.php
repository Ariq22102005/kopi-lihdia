<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Admin {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <meta name="theme-color" content="#0f3320">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/kopilihdia.css') }}?v=20" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}?v=1" rel="stylesheet">
    @stack('head')
</head>
@php
    $user = auth()->user();
    $pendingPay = \App\Models\Order::where('payment_status', 'pending')->count();
    $pendingRsv = \App\Models\Reservation::where('status', 'pending')->count();
@endphp
<body class="ad-body">
@include('partials.icons')
<div class="ad-shell" id="ad-shell">
    <aside class="ad-side" id="ad-side" aria-label="Navigasi admin">
        <a href="{{ route('admin.dashboard') }}" class="ad-brand" aria-label="Dashboard admin">
            <img src="{{ asset('img/logo-horizontal-white.png') }}" alt="{{ config('app.name') }}">
            <span>Admin</span>
        </a>

        <nav>
            <a href="{{ route('admin.dashboard') }}" @class(['ad-link', 'active' => request()->routeIs('admin.dashboard')])><x-icon name="grid" :size="20" /> Dashboard</a>

            <div class="ad-navlabel">Katalog</div>
            <a href="{{ route('admin.products.index') }}" @class(['ad-link', 'active' => request()->routeIs('admin.products.*')])><x-icon name="box" :size="20" /> Produk</a>
            <a href="{{ route('admin.categories.index') }}" @class(['ad-link', 'active' => request()->routeIs('admin.categories.*')])><x-icon name="layers" :size="20" /> Kategori</a>

            <div class="ad-navlabel">Transaksi</div>
            <a href="{{ route('admin.orders.index') }}" @class(['ad-link', 'active' => request()->routeIs('admin.orders.*')])>
                <x-icon name="receipt" :size="20" /> Pesanan
                @if($pendingPay > 0)<span class="ad-badge" title="Menunggu pembayaran">{{ $pendingPay }}</span>@endif
            </a>
            <a href="{{ route('admin.reservations.index') }}" @class(['ad-link', 'active' => request()->routeIs('admin.reservations.*')])>
                <x-icon name="calendar" :size="20" /> Reservasi
                @if($pendingRsv > 0)<span class="ad-badge" title="Menunggu konfirmasi">{{ $pendingRsv }}</span>@endif
            </a>
        </nav>

        <div class="ad-side-foot">
            <div class="ad-me">
                <span class="kl-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}</span>
                <div><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></div>
            </div>
            <a href="{{ route('home') }}" class="ad-link"><x-icon name="home" :size="20" /> Lihat toko</a>
            <a href="{{ route('profile.edit') }}" class="ad-link"><x-icon name="user" :size="20" /> Profil saya</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ad-link is-danger"><x-icon name="logout" :size="20" /> Keluar</button>
            </form>
        </div>
    </aside>
    <div class="ad-scrim" id="ad-scrim"></div>

    <div class="ad-main">
        <header class="ad-top">
            <button type="button" class="ad-burger" id="ad-burger" aria-label="Buka menu" aria-controls="ad-side">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
            <div class="ad-crumb"><small>Admin KopiLihdia</small>@yield('title', 'Admin')</div>
            <div class="ad-top-r">
                <a href="{{ route('home') }}" class="ad-pill" target="_blank" rel="noopener"><x-icon name="home" :size="16" /> <span>Lihat toko</span></a>
            </div>
        </header>

        <main class="ad-content" id="konten">
            @if(session('success'))
                <div class="alert alert-success" role="status"><x-icon name="check" :size="20" /><div>{{ session('success') }}</div></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" role="alert"><x-icon name="alert" :size="20" /><div>{{ session('error') }}</div></div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        var shell = document.getElementById('ad-shell');
        var toggle = function (open) { shell.classList.toggle('is-open', open); };
        document.getElementById('ad-burger').addEventListener('click', function () { toggle(!shell.classList.contains('is-open')); });
        document.getElementById('ad-scrim').addEventListener('click', function () { toggle(false); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') toggle(false); });
    })();
</script>
@stack('scripts')
</body>
</html>
