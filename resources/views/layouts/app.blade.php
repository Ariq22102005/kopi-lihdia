<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --kl-green: #0b5d2e;
            --kl-green-dark: #084a24;
            --kl-cream: #f4f1de;
            --kl-soft: #dfe9d6;
        }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: var(--kl-cream); color: #1d2b22; }
        .kl-nav { background: var(--kl-cream); border-bottom: 1.5px solid rgba(11,93,46,.15); }
        .kl-nav .navbar-brand { color: var(--kl-green); font-weight: 800; letter-spacing: .3px; }
        .kl-nav .nav-link { color: var(--kl-green); font-weight: 600; }
        .kl-nav .nav-link:hover { color: var(--kl-green-dark); text-decoration: underline; }
        .btn-dark { background: var(--kl-green); border-color: var(--kl-green); }
        .btn-dark:hover, .btn-dark:focus { background: var(--kl-green-dark); border-color: var(--kl-green-dark); }
        .btn-outline-dark { color: var(--kl-green); border-color: var(--kl-green); }
        .btn-outline-dark:hover { background: var(--kl-green); border-color: var(--kl-green); }
        a { color: var(--kl-green); }
        .card { border-radius: 16px; }

        /* Komponen katalog */
        .kl-search { background: #fff; border: 1.5px solid var(--kl-green); border-radius: 999px; padding: 6px 8px 6px 18px; }
        .kl-cat { display: block; text-decoration: none; background: #fff; color: #1d2b22; border: 1.5px solid rgba(11,93,46,.25);
                  border-radius: 24px; padding: 18px 20px; height: 100%; transition: .15s; }
        .kl-cat:hover { border-color: var(--kl-green); transform: translateY(-2px); }
        .kl-cat.active { background: var(--kl-green); color: #fff; border-color: var(--kl-green); }
        .kl-cat .kl-cat-name { font-weight: 700; font-size: 1.15rem; }
        .kl-cat .kl-cat-count { font-size: .85rem; opacity: .75; }
        .kl-product { background: #fff; border: 1.5px solid var(--kl-green); border-radius: 28px; padding: 16px; height: 100%;
                      display: flex; flex-direction: column; }
        .kl-product-img { aspect-ratio: 1 / 1; border-radius: 20px; background: var(--kl-soft); overflow: hidden;
                          display: flex; align-items: center; justify-content: center; font-size: 3rem; }
        .kl-product-img img { width: 100%; height: 100%; object-fit: cover; }
        .kl-product-name { font-weight: 700; color: #1d2b22; text-decoration: none; line-height: 1.25; }
        .kl-price { color: #5b6b60; font-weight: 600; }
        .kl-add { width: 48px; height: 48px; border-radius: 50%; border: 1.5px solid var(--kl-green); background: #fff;
                  color: var(--kl-green); font-size: 1.6rem; line-height: 1; display: inline-flex; align-items: center;
                  justify-content: center; text-decoration: none; transition: .15s; flex-shrink: 0; }
        .kl-add:hover { background: var(--kl-green); color: #fff; }
        .kl-add.disabled { opacity: .35; pointer-events: none; }
        .kl-badge { position: absolute; top: 10px; left: 10px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg kl-nav">
    <div class="container">
        <a class="navbar-brand" href="{{ route('products.index') }}">☕ {{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Menu</a></li>
            </ul>
            <ul class="navbar-nav align-items-lg-center">
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Masuk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Daftar</a></li>
                @else
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.categories.index') }}">Kategori</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.products.index') }}">Kelola Produk</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-link nav-link" type="submit">Keluar</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
