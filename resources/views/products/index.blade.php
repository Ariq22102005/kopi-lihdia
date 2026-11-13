@extends('layouts.app')
@section('title', 'Menu')

@section('content')
{{-- Pencarian --}}
<form method="GET" action="{{ route('products.index') }}" class="mb-4">
    @if($activeCategory)
        <input type="hidden" name="kategori" value="{{ $activeCategory }}">
    @endif
    <div class="kl-search d-flex align-items-center">
        <span class="me-2">🔍</span>
        <input type="text" name="q" value="{{ $search }}" placeholder="Cari menu..."
               class="form-control border-0 shadow-none bg-transparent">
        <button class="btn btn-dark rounded-pill px-4">Cari</button>
    </div>
</form>

{{-- Filter kategori --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('products.index', array_filter(['q' => $search])) }}"
           class="kl-cat @if(!$activeCategory) active @endif">
            <div class="kl-cat-name">Semua Menu</div>
            <div class="kl-cat-count">{{ $totalActive }} item</div>
        </a>
    </div>
    @foreach($categories as $c)
        <div class="col-6 col-md-3">
            <a href="{{ route('products.index', array_filter(['kategori' => $c->slug, 'q' => $search])) }}"
               class="kl-cat @if($activeCategory === $c->slug) active @endif">
                <div class="kl-cat-name">{{ $c->name }}</div>
                <div class="kl-cat-count">{{ $c->products_count }} item</div>
            </a>
        </div>
    @endforeach
</div>

@if($search !== '')
    <p class="text-muted">Hasil untuk "<strong>{{ $search }}</strong>" ({{ $products->total() }} menu)</p>
@endif

{{-- Daftar produk --}}
<div class="row g-3">
    @forelse($products as $product)
        <div class="col-6 col-md-4 col-lg-3">
            @include('products._card', ['product' => $product])
        </div>
    @empty
        <div class="col-12">
            <div class="text-center text-muted py-5">Menu tidak ditemukan.</div>
        </div>
    @endforelse
</div>

<div class="mt-4">{{ $products->links('pagination::bootstrap-5') }}</div>
@endsection
