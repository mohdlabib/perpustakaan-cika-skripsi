@extends('layouts.admin')

@section('page-title', 'Kelola Buku')

@section('content')
<!-- Header Actions -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <p class="text-gray-500">Total {{ $books->total() }} buku</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.books.export') }}" class="px-4 py-2 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Excel
        </a>
        <a href="{{ route('admin.books.create') }}" class="px-4 py-2 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Buku
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl p-4 mb-6 border border-gray-100">
    <form action="{{ route('admin.books.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, ISBN..." 
            class="flex-1 px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
        <select name="category" class="px-4 py-2 border border-gray-200 rounded-xl">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-xl">Cari</button>
    </form>
</div>

<!-- Books Table -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-600 text-sm">
                    <th class="px-6 py-4 font-medium">Buku</th>
                    <th class="px-6 py-4 font-medium">Kategori</th>
                    <th class="px-6 py-4 font-medium">Stok</th>
                    <th class="px-6 py-4 font-medium">Lokasi</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($books as $book)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-16 bg-primary-light rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden">
                                @if($book->cover_image)
                                    <img src="{{ Storage::url($book->cover_image) }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-primary-dark/30" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $book->title }}</p>
                                <p class="text-gray-500 text-sm">{{ $book->author }}</p>
                                @if($book->isbn)
                                    <p class="text-gray-400 text-xs">ISBN: {{ $book->isbn }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-primary-light text-primary-dark text-xs rounded-lg">
                            {{ $book->category->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="{{ $book->available_stock > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                            {{ $book->available_stock }}/{{ $book->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $book->shelf_location ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.books.edit', $book) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.books.destroy', $book) }}" method="POST" onsubmit="return confirm('Hapus buku ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        Tidak ada buku ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($books->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $books->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
