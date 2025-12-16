@extends('layouts.admin')

@section('page-title', 'Catat Peminjaman')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl p-6 border border-gray-100">
        <form action="{{ route('admin.borrowings.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Student Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Siswa *</label>
                <select name="student_nis" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($students as $student)
                        <option value="{{ $student->nis }}" {{ old('student_nis') == $student->nis ? 'selected' : '' }}>
                            {{ $student->name }} ({{ $student->nis }}) - {{ $student->class }}
                        </option>
                    @endforeach
                </select>
                @error('student_nis')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <!-- Book Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Buku *</label>
                <select name="book_id" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                    <option value="">-- Pilih Buku --</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                            {{ $book->title }} - {{ $book->author }} (Stok: {{ $book->available_stock }})
                        </option>
                    @endforeach
                </select>
                @error('book_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <!-- Due Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Batas Pengembalian *</label>
                <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}" required
                    min="{{ now()->addDay()->format('Y-m-d') }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                @error('due_date')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="flex gap-4 pt-4">
                <button type="submit" class="px-8 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition">
                    Simpan Peminjaman
                </button>
                <a href="{{ route('admin.borrowings.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
