@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="row justify-content-center py-lg-4">
    <div class="col-lg-10 col-xl-9">
        <div class="kl-auth">
            <div class="row g-0">
                <div class="col-md-5 d-none d-md-block">
                    <div class="kl-auth-side">
                        <img src="{{ asset('img/logo-stacked-white.png') }}" alt="{{ config('app.name') }}">
                        <p>Selamat datang kembali. Masuk untuk memesan kopi favoritmu.</p>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="p-4 p-md-5">
                        <h1 class="h3 fw-bold mb-1">Masuk</h1>
                        <p class="text-muted mb-4">Gunakan akun {{ config('app.name') }} Anda.</p>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <div class="kl-field">
                                    <x-icon name="mail" :size="18" />
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email"
                                           class="form-control @error('email') is-invalid @enderror" required autofocus>
                                </div>
                                @error('email')<div class="text-danger small fw-medium mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <div class="kl-field has-eye">
                                    <x-icon name="lock" :size="18" />
                                    <input type="password" id="password" name="password" autocomplete="current-password" class="form-control" required>
                                    <button type="button" class="kl-eye" data-toggle-password="password" aria-label="Tampilkan password">
                                        <x-icon name="eye" :size="18" />
                                    </button>
                                </div>
                            </div>
                            <div class="form-check mb-4">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Ingat saya</label>
                            </div>
                            <button class="btn btn-dark w-100" data-loading-text="Memproses...">Masuk</button>
                        </form>

                        <p class="mt-4 mb-0 text-center">Belum punya akun? <a href="{{ route('register') }}" class="fw-semibold">Daftar</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
