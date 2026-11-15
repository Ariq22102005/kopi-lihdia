@php $inCart = ($cartQty ?? [])[$product->id] ?? 0; @endphp
<div class="kl-product" data-product-card="{{ $product->id }}">
    <span class="kl-incart" data-incart @if(! $inCart) style="display:none" @endif>{{ $inCart }}</span>

    @if($product->stock <= 0)
        <span class="badge text-bg-danger kl-badge">Habis</span>
    @elseif($product->stock <= 5)
        <span class="badge text-bg-warning kl-badge">Sisa {{ $product->stock }}</span>
    @endif

    <a href="{{ route('products.show', $product) }}" class="kl-product-img mb-3 text-decoration-none">
        @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <img src="{{ asset('img/logo-icon-green.png') }}" alt="" class="kl-ph">
        @endif
    </a>

    <div class="d-flex justify-content-between align-items-end mt-auto gap-2">
        <div style="min-width:0">
            <a href="{{ route('products.show', $product) }}" class="kl-product-name d-block">{{ $product->name }}</a>
            <div class="kl-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
        </div>
        <form method="POST" action="{{ route('cart.store', $product) }}" data-ajax-add>
            @csrf
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="kl-add @if($inCart) kl-add-on @endif"
                    aria-label="Tambah {{ $product->name }} ke keranjang"
                    @disabled($product->stock <= 0)>+</button>
        </form>
    </div>
</div>
