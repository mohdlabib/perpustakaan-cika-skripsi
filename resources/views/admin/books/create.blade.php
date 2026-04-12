@extends('layouts.admin')

@section('page-title', 'Tambah Buku')

@section('content')
<div class="max-w-5xl mx-auto">
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="{{ route('admin.books.index') }}" class="hover:text-primary-dark transition">Kelola Buku</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-medium">Tambah Buku</li>
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
                    <h2 class="text-xl font-bold">Tambah Buku Baru</h2>
                    <p class="text-white/70 text-sm">Lengkapi data bibliografi dan info eksemplar pertama</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            
            <!-- Section: Informasi Dasar -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Dasar</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Buku <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Masukkan judul buku">
                        @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pengarang <span class="text-red-500">*</span></label>
                        <input type="text" name="author" value="{{ old('author') }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Nama penulis">
                        @error('author')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark cursor-pointer">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cetakan/Edisi</label>
                        <input type="text" name="edition" value="{{ old('edition') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Contoh: Cetakan ke-3">
                    </div>
                </div>
            </div>
            
            <!-- Section: Identifikasi -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Identifikasi & Kode</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ISBN/ISSN</label>
                        <input type="text" name="isbn" value="{{ old('isbn') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="978-xxx-xxx-xxx-x">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Klasifikasi</label>
                        <input type="text" name="classification" value="{{ old('classification') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Klasifikasi DDC">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Panggil</label>
                        <input type="text" name="call_number" value="{{ old('call_number') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Call number">
                    </div>
                </div>
            </div>
            
            <!-- Section: Penerbitan -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Penerbitan</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Penerbit</label>
                        <input type="text" name="publisher" value="{{ old('publisher') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Nama penerbit">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Terbit</label>
                        <input type="number" name="publication_year" value="{{ old('publication_year') }}" min="1800" max="{{ date('Y') + 1 }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="{{ date('Y') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Terbit</label>
                        <input type="text" name="publication_place" value="{{ old('publication_place') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Contoh: Jakarta">
                    </div>
                </div>
            </div>
            
            <!-- Section: Deskripsi & Cover -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Deskripsi & Cover</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sinopsis / Deskripsi</label>
                        <textarea name="description" rows="5"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Deskripsi singkat tentang buku...">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Fisik</label>
                        <input type="text" name="physical_description" value="{{ old('physical_description') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition mb-4"
                            placeholder="Contoh: xii, 250 hlm; 21 cm">
                        
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cover Buku</label>
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-light file:text-primary-dark file:font-medium cursor-pointer">
                        <p class="text-gray-400 text-sm mt-2">Format: JPG, PNG. Maksimal 2MB.</p>
                        @error('cover_image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
            
            <!-- Section: Eksemplar Pertama -->
            <div class="mb-8 bg-blue-50 rounded-xl p-6 border border-blue-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-blue-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Data Eksemplar Pertama
                </h3>
                <p class="text-sm text-blue-600 mb-4">Isi data fisik untuk eksemplar pertama. Eksemplar tambahan dapat ditambahkan setelah buku dibuat.</p>
                
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Eksemplar</label>
                        <input type="number" name="initial_copies" value="{{ old('initial_copies', 1) }}" min="1" max="100"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Eksemplar</label>
                        <input type="text" name="copy_code" value="{{ old('copy_code') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="EKS-001">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. Inventaris</label>
                        <input type="text" name="inventory_code" value="{{ old('inventory_code') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="INV-001">
                    </div>
                </div>
                
                <div class="grid md:grid-cols-3 gap-6 mt-4" x-data="{ shelfId: '{{ old('shelf_id', '') }}', columns: [] }" x-init="if(shelfId) fetch('/admin/shelves/' + shelfId + '/columns').then(r => r.json()).then(d => columns = d)">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rak Penyimpanan</label>
                        <select name="shelf_id" x-model="shelfId" @change="if(shelfId) fetch('/admin/shelves/' + shelfId + '/columns').then(r => r.json()).then(d => columns = d); else columns = []"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark cursor-pointer">
                            <option value="">Pilih Rak</option>
                            @foreach($shelves as $shelf)
                                <option value="{{ $shelf->id }}" {{ old('shelf_id') == $shelf->id ? 'selected' : '' }}>{{ $shelf->code }} - {{ $shelf->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kolom Rak</label>
                        <select name="shelf_column_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark cursor-pointer">
                            <option value="">Pilih Kolom</option>
                            <template x-for="col in columns" :key="col.id">
                                <option :value="col.id" x-text="'Kolom ' + col.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Diterima</label>
                        <input type="date" name="received_date" value="{{ old('received_date') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition cursor-pointer">
                    </div>
                </div>
                
                <div class="grid md:grid-cols-3 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga (Rp)</label>
                        <input type="number" name="price" value="{{ old('price') }}" min="0" step="100"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="0">
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-4 pt-6 border-t border-gray-100">
                <button type="submit" class="px-8 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Tambah Buku
                </button>
                <a href="{{ route('admin.books.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition flex items-center gap-2 cursor-pointer">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
