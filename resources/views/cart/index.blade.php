@extends('layouts.app')
@section('title', 'Keranjang')

@section('content')
<div class="kl-steps" aria-label="Langkah pemesanan">
    <span class="kl-step now"><span class="kl-step-n">1</span> Keranjang</span><span class="kl-step-bar"></span>
    <span class="kl-step"><span class="kl-step-n">2</span> Checkout</span><span class="kl-step-bar"></span>
    <span class="kl-step"><span class="kl-step-n">3</span> Bayar</span>
</div>

<div class="kl-pagehead">
    <div>
        <h1>Keranjang Belanja</h1>
        @unless($items->isEmpty())<p>{{ $items->sum('quantity') }} item siap dipesan</p>@endunless
    </div>
</div>

@if($items->isEmpty())
    <div class="kl-empty py-5">
        <span class="kl-empty-ic"><x-icon name="cart" :size="34" /></span>
        <h5>Keranjang masih kosong</h5>
        <p class="mb-3">Pilih menu favoritmu, nanti muncul di sini.</p>
        <a href="{{ route('products.index') }}" class="btn btn-dark px-4">Lihat menu</a>
    </div>
@else
<div class="row g-4">
    {{-- Daftar item --}}
    <div class="col-lg-8">
        @foreach($items as $item)
            @php
                $p = $item->product;
            @endphp
            <div class="kl-line">
                <a href="{{ route('products.show', $p) }}" class="kl-line-img" tabindex="-1" aria-hidden="true">
                    @if($p->image)
                        <img src="{{ asset('img/'.$p->image) }}" alt="">
                    @else
                        <img src="{{ asset('img/logo-icon-green.png') }}" alt="" class="kl-ph">
                    @endif
                </a>

                <div class="flex-grow-1" style="min-width:0">
                    <a href="{{ route('products.show', $p) }}" class="kl-product-name">{{ $p->name }}</a>
                    <div class="text-muted small">Rp {{ number_format($p->price, 0, ',', '.') }}</div>

                    @if(! $p->is_active)
                        <div class="kl-line-warn"><x-icon name="alert" :size="16" /> Menu ini sudah tidak tersedia.</div>
                    @elseif($p->stock < $item->quantity)
                        <div class="kl-line-warn"><x-icon name="alert" :size="16" /> Stok tersisa {{ $p->stock }}, kurangi jumlahnya.</div>
                    @endif

                    <div class="kl-stepper mt-2">
                        <form method="POST" action="{{ route('cart.update', $item) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="quantity" value="{{ $item->quantity - 1 }}">
                            <button type="submit" aria-label="Kurangi {{ $p->name }}" @disabled($item->quantity <= 1)><x-icon name="minus" :size="16" /></button>
                        </form>
                        <strong aria-label="Jumlah">{{ $item->quantity }}</strong>
                        <form method="POST" action="{{ route('cart.update', $item) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                            <button type="submit" aria-label="Tambah {{ $p->name }}" @disabled($item->quantity >= $p->stock)><x-icon name="plus" :size="16" /></button>
                        </form>
                    </div>
                </div>

                <div class="text-end d-flex flex-column align-items-end gap-2 ms-auto">
                    <div class="fw-bold">Rp {{ number_format($p->price * $item->quantity, 0, ',', '.') }}</div>
                    <form method="POST" action="{{ route('cart.destroy', $item) }}">
                        @csrf @method('DELETE')
                        <button class="kl-icon-btn" aria-label="Hapus {{ $p->name }} dari keranjang"><x-icon name="trash" :size="18" /></button>
                    </form>
                </div>
            </div>
        @endforeach
        <a href="{{ route('products.index') }}" class="btn btn-link ps-0"><x-icon name="chevron-left" :size="18" /> Tambah menu lain</a>
    </div>

    {{-- Ringkasan --}}
    <div class="col-lg-4">
        <div class="kl-card kl-sticky">
            <h2 class="kl-card-title"><x-icon name="receipt" :size="20" /> Ringkasan Pesanan</h2>
            @foreach($items as $item)
                <div class="kl-summary-row">
                    <span class="text-muted">{{ $item->product->name }} &times; {{ $item->quantity }}</span>
                    <span class="fw-semibold">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <div class="kl-summary-total">
                <span>Total</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <div class="small text-muted mt-1">Belum termasuk ongkos kirim.</div>
            <a href="{{ route('checkout.index') }}" class="btn btn-dark btn-lg w-100 mt-3">Lanjut ke checkout</a>
        </div>
    </div>
</div>
@endif
@endsection
