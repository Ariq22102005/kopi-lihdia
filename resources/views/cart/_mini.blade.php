@php
    $items = $items ?? collect();
    $subtotal = $subtotal ?? 0;
    $count = $items->sum('quantity');
@endphp

@guest
    <div class="kl-empty py-3">
        <span class="kl-empty-ic"><x-icon name="cart" :size="30" /></span>
        <h6 class="mb-1">Pesanan Anda</h6>
        <p class="small mb-3">Masuk untuk mulai memesan menu favorit.</p>
        <a href="{{ route('login') }}" class="btn btn-dark w-100 mb-2">Masuk</a>
        <a href="{{ route('register') }}" class="btn btn-outline-dark w-100">Daftar</a>
    </div>
@else
    @if(auth()->user()->isAdmin())
        <div class="kl-empty small">
            <span class="kl-empty-ic"><x-icon name="grid" :size="28" /></span>
            <div>Akun admin tidak dapat berbelanja.<br>Kelola menu dari Dashboard.</div>
        </div>
    @else
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h5 fw-bold mb-0">Pesanan Anda</h2>
            <span class="kl-count-pill">{{ $count }} item</span>
        </div>

        @forelse($items as $item)
            @php
                $p = $item->product;
                $isLast = $item->quantity <= 1;
            @endphp
            <div class="kl-mini-item">
                <div class="kl-mini-thumb">
                    @if($p->image)
                        <img src="{{ asset('img/'.$p->image) }}" alt="">
                    @else
                        <img src="{{ asset('img/logo-icon-green.png') }}" alt="" class="kl-ph">
                    @endif
                </div>
                <div class="flex-grow-1" style="min-width:0">
                    <div class="kl-mini-name text-truncate">{{ $p->name }}</div>
                    <div class="small text-muted">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                    @if(! $p->is_active || $p->stock < $item->quantity)
                        <div class="small fw-semibold text-danger">Stok tidak cukup</div>
                    @endif
                    <div class="kl-mini-qty mt-1">
                        <button type="button" class="kl-qty-btn"
                                data-cart-action="{{ $isLast ? 'remove' : 'update' }}"
                                data-url="{{ $isLast ? route('cart.destroy', $item) : route('cart.update', $item) }}"
                                data-qty="{{ $item->quantity - 1 }}"
                                aria-label="{{ $isLast ? 'Hapus '.$p->name : 'Kurangi '.$p->name }}">
                            <x-icon :name="$isLast ? 'trash' : 'minus'" :size="14" />
                        </button>
                        <span class="fw-bold">{{ $item->quantity }}</span>
                        <button type="button" class="kl-qty-btn"
                                data-cart-action="update"
                                data-url="{{ route('cart.update', $item) }}"
                                data-qty="{{ $item->quantity + 1 }}"
                                aria-label="Tambah {{ $p->name }}"
                                @disabled($item->quantity >= $p->stock)><x-icon name="plus" :size="14" /></button>
                    </div>
                </div>
                <div class="fw-bold small text-end">
                    Rp {{ number_format($p->price * $item->quantity, 0, ',', '.') }}
                </div>
            </div>
        @empty
            <div class="kl-empty">
                <span class="kl-empty-ic"><x-icon name="receipt" :size="30" /></span>
                <h6 class="mb-1">Belum ada pesanan</h6>
                <div class="small">Tekan tombol + pada menu untuk menambahkan.</div>
            </div>
        @endforelse

        @if($items->isNotEmpty())
            <div class="kl-summary-total" style="font-size:1.05rem">
                <span>Subtotal</span>
                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="small text-muted mt-1 mb-3">Ongkos kirim dihitung saat checkout.</div>
            <a href="{{ route('checkout.index') }}" class="btn btn-dark w-100">Checkout</a>
            <a href="{{ route('cart.index') }}" class="btn btn-link btn-sm w-100 mt-1">Lihat keranjang lengkap</a>
        @endif
    @endif
@endguest
