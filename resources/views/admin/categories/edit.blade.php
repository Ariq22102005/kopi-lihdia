@extends('layouts.app')
@section('title', 'Ubah Kategori')

@section('content')
<div class="row justify-content-center"><div class="col-md-6">
    <div class="card shadow-sm"><div class="card-body p-4">
        <h4 class="mb-3">Ubah Kategori</h4>
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf @method('PUT')
            @include('admin.categories._form')
        </form>
    </div></div>
</div></div>
@endsection
