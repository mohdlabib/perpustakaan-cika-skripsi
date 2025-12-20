@extends('layouts.student')

@section('title', 'Login Siswa - Perpustakaan Jendela Ilmu')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary-light rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Login Siswa</h1>
                <p class="text-gray-500 mt-2">Masukkan NIS untuk mengakses perpustakaan</p>
            </div>
            
            <form action="{{ route('student.login.submit') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="nis" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Induk Siswa (NIS)
                    </label>
                    <input 
                        type="text" 
                        id="nis" 
                        name="nis" 
                        value="{{ old('nis') }}"
                        placeholder="Contoh: 2024001"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition text-lg"
                        autofocus
                        required
                    >
                    @error('nis')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" class="w-full bg-primary-dark text-white py-3 px-6 rounded-xl font-semibold hover:bg-opacity-90 transition transform hover:scale-[1.02] active:scale-[0.98]">
                    Masuk
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <a href="{{ route('catalog.index') }}" class="text-gray-500 hover:text-primary-dark transition text-sm">
                    ← Kembali ke Katalog
                </a>
            </div>
        </div>
        
        <div class="mt-6 text-center text-gray-500 text-sm">
            <p>Belum punya NIS? Hubungi petugas perpustakaan.</p>
        </div>
    </div>
</div>
@endsection
