@extends('layouts.app')
@section('title', 'Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Kategori</h3>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-dark">+ Tambah</a>
</div>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr><th>Nama</th><th>Slug</th><th>Jumlah Produk</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($categories as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td class="text-muted">{{ $c->slug }}</td>
                    <td>{{ $c->products_count }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-outline-secondary">Ubah</a>
                        <form action="{{ route('admin.categories.destroy', $c) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $categories->links('pagination::bootstrap-5') }}</div>
@endsection
