@extends('layouts.app')
@section('title', $product->name)

@section('content')
<a href="{{ route('products.index') }}" class="text-decoration-none">&larr; Kembali ke menu</a>

<div class="row g-4 mt-1">
    <div class="col-md-5">
        <div class="kl-product-img" style="border:1.5px solid var(--kl-green); border-radius:28px;">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
            @else
                ☕
            @endif
        </div>
    </div>
    <div class="col-md-7">
        <span class="badge rounded-pill text-bg-light border">{{ $product->category->name }}</span>
        <h2 class="fw-bold mt-2">{{ $product->name }}</h2>
        <div class="fs-3 fw-bold" style="color: var(--kl-green)">
            Rp {{ number_format($product->price, 0, ',', '.') }}
        </div>

        <p class="mt-3">{{ $product->description }}</p>

        <p class="mb-3">
            @if($product->stock <= 0)
                <span class="badge text-bg-danger">Stok habis</span>
            @else
                <span class="text-muted">Stok tersedia: {{ $product->stock }}</span>
            @endif
        </p>

        @if($product->stock > 0)
            <form method="POST" action="{{ route('cart.store', $product) }}" class="d-flex align-items-center gap-2">
                @csrf
                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                       class="form-control rounded-pill text-center" style="width:90px">
                <button class="btn btn-dark rounded-pill px-4 py-2">Tambah ke keranjang</button>
            </form>
        @else
            <button class="btn btn-dark rounded-pill px-4 py-2" disabled>Stok habis</button>
        @endif
    </div>
</div>

@if($related->isNotEmpty())
    <h5 class="fw-bold mt-5 mb-3">Menu lain di {{ $product->category->name }}</h5>
    <div class="row g-3">
        @foreach($related as $item)
            <div class="col-6 col-md-3">
                @include('products._card', ['product' => $item])
            </div>
        @endforeach
    </div>
@endif
@endsection
