@extends('layouts.student')

@section('title', 'Profil Saya - Perpustakaan Jendela Ilmu')

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Profil Saya</h1>
        <p class="text-gray-600 mt-2">Perbarui informasi profil Anda</p>
    </div>

    <!-- Profile Card -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-dark to-green-700 p-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-2xl font-bold">
                    {{ substr($student->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ $student->name }}</h2>
                    <p class="text-white/70">NIS: {{ $student->nis }}</p>
                    @if($student->grade)
                        <span class="inline-block mt-1 px-3 py-0.5 bg-white/20 rounded-lg text-xs font-medium">{{ $student->grade->name }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('student.profile.update') }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <!-- NIS (Read Only) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">NIS</label>
                <input type="text" value="{{ $student->nis }}" disabled
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500 cursor-not-allowed">
                <p class="text-xs text-gray-400 mt-1">NIS tidak dapat diubah</p>
            </div>

            <!-- Nama (Read Only) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" value="{{ $student->name }}" disabled
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500 cursor-not-allowed">
                <p class="text-xs text-gray-400 mt-1">Untuk perubahan nama, hubungi admin perpustakaan</p>
            </div>

            <!-- Kelas (Editable) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Kelas <span class="text-red-500">*</span>
                </label>
                <input type="text" name="class" value="{{ old('class', $student->class) }}" 
                    placeholder="Contoh: XII IPA 1" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition @error('class') border-red-300 @enderror">
                @error('class')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Telepon (Editable) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">No. Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" 
                    placeholder="Contoh: 08123456789"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition @error('phone') border-red-300 @enderror">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quick Stats -->
            <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-2 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-dark">{{ $student->activeBorrowings()->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Buku Dipinjam</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-dark">{{ $student->visitCountThisMonth() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Kunjungan Bulan Ini</div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full py-3.5 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition cursor-pointer flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
