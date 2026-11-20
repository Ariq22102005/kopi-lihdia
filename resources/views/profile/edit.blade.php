@extends('layouts.app')
@section('title', 'Profil')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
        <div class="kl-pagehead">
            <div class="d-flex align-items-center gap-3">
                <span class="kl-avatar" style="width:56px;height:56px;font-size:1.3rem;margin:0" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}</span>
                <div>
                    <h1>Profil Saya</h1>
                    <p>{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <div class="kl-card">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label" for="name">Nama</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" autocomplete="name"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email"
                           class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="phone">No. telepon</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" autocomplete="tel" inputmode="tel" class="form-control">
                </div>
                <div class="mb-4">
                    <label class="form-label" for="address">Alamat</label>
                    <textarea id="address" name="address" rows="3" autocomplete="street-address" class="form-control">{{ old('address', $user->address) }}</textarea>
                    <div class="form-text">Alamat ini diisi otomatis saat checkout.</div>
                </div>
                <button class="btn btn-dark px-4" data-loading-text="Menyimpan...">Simpan perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection
