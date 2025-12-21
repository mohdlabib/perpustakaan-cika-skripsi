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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Barcode</label>
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
                </div>
                
                <!-- Rak Penyimpanan - Full Width dengan Searchable Dropdown -->
                <div class="mt-6" x-data="shelfSelector()">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        Rak Penyimpanan
                    </label>
                    <div class="relative">
                        <input type="hidden" name="shelf_id" x-model="selectedId">
                        <div class="relative">
                            <input type="text" 
                                x-model="search"
                                @focus="showDropdown = true"
                                @click.away="showDropdown = false"
                                @keydown.escape="showDropdown = false"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition pr-10"
                                placeholder="Cari atau pilih rak...">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Dropdown -->
                        <div x-show="showDropdown && filteredShelves.length > 0" x-cloak
                            class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                            <template x-for="shelf in filteredShelves" :key="shelf.id">
                                <div @click="selectShelf(shelf)" 
                                    class="px-4 py-3 hover:bg-primary-light cursor-pointer transition border-b border-gray-50 last:border-b-0">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-800" x-text="shelf.code"></span>
                                            <span class="text-gray-600" x-text="' - ' + shelf.name"></span>
                                            <p class="text-xs text-gray-400" x-text="shelf.location || 'Lokasi tidak ditentukan'"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- No results -->
                        <div x-show="showDropdown && search.length > 0 && filteredShelves.length === 0" x-cloak
                            class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl p-4 text-center text-gray-500">
                            Tidak ada rak yang ditemukan
                        </div>
                    </div>
                    
                    <!-- Selected Shelf Display -->
                    <div x-show="selectedId" class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-yellow-800" x-text="selectedName"></p>
                                <p class="text-xs text-yellow-600" x-text="selectedLocation"></p>
                            </div>
                        </div>
                        <button type="button" @click="clearSelection()" class="text-yellow-600 hover:text-yellow-800 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Column Dropdown (shows after selecting shelf) -->
                    <div x-show="selectedId && columns.length > 0" class="mt-3">
                        <input type="hidden" name="shelf_column_id" x-model="selectedColumnId">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kolom Rak</label>
                        <select x-model="selectedColumnId" 
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark cursor-pointer">
                            <option value="">Pilih Kolom</option>
                            <template x-for="col in columns" :key="col.id">
                                <option :value="col.id" x-text="'Kolom ' + col.name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <!-- No columns message -->
                    <div x-show="selectedId && columns.length === 0 && !loadingColumns" class="mt-3 text-sm text-gray-500 italic">
                        Rak ini belum memiliki kolom
                    </div>
                    
                    <!-- Loading columns -->
                    <div x-show="loadingColumns" class="mt-3 text-sm text-gray-500 flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Memuat kolom...
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
                                <img src="{{ $book->cover_url }}" class="w-24 h-32 rounded-lg object-cover shadow border">
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
function shelfSelector() {
    return {
        shelves: @json($shelves ?? []),
        search: '',
        showDropdown: false,
        selectedId: '{{ old('shelf_id', $book->shelf_id ?? '') }}',
        selectedName: '',
        selectedLocation: '',
        columns: [],
        selectedColumnId: '{{ old('shelf_column_id', $book->shelf_column_id ?? '') }}',
        loadingColumns: false,
        
        init() {
            if (this.selectedId) {
                const shelf = this.shelves.find(s => s.id == this.selectedId);
                if (shelf) {
                    this.selectedName = `${shelf.code} - ${shelf.name}`;
                    this.selectedLocation = shelf.location || 'Lokasi tidak ditentukan';
                    this.search = this.selectedName;
                    this.fetchColumns(this.selectedId);
                }
            }
        },
        
        get filteredShelves() {
            if (!this.search) return this.shelves;
            const query = this.search.toLowerCase();
            return this.shelves.filter(shelf => 
                shelf.code.toLowerCase().includes(query) ||
                shelf.name.toLowerCase().includes(query) ||
                (shelf.location && shelf.location.toLowerCase().includes(query))
            );
        },
        
        async fetchColumns(shelfId) {
            this.loadingColumns = true;
            try {
                const response = await fetch(`/admin/shelves/${shelfId}/columns`);
                this.columns = await response.json();
            } catch (error) {
                console.error('Error fetching columns:', error);
                this.columns = [];
            }
            this.loadingColumns = false;
        },
        
        selectShelf(shelf) {
            this.selectedId = shelf.id;
            this.selectedName = `${shelf.code} - ${shelf.name}`;
            this.selectedLocation = shelf.location || 'Lokasi tidak ditentukan';
            this.search = this.selectedName;
            this.showDropdown = false;
            this.selectedColumnId = '';
            this.fetchColumns(shelf.id);
        },
        
        clearSelection() {
            this.selectedId = '';
            this.selectedName = '';
            this.selectedLocation = '';
            this.search = '';
            this.columns = [];
            this.selectedColumnId = '';
        }
    }
}

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

