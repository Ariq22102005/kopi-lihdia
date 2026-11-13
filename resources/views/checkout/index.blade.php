@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<h3 class="fw-bold mb-4">Checkout</h3>

<form method="POST" action="{{ route('checkout.store') }}">
@csrf
<div class="row g-4">
    {{-- Data penerima --}}
    <div class="col-lg-7">
        <div class="kl-product" style="border-radius:24px;">
            <h5 class="fw-bold mb-3">Informasi Penerima</h5>

            <div class="mb-3">
                <label class="form-label">Nama Penerima</label>
                <input type="text" name="recipient_name"
                       value="{{ old('recipient_name', auth()->user()->name) }}"
                       class="form-control @error('recipient_name') is-invalid @enderror" required>
                @error('recipient_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="recipient_phone"
                       value="{{ old('recipient_phone', auth()->user()->phone) }}"
                       class="form-control @error('recipient_phone') is-invalid @enderror" required>
                @error('recipient_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="shipping_address" rows="3"
                          class="form-control @error('shipping_address') is-invalid @enderror"
                          required>{{ old('shipping_address', auth()->user()->address) }}</textarea>
                @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-7 mb-3">
                    <label class="form-label">Kota</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                           class="form-control @error('city') is-invalid @enderror" required>
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Kode Pos</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" maxlength="5"
                           class="form-control @error('postal_code') is-invalid @enderror" required>
                    @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-1">
                <label class="form-label">Catatan (opsional)</label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="col-lg-5">
        <div class="kl-product" style="border-radius:24px;">
            <h5 class="fw-bold mb-3">Ringkasan Pesanan</h5>
            @foreach($items as $item)
                <div class="d-flex justify-content-between small mb-1">
                    <span>{{ $item->product->name }} &times; {{ $item->quantity }}</span>
                    <span>Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <hr>
            <div class="d-flex justify-content-between mb-1">
                <span>Subtotal</span>
                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span>Ongkos kirim</span>
                <span>Rp {{ number_format($shipping, 0, ',', '.') }}</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between fs-5 fw-bold mb-3">
                <span>Total</span>
                <span style="color: var(--kl-green)">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <button class="btn btn-dark rounded-pill py-2 w-100">Buat Pesanan</button>
            <a href="{{ route('cart.index') }}" class="btn btn-link w-100 mt-1">Kembali ke keranjang</a>
            <p class="small text-muted mt-2 mb-0">
                Total dihitung ulang oleh server saat pesanan dibuat.
            </p>
        </div>
    </div>
</div>
</form>
@endsection
