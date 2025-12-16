@extends('layouts.admin')

@section('page-title', isset($book) ? 'Edit Buku' : 'Tambah Buku')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl p-6 border border-gray-100">
        <form action="{{ isset($book) ? route('admin.books.update', $book) : route('admin.books.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @if(isset($book))
                @method('PUT')
            @endif
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Buku *</label>
                    <input type="text" name="title" value="{{ old('title', $book->title ?? '') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                    @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <!-- Author -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Penulis *</label>
                    <input type="text" name="author" value="{{ old('author', $book->author ?? '') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                    @error('author')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <select name="category_id" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <!-- ISBN -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                    @error('isbn')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <!-- Publisher -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Penerbit</label>
                    <input type="text" name="publisher" value="{{ old('publisher', $book->publisher ?? '') }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                </div>
                
                <!-- Year -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Terbit</label>
                    <input type="number" name="publication_year" value="{{ old('publication_year', $book->publication_year ?? '') }}" min="1900" max="{{ date('Y') }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                </div>
                
                <!-- Stock -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stok *</label>
                    <input type="number" name="stock" value="{{ old('stock', $book->stock ?? 1) }}" min="0" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                    @error('stock')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <!-- Shelf Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Rak</label>
                    <input type="text" name="shelf_location" value="{{ old('shelf_location', $book->shelf_location ?? '') }}" placeholder="Contoh: A-01"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                </div>
                
                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi / Sinopsis</label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">{{ old('description', $book->description ?? '') }}</textarea>
                </div>
                
                <!-- Cover Image -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Buku</label>
                    @if(isset($book) && $book->cover_image)
                        <div class="mb-3">
                            <img src="{{ Storage::url($book->cover_image) }}" class="w-24 h-32 object-cover rounded-lg">
                        </div>
                    @endif
                    <input type="file" name="cover_image" accept="image/*"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                    <p class="text-gray-400 text-sm mt-1">Format: JPG, PNG. Maks 2MB.</p>
                </div>
            </div>
            
            <div class="flex gap-4 pt-4">
                <button type="submit" class="px-8 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition">
                    {{ isset($book) ? 'Simpan Perubahan' : 'Tambah Buku' }}
                </button>
                <a href="{{ route('admin.books.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
