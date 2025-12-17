@extends('layouts.admin')

@section('page-title', isset($book) ? 'Edit Buku' : 'Tambah Buku')

@section('content')
<div class="max-w-5xl mx-auto">
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="{{ route('admin.books.index') }}" class="hover:text-primary-dark transition">Kelola Buku</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-medium">{{ isset($book) ? 'Edit Buku' : 'Tambah Buku' }}</li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-primary-dark to-green-700 px-8 py-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ isset($book) ? 'Edit Buku' : 'Tambah Buku Baru' }}</h2>
                    <p class="text-white/70 text-sm">Lengkapi data buku perpustakaan</p>
                </div>
            </div>
        </div>

        <form action="{{ isset($book) ? route('admin.books.update', $book) : route('admin.books.store') }}" 
              method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            @if(isset($book)) @method('PUT') @endif
            
            <!-- Section: Informasi Dasar -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Dasar</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Buku <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $book->title ?? '') }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Masukkan judul buku">
                        @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pengarang <span class="text-red-500">*</span></label>
                        <input type="text" name="author" value="{{ old('author', $book->author ?? '') }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Nama penulis">
                        @error('author')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark cursor-pointer">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cetakan/Edisi</label>
                        <input type="text" name="edition" value="{{ old('edition', $book->edition ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Contoh: Cetakan ke-3">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stok <span class="text-red-500">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock', $book->stock ?? 1) }}" min="0" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                        @error('stock')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
            
            <!-- Section: Identifikasi -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Identifikasi & Kode</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ISBN/ISSN</label>
                        <input type="text" name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="978-xxx-xxx-xxx-x">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Eksemplar</label>
                        <input type="text" name="item_code" value="{{ old('item_code', $book->item_code ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Kode item">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Inventaris</label>
                        <input type="text" name="inventory_code" value="{{ old('inventory_code', $book->inventory_code ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Nomor inventaris">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Klasifikasi</label>
                        <input type="text" name="classification" value="{{ old('classification', $book->classification ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Klasifikasi DDC">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Panggil</label>
                        <input type="text" name="call_number" value="{{ old('call_number', $book->call_number ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Call number">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Rak</label>
                        <input type="text" name="shelf_location" value="{{ old('shelf_location', $book->shelf_location ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Contoh: A-01">
                    </div>
                </div>
            </div>
            
            <!-- Section: Penerbitan -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Penerbitan</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Penerbit</label>
                        <input type="text" name="publisher" value="{{ old('publisher', $book->publisher ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Nama penerbit">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Terbit</label>
                        <input type="number" name="publication_year" value="{{ old('publication_year', $book->publication_year ?? '') }}" min="1900" max="{{ date('Y') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="{{ date('Y') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Terbit</label>
                        <input type="text" name="publication_place" value="{{ old('publication_place', $book->publication_place ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Contoh: Jakarta">
                    </div>
                </div>
            </div>
            
            <!-- Section: Fisik & Penerimaan -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Fisik & Penerimaan</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Fisik</label>
                        <input type="text" name="physical_description" value="{{ old('physical_description', $book->physical_description ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Contoh: xii, 250 hlm; 21 cm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Diterima</label>
                        <input type="date" name="received_date" value="{{ old('received_date', isset($book->received_date) ? $book->received_date->format('Y-m-d') : '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition cursor-pointer">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Buku (Rp)</label>
                        <input type="number" name="price" value="{{ old('price', $book->price ?? '') }}" min="0" step="1000"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="0">
                    </div>
                </div>
            </div>
            
            <!-- Section: Deskripsi -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Deskripsi & Cover</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sinopsis / Deskripsi</label>
                        <textarea name="description" rows="5"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Deskripsi singkat tentang buku...">{{ old('description', $book->description ?? '') }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cover Buku</label>
                        @if(isset($book) && $book->cover_image)
                            <div class="mb-3" id="current-cover">
                                <p class="text-xs text-gray-500 mb-1">Cover saat ini:</p>
                                <img src="{{ Storage::url($book->cover_image) }}" class="w-24 h-32 rounded-lg object-cover shadow border">
                            </div>
                        @endif
                        <div class="relative">
                            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg" id="cover_image_input"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-light file:text-primary-dark file:font-medium cursor-pointer"
                                onchange="previewCover(this)">
                        </div>
                        <!-- Preview new image -->
                        <div id="cover-preview" class="mt-3 hidden">
                            <p class="text-xs text-gray-500 mb-1">Preview:</p>
                            <img id="cover-preview-img" class="w-24 h-32 rounded-lg object-cover shadow border">
                        </div>
                        <p class="text-gray-400 text-sm mt-2">Format: JPG, PNG. Maksimal 2MB.</p>
                        @error('cover_image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-4 pt-6 border-t border-gray-100">
                <button type="submit" class="px-8 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ isset($book) ? 'Simpan Perubahan' : 'Tambah Buku' }}
                </button>
                <a href="{{ route('admin.books.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition flex items-center gap-2 cursor-pointer">
                    Batal
                </a>
            </div>
</form>
    </div>
</div>

@push('scripts')
<script>
function previewCover(input) {
    const preview = document.getElementById('cover-preview');
    const previewImg = document.getElementById('cover-preview-img');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            input.value = '';
            preview.classList.add('hidden');
            return;
        }
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            alert('Format file tidak valid. Gunakan JPG atau PNG.');
            input.value = '';
            preview.classList.add('hidden');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
}
</script>
@endpush
@endsection
