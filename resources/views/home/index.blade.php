@extends('layouts.app')
@section('title', 'Beranda')

@push('head')
{{-- Loader tampil sekali per sesi. Diputuskan sebelum halaman digambar agar tidak berkedip. --}}
<script>
    (function () {
        var d = document.documentElement;
        d.classList.add('js');
        try {
            if (!sessionStorage.getItem('kl-seen') && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
                d.classList.add('kl-loading');
            } else {
                d.classList.add('kl-ready');
            }
        } catch (e) { d.classList.add('kl-ready'); }
    })();
</script>
@endpush

@section('hero')
<section id="beranda" class="kl-hero2">
    <div class="container kl-hero2-in">
        <div class="kl-hero2-text">
            <h1 class="kl-in" style="--d:0">Segelas Kebaikan,<br>Langsung dari Alam.</h1>
            <p class="kl-in" style="--d:1">Nikmati kehangatan dan kesegaran kopi pilihan terbaik dari <strong>KopiLihdia</strong>, diseduh dengan cinta untuk menemani hari Anda.</p>
            <div class="kl-hero2-cta kl-in" style="--d:2">
                <a href="{{ route('products.index') }}" class="kl-btn-white">Pesan sekarang</a>
                <a href="#menu-pilihan" class="kl-textlink">Lihat menu</a>
            </div>
        </div>

        <div class="kl-art kl-in" style="--d:1" data-parallax-wrap aria-hidden="true">
            <div class="kl-art-layer" data-depth="-14"><img class="kl-art-swirl" src="{{ asset('img/hero-art-swirl.svg') }}" alt=""></div>
            <div class="kl-art-layer" data-depth="10"><img class="kl-art-leaves" src="{{ asset('img/hero-art-leaves.svg') }}" alt=""></div>
            <div class="kl-art-core"><img src="{{ asset('img/logo-icon-white.png') }}" alt=""></div>
        </div>
    </div>

    <a href="#info" class="kl-mouse" aria-label="Gulir ke bawah"><span></span></a>

    <nav class="container kl-hero2-links kl-in" style="--d:3" aria-label="Pintasan">
        <a href="#menu-pilihan"><small>01</small><strong>Menu pilihan</strong><span>Kopi, non-kopi, dan camilan.</span><i aria-hidden="true">&rarr;</i></a>
        <a href="#suasana"><small>02</small><strong>Suasana</strong><span>Intip sudut-sudut tempat kami.</span><i aria-hidden="true">&rarr;</i></a>
        <a href="#reservasi"><small>03</small><strong>Reservasi</strong><span>Pesan tempat duduk untuk kunjunganmu.</span><i aria-hidden="true">&rarr;</i></a>
    </nav>
</section>
@endsection

@section('content')
@php
    $c = config('kopilihdia');
@endphp

{{-- Loader --}}
<div id="kl-loader" class="kl-loader" aria-hidden="true">
    <div class="kl-loader-in">
        <div class="kl-loader-logo">
            <span class="kl-loader-ring"></span>
            <img src="{{ asset('img/logo-stacked-white.png') }}" alt="" width="260">
        </div>
        <div class="kl-loader-bar"><span></span></div>
    </div>
</div>
<div class="kl-progress" id="kl-progress" aria-hidden="true"></div>

{{-- Info singkat --}}
<section id="info" class="kl-inforow" data-reveal>
    <div><small>Jam buka</small><strong>{{ $c['hours'] }}</strong></div>
    <div><small>Lokasi</small><strong>{{ $c['address'] }}</strong></div>
    <div><small>Pesan online</small><strong>Diantar ke alamatmu</strong></div>
</section>

{{-- Fasilitas --}}
<section id="fasilitas" class="kl-section">
    <header class="kl-sechead" data-reveal>
        <span class="kl-label"><b>01</b> Fasilitas</span>
        <h2>Tempat yang bikin betah</h2>
        <p>Dari kerja sampai kumpul bareng teman, semua yang kamu butuhkan sudah ada.</p>
    </header>

    <div class="kl-facs">
        @foreach($c['facilities'] as $i => $f)
            @php $hasImg = ! empty($f['image']) && file_exists(public_path('img/'.$f['image'])); @endphp
            <article @class(['kl-fac2', 'has-img' => $hasImg]) data-reveal style="--d: {{ $i % 3 }}">
                @if($hasImg)
                    <figure class="kl-fac2-img"><img src="{{ asset('img/'.$f['image']) }}" alt="{{ $f['title'] }}" loading="lazy"></figure>
                @endif
                <span class="kl-fac2-n">{{ sprintf('%02d', $i + 1) }}</span>
                <h3>{{ $f['title'] }}</h3>
                <p>{{ $f['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

{{-- Menu pilihan --}}
<section id="menu-pilihan" class="kl-section">
    <header class="kl-sechead" data-reveal>
        <span class="kl-label"><b>02</b> Menu</span>
        <h2>Apa yang bisa kamu pesan</h2>
        <p>Pilih kategori, tambahkan ke keranjang, dan kami siapkan.</p>
    </header>

    @if($products->isEmpty())
        <div class="kl-empty" data-reveal>
            <span class="kl-empty-ic"><x-icon name="cup" :size="30" /></span>
            <h5>Menu segera hadir</h5>
            <p class="mb-0">Belum ada menu yang tersedia saat ini.</p>
        </div>
    @else
        <div class="kl-chips justify-content-md-center mb-3" id="menu-tabs" role="tablist" aria-label="Kategori menu" data-reveal>
            <button type="button" class="kl-chip active" data-filter="all" role="tab" aria-selected="true">
                Semua
            </button>
            @foreach($categories as $cat)
                <button type="button" class="kl-chip" data-filter="{{ $cat->slug }}" role="tab" aria-selected="false">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        <div class="row g-3 justify-content-center" id="menu-grid">
            @foreach($products as $product)
                <div class="col-6 col-md-4 col-lg-3" data-cat="{{ $product->category?->slug }}">
                    @include('products._card', ['product' => $product, 'cartQty' => $cartQty])
                </div>
            @endforeach
        </div>

        <div class="text-center mt-4" data-reveal>
            <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-lg kl-btn-arrow">Lihat semua menu <x-icon name="arrow-right" :size="18" /></a>
        </div>
    @endif
</section>

{{-- Galeri suasana --}}
<section id="suasana" class="kl-section">
    <header class="kl-sechead" data-reveal>
        <span class="kl-label"><b>03</b> Suasana</span>
        <h2>Intip suasana tempat kami</h2>
        <p>Sudut-sudut favorit yang sering jadi tempat cerita dimulai.</p>
    </header>

    <div class="kl-gallery">
        @foreach($c['gallery'] as $i => $g)
            @php $has = file_exists(public_path('img/'.$g['file'])); @endphp
            @if($has)
                <button type="button" class="kl-shot kl-shot-{{ $g['size'] }}" data-reveal style="--d: {{ $i % 3 }}"
                        data-lightbox="{{ asset('img/'.$g['file']) }}" data-caption="{{ $g['alt'] }}" aria-label="Perbesar foto: {{ $g['alt'] }}">
                    <img src="{{ asset('img/'.$g['file']) }}" alt="{{ $g['alt'] }}" loading="lazy">
                    <span class="kl-shot-cap">{{ $g['alt'] }}</span>
                </button>
            @else
                <div class="kl-shot kl-shot-ph kl-shot-{{ $g['size'] }}" data-reveal style="--d: {{ $i % 3 }}" role="img" aria-label="Tempat foto: {{ $g['alt'] }}">
                    <span>{{ $g['alt'] }}</span>
                </div>
            @endif
        @endforeach
    </div>

    <div class="kl-ig" data-reveal>
        <div>
            <strong>Lebih banyak cerita di Instagram</strong>
            <span>Ikuti {{ $c['instagram']['handle'] }} untuk info menu baru dan promo.</span>
        </div>
        <a href="{{ $c['instagram']['url'] }}" target="_blank" rel="noopener" class="btn btn-dark btn-lg kl-ig-btn">
            Ikuti {{ $c['instagram']['handle'] }} <span aria-hidden="true">&nearr;</span>
        </a>
    </div>
</section>

<dialog id="kl-lightbox" class="kl-lightbox" aria-label="Pratinjau foto">
    <button type="button" class="kl-lb-close" data-lb="close" aria-label="Tutup"><x-icon name="x" :size="22" /></button>
    <button type="button" class="kl-lb-nav kl-lb-prev" data-lb="prev" aria-label="Sebelumnya"><x-icon name="chevron-left" :size="24" /></button>
    <figure>
        <img src="" alt="">
        <figcaption></figcaption>
    </figure>
    <button type="button" class="kl-lb-nav kl-lb-next" data-lb="next" aria-label="Berikutnya"><x-icon name="chevron-right" :size="24" /></button>
</dialog>

{{-- Testimoni --}}
<section id="testimoni" class="kl-section">
    <header class="kl-sechead" data-reveal>
        <span class="kl-label"><b>04</b> Testimoni</span>
        <h2>Kata mereka tentang kami</h2>
        <p>Cerita dari pelanggan yang sudah mampir dan balik lagi.</p>
    </header>

    <div class="kl-slider" id="kl-slider" data-reveal aria-roledescription="carousel" aria-label="Testimoni pelanggan">
        <div class="kl-slider-track" tabindex="0">
            @foreach($c['testimonials'] as $t)
                <figure class="kl-testi" aria-roledescription="slide">
                    <span class="kl-testi-q" aria-hidden="true">&ldquo;</span>
                    @if($t['sample'] ?? false)<span class="kl-sample">Contoh</span>@endif
                    <div class="kl-stars" role="img" aria-label="Rating {{ $t['rating'] }} dari 5">
                        @for($i = 1; $i <= 5; $i++)<span class="{{ $i <= $t['rating'] ? 'on' : '' }}">&#9733;</span>@endfor
                    </div>
                    <blockquote>{{ $t['text'] }}</blockquote>
                    <figcaption>
                        <span class="kl-testi-av" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($t['name'], 0, 1)) }}</span>
                        <span><strong>{{ $t['name'] }}</strong><small>{{ $t['role'] }}</small></span>
                    </figcaption>
                </figure>
            @endforeach
        </div>

        <div class="kl-slider-ctrl">
            <button type="button" class="kl-lb-nav-lite" data-slide="prev" aria-label="Testimoni sebelumnya"><x-icon name="chevron-left" :size="20" /></button>
            <div class="kl-dots" role="tablist" aria-label="Pilih halaman testimoni"></div>
            <button type="button" class="kl-lb-nav-lite" data-slide="next" aria-label="Testimoni berikutnya"><x-icon name="chevron-right" :size="20" /></button>
        </div>
    </div>
</section>

{{-- Reservasi --}}
@php
    $done = session('reservation');
    $oldDate = old('date');
    $oldTime = old('time');
@endphp
<section id="reservasi" class="kl-section" @if($done || $errors->any()) data-scroll-to @endif>
    <header class="kl-sechead" data-reveal>
        <span class="kl-label"><b>05</b> Reservasi</span>
        <h2>Pesan tempat duduk</h2>
        <p>Rencanakan kunjunganmu. Isi form singkat ini, kami hubungi untuk konfirmasi.</p>
    </header>

    <div class="kl-rsv" data-reveal>
        <aside class="kl-rsv-side">
            <h3>Cara kerjanya</h3>
            <ol class="kl-rsv-steps">
                <li><span>1</span><div><strong>Isi form</strong><small>Pilih tanggal, jam, dan jumlah orang.</small></div></li>
                <li><span>2</span><div><strong>Kami konfirmasi</strong><small>Tim kami menghubungi lewat nomor yang kamu isi.</small></div></li>
                <li><span>3</span><div><strong>Datang dan nikmati</strong><small>Meja sudah menunggumu.</small></div></li>
            </ol>
            <div class="kl-rsv-meta">
                <div>{{ $c['hours'] }}</div>
                <div>{{ $c['phone'] }}</div>
                <div>Maksimal {{ $rsvCfg['max_guests'] }} orang per reservasi</div>
            </div>
        </aside>

        <div class="kl-rsv-main">
            @if($done)
                <div class="kl-success" role="status">
                    <svg class="kl-success-ic" viewBox="0 0 52 52" aria-hidden="true"><circle cx="26" cy="26" r="24"/><path d="m15 27 8 8 15-17"/></svg>
                    <h3>Reservasi terkirim</h3>
                    <p>Terima kasih, {{ $done['name'] }}. Kami akan menghubungi {{ $done['phone'] }} untuk konfirmasi.</p>
                    <div class="kl-code">{{ $done['code'] }}</div>
                    <dl class="kl-rsv-sum">
                        <div><dt>Tanggal</dt><dd>{{ $done['date'] }}</dd></div>
                        <div><dt>Jam</dt><dd>{{ $done['time'] }}</dd></div>
                        <div><dt>Jumlah</dt><dd>{{ $done['guests'] }} orang</dd></div>
                    </dl>
                    <a href="{{ route('home') }}#reservasi" class="btn btn-outline-dark">Buat reservasi lain</a>
                </div>
            @else
                <form method="POST" action="{{ route('reservations.store') }}" id="rsv-form" data-avail-url="{{ route('reservations.availability') }}" novalidate>
                    @csrf

                    @if($errors->any())
                        <div class="alert alert-danger" role="alert"><x-icon name="alert" :size="20" /><div>Ada isian yang perlu diperbaiki. Cek pesan di bawah kolom yang bertanda merah.</div></div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="rsv-name">Nama</label>
                            <input type="text" id="rsv-name" name="name" autocomplete="name" value="{{ old('name', auth()->user()?->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="rsv-phone">No. telepon / WhatsApp</label>
                            <input type="tel" id="rsv-phone" name="phone" autocomplete="tel" inputmode="tel" value="{{ old('phone', auth()->user()?->phone) }}"
                                   class="form-control @error('phone') is-invalid @enderror" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-7">
                            <label class="form-label" for="rsv-date">Tanggal</label>
                            <input type="date" id="rsv-date" name="date" min="{{ $minDate }}" max="{{ $maxDate }}" value="{{ $oldDate }}"
                                   class="form-control @error('date') is-invalid @enderror" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="rsv-guests">Jumlah orang</label>
                            <div class="kl-stepper" data-stepper>
                                <button type="button" data-step="-1" aria-label="Kurangi jumlah orang"><x-icon name="minus" :size="18" /></button>
                                <input type="number" id="rsv-guests" name="guests" value="{{ old('guests', 2) }}" min="1" max="{{ $rsvCfg['max_guests'] }}" aria-label="Jumlah orang">
                                <button type="button" data-step="1" aria-label="Tambah jumlah orang"><x-icon name="plus" :size="18" /></button>
                            </div>
                            @error('guests')<div class="text-danger small fw-medium mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <span class="form-label d-block" id="rsv-time-label">Jam kedatangan</span>
                            <div class="kl-slots" role="radiogroup" aria-labelledby="rsv-time-label">
                                @foreach($slots as $slot)
                                    <label class="kl-slot">
                                        <input type="radio" name="time" value="{{ $slot }}" data-slot="{{ $slot }}" @checked($oldTime === $slot)>
                                        <span>{{ $slot }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('time')<div class="text-danger small fw-medium mt-2">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="rsv-notes">Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                            <textarea id="rsv-notes" name="notes" rows="2" maxlength="300" placeholder="Contoh: ulang tahun, butuh kursi bayi, area outdoor"
                                      class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <button class="btn btn-dark btn-lg w-100" data-loading-text="Mengirim...">Kirim reservasi</button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('js/home.js') }}?v=6" defer></script>
@endpush
