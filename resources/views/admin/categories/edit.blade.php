@extends('layouts.admin')
@section('title', 'Ubah Kategori')

@section('content')
<a href="{{ route('admin.categories.index') }}" class="kl-back"><x-icon name="chevron-left" :size="18" /> Semua kategori</a>
<div class="kl-pagehead"><div><h1>Ubah Kategori</h1><p>{{ $category->name }}</p></div></div>
<div class="row"><div class="col-xl-7 col-lg-8">
    <div class="ad-card ad-card-pad">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf @method('PUT')
            @include('admin.categories._form')
        </form>
    </div>
</div></div>
@endsection
