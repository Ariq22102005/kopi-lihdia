<div class="kl-product position-relative">
    <a href="{{ route('products.show', $product) }}" class="kl-product-img mb-3 text-decoration-none">
        @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
        @else
            ☕
        @endif
    </a>

    @if($product->stock <= 0)
        <span class="badge text-bg-danger kl-badge">Habis</span>
    @elseif($product->stock <= 5)
        <span class="badge text-bg-warning kl-badge">Sisa {{ $product->stock }}</span>
    @endif

    <div class="d-flex justify-content-between align-items-end mt-auto gap-2">
        <div>
            <a href="{{ route('products.show', $product) }}" class="kl-product-name d-block">{{ $product->name }}</a>
            <div class="kl-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
        </div>
        {{-- Tahap 6: tombol ini diganti form "tambah ke keranjang" --}}
        <a href="{{ route('products.show', $product) }}"
           class="kl-add @if($product->stock <= 0) disabled @endif" aria-label="Lihat {{ $product->name }}">+</a>
    </div>
</div>
