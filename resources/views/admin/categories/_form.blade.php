<div class="mb-3">
    <label class="form-label" for="c-name">Nama</label>
    <input type="text" id="c-name" name="name" value="{{ old('name', $category->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label" for="c-desc">Deskripsi</label>
    <textarea id="c-desc" name="description" rows="3" class="form-control">{{ old('description', $category->description ?? '') }}</textarea>
</div>
<div class="ad-actions">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Batal</a>
    <button class="btn btn-dark"><x-icon name="check" :size="18" /> Simpan</button>
</div>
