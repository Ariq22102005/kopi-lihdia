@php
    $items = $items ?? collect();
    $subtotal = $subtotal ?? 0;
    $count = $items->sum('quantity');
@endphp

@guest
    <div class="text-center py-4">
        <div style="font-size:2.5rem">🛒</div>
        <h6 class="fw-bold mt-2">Pesanan Anda</h6>
        <p class="text-muted small">Masuk untuk mulai memesan menu favorit.</p>
        <a href="{{ route('login') }}" class="btn btn-dark rounded-pill w-100 mb-2">Masuk</a>
        <a href="{{ route('register') }}" class="btn btn-outline-dark rounded-pill w-100">Daftar</a>
    </div>
@else
    @if(auth()->user()->isAdmin())
        <div class="text-center text-muted small py-4">
            Akun admin tidak dapat berbelanja.<br>Kelola menu dari Dashboard.
        </div>
    @else
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Pesanan Anda</h5>
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
                        <img src="{{ asset('storage/'.$p->image) }}" alt="{{ $p->name }}">
                    @else
                        <img src="{{ asset('img/logo-icon-green.png') }}" alt="" class="kl-ph">
                    @endif
                </div>
                <div class="flex-grow-1" style="min-width:0">
                    <div class="kl-mini-name text-truncate">{{ $p->name }}</div>
                    <div class="small text-muted">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                    @if(! $p->is_active || $p->stock < $item->quantity)
                        <div class="small text-danger">Stok tidak cukup</div>
                    @endif
                    <div class="kl-mini-qty mt-1">
                        <button type="button" class="kl-qty-btn"
                                data-cart-action="{{ $isLast ? 'remove' : 'update' }}"
                                data-url="{{ $isLast ? route('cart.destroy', $item) : route('cart.update', $item) }}"
                                data-qty="{{ $item->quantity - 1 }}"
                                aria-label="Kurangi">{!! $isLast ? '&times;' : '&minus;' !!}</button>
                        <span class="fw-semibold">{{ $item->quantity }}</span>
                        <button type="button" class="kl-qty-btn"
                                data-cart-action="update"
                                data-url="{{ route('cart.update', $item) }}"
                                data-qty="{{ $item->quantity + 1 }}"
                                aria-label="Tambah"
                                @disabled($item->quantity >= $p->stock)>+</button>
                    </div>
                </div>
                <div class="fw-semibold small text-end">
                    Rp {{ number_format($p->price * $item->quantity, 0, ',', '.') }}
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">
                <div style="font-size:2rem">🧾</div>
                <div class="small mt-1">Belum ada pesanan.<br>Tekan tombol + pada menu.</div>
            </div>
        @endforelse

        @if($items->isNotEmpty())
            <hr>
            <div class="small text-muted mb-2">Rincian Pembayaran</div>
            <div class="d-flex justify-content-between small mb-1">
                <span>Subtotal</span>
                <span class="fw-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between small text-muted mb-3">
                <span>Ongkos kirim</span>
                <span>dihitung saat checkout</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn btn-dark rounded-pill py-2 w-100">Checkout</a>
            <a href="{{ route('cart.index') }}" class="btn btn-link btn-sm w-100 mt-1">Lihat keranjang lengkap</a>
        @endif
    @endif
@endguest
