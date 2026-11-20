@extends('layouts.admin')
@section('title', 'Tambah Kategori')

@section('content')
<a href="{{ route('admin.categories.index') }}" class="kl-back"><x-icon name="chevron-left" :size="18" /> Semua kategori</a>
<div class="kl-pagehead"><div><h1>Tambah Kategori</h1><p>Buat kelompok menu baru.</p></div></div>
<div class="row"><div class="col-xl-7 col-lg-8">
    <div class="ad-card ad-card-pad">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            @include('admin.categories._form')
        </form>
    </div>
</div></div>
@endsection
