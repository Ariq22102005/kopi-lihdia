<div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description ?? '') }}</textarea>
</div>
<a href="{{ route('admin.categories.index') }}" class="btn btn-light">Batal</a>
<button class="btn btn-dark">Simpan</button>
