<div class="row g-4">
    <div class="col-lg-8">
        <div class="ad-card ad-card-pad">
            <h2 class="ad-card-title"><x-icon name="cup" :size="18" /> Detail menu</h2>

            <div class="mb-3">
                <label class="form-label" for="p-name">Nama Produk</label>
                <input type="text" id="p-name" name="name" value="{{ old('name', $product->name ?? '') }}"
                       class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="p-cat">Kategori</label>
                <select id="p-cat" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="p-desc">Deskripsi</label>
                <textarea id="p-desc" name="description" rows="4" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="p-price">Harga (Rp)</label>
                    <input type="number" id="p-price" name="price" min="0" value="{{ old('price', $product->price ?? '') }}"
                           class="form-control @error('price') is-invalid @enderror" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="p-stock">Stok</label>
                    <input type="number" id="p-stock" name="stock" min="0" value="{{ old('stock', $product->stock ?? 0) }}"
                           class="form-control @error('stock') is-invalid @enderror" required>
                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ad-card ad-card-pad mb-4">
            <h2 class="ad-card-title"><x-icon name="box" :size="18" /> Gambar</h2>
            <div class="ad-upload">
                @if(!empty($product->image))
                    <img src="{{ asset('img/'.$product->image) }}" alt="" id="p-preview">
                @else
                    <div class="ad-ph" id="p-ph"><img src="{{ asset('img/logo-icon-green.png') }}" alt=""></div>
                    <img src="" alt="" id="p-preview" hidden>
                @endif
                <input type="file" name="image" id="p-file" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <div class="form-text mt-2">Maksimal 2 MB. JPG atau PNG.</div>
            </div>
        </div>

        <div class="ad-card ad-card-pad">
            <h2 class="ad-card-title"><x-icon name="check" :size="18" /> Visibilitas</h2>
            <div class="form-check form-switch">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" role="switch"
                       @checked(old('is_active', $product->is_active ?? true))>
                <label class="form-check-label" for="is_active">Tampilkan di katalog</label>
            </div>
        </div>
    </div>
</div>

<div class="ad-actions">
    <a href="{{ route('admin.products.index') }}" class="btn btn-light">Batal</a>
    <button class="btn btn-dark"><x-icon name="check" :size="18" /> Simpan</button>
</div>

@push('scripts')
<script>
    document.getElementById('p-file').addEventListener('change', function (e) {
        var f = e.target.files[0]; if (!f) return;
        var img = document.getElementById('p-preview'), ph = document.getElementById('p-ph');
        img.src = URL.createObjectURL(f); img.hidden = false; if (ph) ph.hidden = true;
    });
</script>
@endpush
