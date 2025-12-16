@extends('layouts.admin')

@section('page-title', isset($shelf) ? 'Edit Rak' : 'Tambah Rak')

@section('content')
<div class="max-w-xl mx-auto">
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="{{ route('admin.shelves.index') }}" class="hover:text-primary-dark transition">Rak</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-medium">{{ isset($shelf) ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-primary-dark to-green-700 px-8 py-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ isset($shelf) ? 'Edit Rak' : 'Tambah Rak Baru' }}</h2>
                    <p class="text-white/70 text-sm">Kelola lokasi rak perpustakaan</p>
                </div>
            </div>
        </div>

        <form action="{{ isset($shelf) ? route('admin.shelves.update', $shelf) : route('admin.shelves.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            @if(isset($shelf))
                @method('PUT')
            @endif
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Rak <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code', $shelf->code ?? '') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                        placeholder="Contoh: A-01">
                    @error('code')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Rak <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $shelf->name ?? '') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                        placeholder="Contoh: Rak Fiksi">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $shelf->location ?? '') }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                        placeholder="Contoh: Lantai 1">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kapasitas</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $shelf->capacity ?? '') }}" min="1"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                        placeholder="Jumlah buku">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                    placeholder="Deskripsi rak (opsional)">{{ old('description', $shelf->description ?? '') }}</textarea>
            </div>
            
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" id="is_active" 
                    {{ old('is_active', $shelf->is_active ?? true) ? 'checked' : '' }}
                    class="w-5 h-5 text-primary-dark rounded cursor-pointer">
                <label for="is_active" class="text-gray-700 cursor-pointer">Status Aktif</label>
            </div>
            
            <div class="flex gap-4 pt-4 border-t border-gray-100">
                <button type="submit" class="px-8 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
                <a href="{{ route('admin.shelves.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition cursor-pointer">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
