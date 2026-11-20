@extends('layouts.admin')
@section('title', 'Tambah Produk')

@section('content')
<a href="{{ route('admin.products.index') }}" class="kl-back"><x-icon name="chevron-left" :size="18" /> Semua produk</a>
<div class="kl-pagehead"><div><h1>Tambah Produk</h1><p>Isi detail menu baru.</p></div></div>
<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.products._form')
</form>
@endsection
