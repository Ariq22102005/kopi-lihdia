@extends('layouts.app')
@section('title', 'Keranjang')

@section('content')
<h3 class="fw-bold mb-4">Keranjang Belanja</h3>

@if($items->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem">🛒</div>
        <p class="text-muted mt-2">Keranjang masih kosong.</p>
        <a href="{{ route('products.index') }}" class="btn btn-dark rounded-pill px-4">Lihat Menu</a>
    </div>
@else
<div class="row g-4">
    {{-- Daftar item --}}
    <div class="col-lg-8">
        @foreach($items as $item)
            @php
                $p = $item->product;
                $problem = ! $p->is_active || $p->stock < $item->quantity;
            @endphp
            <div class="kl-product mb-3 flex-row align-items-center gap-3">
                <div class="kl-product-img flex-shrink-0" style="width:84px; height:84px; font-size:2rem;">
                    @if($p->image)
                        <img src="{{ asset('storage/'.$p->image) }}" alt="{{ $p->name }}">
                    @else
                        ☕
                    @endif
                </div>

                <div class="flex-grow-1">
                    <a href="{{ route('products.show', $p) }}" class="kl-product-name">{{ $p->name }}</a>
                    <div class="kl-price">Rp {{ number_format($p->price, 0, ',', '.') }}</div>

                    @if(! $p->is_active)
                        <div class="small text-danger">Menu ini sudah tidak tersedia.</div>
                    @elseif($p->stock < $item->quantity)
                        <div class="small text-danger">Stok tersisa {{ $p->stock }}, kurangi jumlahnya.</div>
                    @endif

                    <div class="kl-qty mt-2">
                        <form method="POST" action="{{ route('cart.update', $item) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="quantity" value="{{ $item->quantity - 1 }}">
                            <button class="btn btn-outline-dark" @disabled($item->quantity <= 1)>&minus;</button>
                        </form>
                        <strong>{{ $item->quantity }}</strong>
                        <form method="POST" action="{{ route('cart.update', $item) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                            <button class="btn btn-outline-dark" @disabled($item->quantity >= $p->stock)>+</button>
                        </form>
                    </div>
                </div>

                <div class="text-end">
                    <div class="fw-bold mb-2">Rp {{ number_format($p->price * $item->quantity, 0, ',', '.') }}</div>
                    <form method="POST" action="{{ route('cart.destroy', $item) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger rounded-pill">Hapus</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Ringkasan --}}
    <div class="col-lg-4">
        <div class="kl-product" style="border-radius:24px;">
            <h5 class="fw-bold mb-3">Ringkasan Pesanan</h5>
            @foreach($items as $item)
                <div class="d-flex justify-content-between small mb-1">
                    <span>{{ $item->product->name }} &times; {{ $item->quantity }}</span>
                    <span>Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <hr>
            <div class="d-flex justify-content-between fs-5 fw-bold mb-3">
                <span>Total</span>
                <span style="color: var(--kl-green)">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn btn-dark rounded-pill py-2 w-100">Checkout</a>
            <a href="{{ route('products.index') }}" class="btn btn-link w-100 mt-1">Tambah menu lain</a>
        </div>
    </div>
</div>
@endif
@endsection
