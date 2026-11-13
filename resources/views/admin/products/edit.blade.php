@extends('layouts.app')
@section('title', 'Ubah Produk')

@section('content')
<div class="row justify-content-center"><div class="col-md-8">
    <div class="card shadow-sm"><div class="card-body p-4">
        <h4 class="mb-3">Ubah Produk</h4>
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('admin.products._form')
        </form>
    </div></div>
</div></div>
@endsection
