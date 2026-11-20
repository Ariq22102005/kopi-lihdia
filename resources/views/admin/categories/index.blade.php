@extends('layouts.admin')
@section('title', 'Kategori')

@section('content')
<div class="kl-pagehead">
    <div><h1>Kategori</h1><p>Kelompokkan menu agar mudah dicari pelanggan.</p></div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-dark"><x-icon name="plus" :size="18" /> Tambah kategori</a>
</div>
<div class="ad-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr><th>Nama</th><th>Slug</th><th>Jumlah Produk</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($categories as $c)
                <tr>
                    <td class="fw-semibold">{{ $c->name }}</td>
                    <td class="text-muted">{{ $c->slug }}</td>
                    <td><span class="kl-status kl-st-success">{{ $c->products_count }} produk</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-outline-secondary"><x-icon name="edit" :size="16" /> Ubah</a>
                        <form action="{{ route('admin.categories.destroy', $c) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><x-icon name="trash" :size="16" /> Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-5">Belum ada kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $categories->links('pagination::bootstrap-5') }}</div>
@endsection
