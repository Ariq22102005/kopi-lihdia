@extends('layouts.app')
@section('title', 'Daftar')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="kl-auth">
            <div class="row g-0">
                <div class="col-md-5">
                    <div class="kl-auth-side">
                        <img src="{{ asset('img/logo-stacked-white.png') }}" alt="{{ config('app.name') }}">
                        <p>Buat akun untuk memesan, membayar, dan memantau pesananmu.</p>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="p-4 p-md-5">
                        <h3 class="fw-bold mb-1">Daftar Akun</h3>
                        <p class="text-muted mb-4">Gratis dan hanya butuh satu menit.</p>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror" required autofocus>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Minimal 8 karakter.</div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <button class="btn btn-dark rounded-pill w-100">Daftar</button>
                        </form>

                        <p class="mt-4 mb-0 text-center">Sudah punya akun? <a href="{{ route('login') }}" class="fw-semibold">Masuk</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
