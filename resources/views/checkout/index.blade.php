@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="kl-steps" aria-label="Langkah pemesanan">
    <span class="kl-step done"><span class="kl-step-n"><x-icon name="check" :size="14" /></span> Keranjang</span><span class="kl-step-bar"></span>
    <span class="kl-step now"><span class="kl-step-n">2</span> Checkout</span><span class="kl-step-bar"></span>
    <span class="kl-step"><span class="kl-step-n">3</span> Bayar</span>
</div>

<div class="kl-pagehead">
    <div>
        <h1>Checkout</h1>
        <p>Isi alamat pengiriman, lalu bayar di langkah berikutnya.</p>
    </div>
</div>

<form method="POST" action="{{ route('checkout.store') }}">
@csrf
<div class="row g-4">
    {{-- Data penerima --}}
    <div class="col-lg-7">
        <div class="kl-card">
            <h2 class="kl-card-title"><x-icon name="pin" :size="20" /> Informasi Penerima</h2>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="recipient_name">Nama penerima</label>
                    <input type="text" id="recipient_name" name="recipient_name" autocomplete="name"
                           value="{{ old('recipient_name', auth()->user()->name) }}"
                           class="form-control @error('recipient_name') is-invalid @enderror" required>
                    @error('recipient_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="recipient_phone">No. telepon</label>
                    <input type="tel" id="recipient_phone" name="recipient_phone" autocomplete="tel" inputmode="tel"
                           value="{{ old('recipient_phone', auth()->user()->phone) }}"
                           class="form-control @error('recipient_phone') is-invalid @enderror" required>
                    @error('recipient_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label" for="shipping_address">Alamat lengkap</label>
                    <textarea id="shipping_address" name="shipping_address" rows="3" autocomplete="street-address"
                              placeholder="Nama jalan, nomor rumah, RT/RW, patokan"
                              class="form-control @error('shipping_address') is-invalid @enderror"
                              required>{{ old('shipping_address', auth()->user()->address) }}</textarea>
                    @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-7">
                    <label class="form-label" for="city">Kota</label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}" autocomplete="address-level2"
                           class="form-control @error('city') is-invalid @enderror" required>
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="postal_code">Kode pos</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" maxlength="5"
                           inputmode="numeric" autocomplete="postal-code"
                           class="form-control @error('postal_code') is-invalid @enderror" required>
                    @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label" for="notes">Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Contoh: kurang manis, tanpa es"
                              class="form-control">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="col-lg-5">
        <div class="kl-card kl-sticky">
            <h2 class="kl-card-title"><x-icon name="receipt" :size="20" /> Ringkasan Pesanan</h2>
            @foreach($items as $item)
                <div class="kl-summary-row">
                    <span class="text-muted">{{ $item->product->name }} &times; {{ $item->quantity }}</span>
                    <span class="fw-semibold">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <hr class="my-3">
            <div class="kl-summary-row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="kl-summary-row">
                <span>Ongkos kirim</span>
                <span>Rp {{ number_format($shipping, 0, ',', '.') }}</span>
            </div>
            <div class="kl-summary-total">
                <span>Total</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <button class="btn btn-dark btn-lg w-100 mt-3" data-loading-text="Membuat pesanan...">Buat pesanan</button>
            <a href="{{ route('cart.index') }}" class="btn btn-link w-100 mt-1">Kembali ke keranjang</a>
            <p class="small text-muted mt-2 mb-0 d-flex gap-2">
                <x-icon name="shield" :size="16" />
                <span>Total dihitung ulang oleh server saat pesanan dibuat.</span>
            </p>
        </div>
    </div>
</div>
</form>
@endsection
