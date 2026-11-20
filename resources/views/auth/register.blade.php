@extends('layouts.app')
@section('title', 'Daftar')

@section('content')
<div class="row justify-content-center py-lg-4">
    <div class="col-lg-10 col-xl-9">
        <div class="kl-auth">
            <div class="row g-0">
                <div class="col-md-5 d-none d-md-block">
                    <div class="kl-auth-side">
                        <img src="{{ asset('img/logo-stacked-white.png') }}" alt="{{ config('app.name') }}">
                        <p>Buat akun untuk memesan, membayar, dan memantau pesanan Anda.</p>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="p-4 p-md-5">
                        <h1 class="h3 fw-bold mb-1">Daftar akun</h1>
                        <p class="text-muted mb-4">Gratis dan hanya butuh satu menit.</p>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="name">Nama</label>
                                <div class="kl-field">
                                    <x-icon name="user" :size="18" />
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" autocomplete="name"
                                           class="form-control @error('name') is-invalid @enderror" required autofocus>
                                </div>
                                @error('name')<div class="text-danger small fw-medium mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <div class="kl-field">
                                    <x-icon name="mail" :size="18" />
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email"
                                           class="form-control @error('email') is-invalid @enderror" required>
                                </div>
                                @error('email')<div class="text-danger small fw-medium mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <div class="kl-field has-eye">
                                    <x-icon name="lock" :size="18" />
                                    <input type="password" id="password" name="password" autocomplete="new-password"
                                           class="form-control @error('password') is-invalid @enderror" required>
                                    <button type="button" class="kl-eye" data-toggle-password="password" aria-label="Tampilkan password">
                                        <x-icon name="eye" :size="18" />
                                    </button>
                                </div>
                                @error('password')<div class="text-danger small fw-medium mt-1">{{ $message }}</div>@enderror
                                <div class="form-text">Minimal 8 karakter.</div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="password_confirmation">Konfirmasi password</label>
                                <div class="kl-field">
                                    <x-icon name="lock" :size="18" />
                                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="form-control" required>
                                </div>
                            </div>
                            <button class="btn btn-dark w-100" data-loading-text="Memproses...">Buat akun</button>
                        </form>

                        <p class="mt-4 mb-0 text-center">Sudah punya akun? <a href="{{ route('login') }}" class="fw-semibold">Masuk</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
