@extends('layouts.admin')

@section('page-title', isset($grade) ? 'Edit Angkatan' : 'Tambah Angkatan')

@section('content')
<div class="max-w-xl mx-auto">
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="{{ route('admin.grades.index') }}" class="hover:text-primary-dark transition">Angkatan</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-medium">{{ isset($grade) ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-primary-dark to-green-700 px-8 py-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ isset($grade) ? 'Edit Angkatan' : 'Tambah Angkatan Baru' }}</h2>
                    <p class="text-white/70 text-sm">Kelola data angkatan/tahun ajaran</p>
                </div>
            </div>
        </div>

        <form action="{{ isset($grade) ? route('admin.grades.update', $grade) : route('admin.grades.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            @if(isset($grade))
                @method('PUT')
            @endif
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Angkatan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $grade->name ?? '') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                    placeholder="Contoh: 2024/2025 atau Kelas XII">
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat</label>
                    <select name="level" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark cursor-pointer">
                        <option value="">Pilih Tingkat</option>
                        <option value="X" {{ old('level', $grade->level ?? '') == 'X' ? 'selected' : '' }}>Kelas X</option>
                        <option value="XI" {{ old('level', $grade->level ?? '') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                        <option value="XII" {{ old('level', $grade->level ?? '') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                    <input type="number" name="academic_year" value="{{ old('academic_year', $grade->academic_year ?? date('Y')) }}" min="2000" max="2100"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                        placeholder="{{ date('Y') }}">
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" id="is_active" 
                    {{ old('is_active', $grade->is_active ?? true) ? 'checked' : '' }}
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
                <a href="{{ route('admin.grades.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition cursor-pointer">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
