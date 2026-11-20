@extends('layouts.app')
@section('title', $product->name)

@section('content')
<a href="{{ route('products.index', ['kategori' => $product->category->slug]) }}" class="kl-back">
    <x-icon name="chevron-left" :size="18" /> Kembali ke menu
</a>

<div class="row g-4 g-lg-5 mt-0">
    <div class="col-md-5">
        <div class="kl-product-img kl-detail-img">
            @if($product->image)
                <img src="{{ asset('img/'.$product->image) }}" alt="{{ $product->name }}">
            @else
                <img src="{{ asset('img/logo-icon-green.png') }}" alt="" class="kl-ph">
            @endif
        </div>
    </div>
    <div class="col-md-7">
        <a href="{{ route('products.index', ['kategori' => $product->category->slug]) }}" class="kl-tag">{{ $product->category->name }}</a>
        <h1 class="fw-bold mt-3 mb-2" style="font-size: clamp(1.6rem, 3vw, 2.2rem)">{{ $product->name }}</h1>
        <div class="kl-price mb-3" style="font-size: 1.7rem">Rp {{ number_format($product->price, 0, ',', '.') }}</div>

        @if($product->description)
            <p class="mb-3" style="max-width: 60ch">{{ $product->description }}</p>
        @endif

        <p class="mb-4">
            @if($product->stock <= 0)
                <span class="kl-stock out">Stok habis</span>
            @elseif($product->stock <= 5)
                <span class="kl-stock low">Tinggal {{ $product->stock }}, pesan sebelum habis</span>
            @else
                <span class="kl-stock">Stok tersedia: {{ $product->stock }}</span>
            @endif
        </p>

        @if($product->stock > 0)
            <form method="POST" action="{{ route('cart.store', $product) }}" data-ajax-add class="d-flex flex-wrap align-items-center gap-3">
                @csrf
                <div class="kl-stepper" data-stepper>
                    <button type="button" data-step="-1" aria-label="Kurangi jumlah"><x-icon name="minus" :size="18" /></button>
                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" aria-label="Jumlah">
                    <button type="button" data-step="1" aria-label="Tambah jumlah"><x-icon name="plus" :size="18" /></button>
                </div>
                <button type="submit" class="btn btn-dark btn-lg"><x-icon name="cart" :size="20" /> Tambah ke keranjang</button>
            </form>
        @else
            <button class="btn btn-dark btn-lg" disabled>Stok habis</button>
        @endif

        <div class="kl-assure">
            <div><x-icon name="shield" :size="20" /> Dibayar aman lewat Midtrans</div>
            <div><x-icon name="truck" :size="20" /> Diantar ke alamat Anda</div>
        </div>
    </div>
</div>

@if($related->isNotEmpty())
    <h2 class="h5 fw-bold mt-5 mb-3">Menu lain di {{ $product->category->name }}</h2>
    <div class="row g-3">
        @foreach($related as $item)
            <div class="col-6 col-md-3">
                @include('products._card', ['product' => $item->setRelation('category', $product->category), 'cartQty' => $cartQty])
            </div>
        @endforeach
    </div>
@endif
@endsection
