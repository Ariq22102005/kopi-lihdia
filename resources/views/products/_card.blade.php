@php
    $inCart = ($cartQty ?? [])[$product->id] ?? 0;
    $out = $product->stock <= 0;
@endphp
<article class="kl-product @if($out) is-out @endif" data-product-card="{{ $product->id }}" data-tilt>
    <div class="kl-product-img">
        <span class="kl-incart" data-incart title="Sudah di keranjang" @if(! $inCart) style="display:none" @endif><x-icon name="cart" :size="13" /> <b>{{ $inCart }}</b></span>

        <button type="button" class="kl-heart" data-fav="{{ $product->id }}" aria-pressed="false"
                aria-label="Tandai {{ $product->name }} sebagai favorit"><x-icon name="heart" :size="18" /></button>

        @if($out)
            <span class="kl-badge kl-badge-out">Lagi habis</span>
        @elseif($product->stock <= 5)
            <span class="kl-badge kl-badge-low">Tinggal {{ $product->stock }}</span>
        @endif

        <a href="{{ route('products.show', $product) }}" class="d-flex w-100 h-100 align-items-center justify-content-center"
           tabindex="-1" aria-hidden="true">
            @if($product->image)
                <img src="{{ asset('img/'.$product->image) }}" alt="" loading="lazy">
            @else
                <img src="{{ asset('img/logo-icon-green.png') }}" alt="" class="kl-ph">
            @endif
        </a>
    </div>

    <div class="kl-product-body">
        @if($product->relationLoaded('category') && $product->category)
            <span class="kl-product-cat">{{ $product->category->name }}</span>
        @endif
        <a href="{{ route('products.show', $product) }}" class="kl-product-name">{{ $product->name }}</a>

        <div class="kl-product-foot">
            <div class="kl-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
            <form method="POST" action="{{ route('cart.store', $product) }}" data-ajax-add>
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="kl-add @if($inCart) kl-add-on @endif"
                        aria-label="Tambah {{ $product->name }} ke keranjang"
                        @disabled($out)><x-icon name="plus" :size="20" /></button>
            </form>
        </div>
    </div>
</article>
