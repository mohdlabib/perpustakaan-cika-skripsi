@extends('layouts.admin')

@section('page-title', isset($student) ? 'Edit Siswa' : 'Tambah Siswa')

@section('content')
<div class="max-w-2xl mx-auto">
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="{{ route('admin.students.index') }}" class="hover:text-primary-dark transition">Siswa</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-medium">{{ isset($student) ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-primary-dark to-green-700 px-8 py-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ isset($student) ? 'Edit Siswa' : 'Tambah Siswa Baru' }}</h2>
                    <p class="text-white/70 text-sm">{{ isset($student) ? 'Perbarui data siswa' : 'Tambahkan siswa baru ke sistem' }}</p>
                </div>
            </div>
        </div>

        <form action="{{ isset($student) ? route('admin.students.update', $student) : route('admin.students.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            @if(isset($student))
                @method('PUT')
            @endif
            
            @if(!isset($student))
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    NIS <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nis" value="{{ old('nis') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                    placeholder="Nomor Induk Siswa">
                @error('nis')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            @endif
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $student->name ?? '') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                    placeholder="Nama lengkap siswa">
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Angkatan</label>
                <select name="grade_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition cursor-pointer">
                    <option value="">Pilih Angkatan</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade->id }}" {{ old('grade_id', $student->grade_id ?? '') == $grade->id ? 'selected' : '' }}>
                            {{ $grade->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $student->phone ?? '') }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                    placeholder="08xxxxxxxxxx">
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Password {{ isset($student) ? '(Kosongkan jika tidak ingin mengubah)' : '(Opsional)' }}
                    </span>
                </label>
                <input type="password" name="password"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                    placeholder="Minimal 6 karakter">
                @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                <p class="text-yellow-700 text-xs mt-2">Password diperlukan jika siswa ingin login ke sistem</p>
            </div>
            
            <div class="flex gap-4 pt-4 border-t border-gray-100">
                <button type="submit" class="px-8 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ isset($student) ? 'Simpan Perubahan' : 'Tambah Siswa' }}
                </button>
                <a href="{{ route('admin.students.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition flex items-center gap-2 cursor-pointer">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
