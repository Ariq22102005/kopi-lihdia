<div class="mb-3">
    <label class="form-label">Nama Produk</label>
    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Kategori</label>
    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
        <option value="">-- Pilih --</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Harga (Rp)</label>
        <input type="number" name="price" min="0" value="{{ old('price', $product->price ?? '') }}"
               class="form-control @error('price') is-invalid @enderror" required>
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Stok</label>
        <input type="number" name="stock" min="0" value="{{ old('stock', $product->stock ?? 0) }}"
               class="form-control @error('stock') is-invalid @enderror" required>
        @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Gambar (maks 2 MB)</label>
    @if(!empty($product->image))
        <div class="mb-2"><img src="{{ asset('storage/'.$product->image) }}" width="100" class="rounded"></div>
    @endif
    <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
           @checked(old('is_active', $product->is_active ?? true))>
    <label class="form-check-label" for="is_active">Tampilkan di katalog</label>
</div>

<a href="{{ route('admin.products.index') }}" class="btn btn-light">Batal</a>
<button class="btn btn-dark">Simpan</button>
