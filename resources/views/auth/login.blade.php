@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="kl-auth">
            <div class="row g-0">
                <div class="col-md-5">
                    <div class="kl-auth-side">
                        <img src="{{ asset('img/logo-stacked-white.png') }}" alt="{{ config('app.name') }}">
                        <p>Selamat datang kembali. Masuk untuk memesan kopi favoritmu.</p>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="p-4 p-md-5">
                        <h3 class="fw-bold mb-1">Masuk</h3>
                        <p class="text-muted mb-4">Gunakan akun {{ config('app.name') }} lo.</p>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror" required autofocus>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-check mb-4">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Ingat saya</label>
                            </div>
                            <button class="btn btn-dark rounded-pill w-100">Masuk</button>
                        </form>

                        <p class="mt-4 mb-0 text-center">Belum punya akun? <a href="{{ route('register') }}" class="fw-semibold">Daftar</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
