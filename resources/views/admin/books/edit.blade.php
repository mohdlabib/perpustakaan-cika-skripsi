@extends('layouts.admin')

@section('page-title', 'Edit Buku - ' . $book->title)

@section('content')
<div class="max-w-5xl mx-auto">
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="{{ route('admin.books.index') }}" class="hover:text-primary-dark transition">Kelola Buku</a></li>
            <li class="text-gray-300">/</li>
            <li><a href="{{ route('admin.books.show', $book) }}" class="hover:text-primary-dark transition">{{ Str::limit($book->title, 30) }}</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-medium">Edit</li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-primary-dark to-green-700 px-8 py-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold">Edit Data Buku</h2>
                    <p class="text-white/70 text-sm">Edit informasi bibliografi buku. Untuk mengelola eksemplar, buka halaman detail buku.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')
            
            <!-- Section: Informasi Dasar -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Dasar</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Buku <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $book->title) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="Masukkan judul buku">
                        @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pengarang <span class="text-red-500">*</span></label>
                        <input type="text" name="author" value="{{ old('author', $book->author) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                        @error('author')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark cursor-pointer">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cetakan/Edisi</label>
                        <input type="text" name="edition" value="{{ old('edition', $book->edition) }}"
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
                        <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                            placeholder="978-xxx-xxx-xxx-x">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Klasifikasi</label>
                        <input type="text" name="classification" value="{{ old('classification', $book->classification) }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Panggil</label>
                        <input type="text" name="call_number" value="{{ old('call_number', $book->call_number) }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                    </div>
                </div>
            </div>
            
            <!-- Section: Penerbitan -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Penerbitan</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Penerbit</label>
                        <input type="text" name="publisher" value="{{ old('publisher', $book->publisher) }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Terbit</label>
                        <input type="number" name="publication_year" value="{{ old('publication_year', $book->publication_year) }}" min="1800" max="{{ date('Y') + 1 }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Terbit</label>
                        <input type="text" name="publication_place" value="{{ old('publication_place', $book->publication_place) }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
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
                            placeholder="Deskripsi singkat...">{{ old('description', $book->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Fisik</label>
                        <input type="text" name="physical_description" value="{{ old('physical_description', $book->physical_description) }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition mb-4"
                            placeholder="Contoh: xii, 250 hlm; 21 cm">
                        
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cover Buku</label>
                        @if($book->cover_image)
                            <div class="mb-3">
                                <p class="text-xs text-gray-500 mb-1">Cover saat ini:</p>
                                <img src="{{ $book->cover_url }}" class="w-24 h-32 rounded-lg object-cover shadow border">
                            </div>
                        @endif
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-light file:text-primary-dark file:font-medium cursor-pointer">
                        <p class="text-gray-400 text-sm mt-2">Format: JPG, PNG. Maks 2MB. Kosongkan jika tidak ingin mengganti.</p>
                        @error('cover_image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mb-8 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-blue-700">
                    Untuk mengelola eksemplar fisik (menambah, edit, hapus eksemplar), silakan buka 
                    <a href="{{ route('admin.books.show', $book) }}" class="font-bold underline hover:text-blue-900">halaman detail buku</a>.
                </p>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-4 pt-6 border-t border-gray-100">
                <button type="submit" class="px-8 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.books.show', $book) }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition cursor-pointer">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
