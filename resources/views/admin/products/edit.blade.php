@extends('layouts.admin')
@section('title', 'Ubah Produk')

@section('content')
<a href="{{ route('admin.products.index') }}" class="kl-back"><x-icon name="chevron-left" :size="18" /> Semua produk</a>
<div class="kl-pagehead"><div><h1>Ubah Produk</h1><p>{{ $product->name }}</p></div></div>
<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.products._form')
</form>
@endsection
