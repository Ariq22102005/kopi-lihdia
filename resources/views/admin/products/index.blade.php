@extends('layouts.admin')
@section('title', 'Produk')

@section('content')
<div class="kl-pagehead">
    <div><h1>Produk</h1><p>Atur menu, harga, dan stok.</p></div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-dark"><x-icon name="plus" :size="18" /> Tambah produk</a>
</div>
<div class="ad-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($products as $p)
                <tr>
                    <td>
                        @if($p->image)
                            <img src="{{ asset('img/'.$p->image) }}" width="48" height="48" class="kl-thumb" alt="">
                        @else
                            <span class="kl-thumb d-inline-flex align-items-center justify-content-center"><img src="{{ asset('img/logo-icon-green.png') }}" alt="" width="26" style="opacity:.5"></span>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $p->name }}</td>
                    <td>{{ $p->category->name }}</td>
                    <td class="text-nowrap">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                    <td>
                        @if($p->stock <= 0)
                            <span class="kl-status kl-st-danger">Habis</span>
                        @elseif($p->stock <= 5)
                            <span class="kl-status kl-st-warning">Sisa {{ $p->stock }}</span>
                        @else
                            {{ $p->stock }}
                        @endif
                    </td>
                    <td>
                        @if($p->is_active)
                            <span class="kl-status kl-st-success">Aktif</span>
                        @else
                            <span class="kl-status kl-st-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-outline-secondary"><x-icon name="edit" :size="16" /> Ubah</a>
                        <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><x-icon name="trash" :size="16" /> Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">Belum ada produk.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $products->links('pagination::bootstrap-5') }}</div>
@endsection
