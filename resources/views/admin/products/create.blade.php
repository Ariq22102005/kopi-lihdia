@extends('layouts.app')
@section('title', 'Tambah Produk')

@section('content')
<div class="row justify-content-center"><div class="col-md-8">
    <div class="card shadow-sm"><div class="card-body p-4">
        <h4 class="mb-3">Tambah Produk</h4>
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.products._form')
        </form>
    </div></div>
</div></div>
@endsection
