@extends('layouts.admin')

@section('page-title', 'Kelola Buku')

@section('content')
<!-- Info Panel Widget -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ $books->total() }}</div>
                <div class="text-blue-100 text-sm mt-1">Total Buku</div>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">📚</div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ \App\Models\Book::where('stock', '>', 0)->count() }}</div>
                <div class="text-green-100 text-sm mt-1">Buku Tersedia</div>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">✅</div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ \App\Models\Category::count() }}</div>
                <div class="text-purple-100 text-sm mt-1">Kategori</div>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">🏷️</div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ \App\Models\Borrowing::active()->count() }}</div>
                <div class="text-orange-100 text-sm mt-1">Sedang Dipinjam</div>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">📖</div>
        </div>
    </div>
</div>

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">Daftar Buku Perpustakaan</h2>
        <p class="text-gray-500 text-sm">Kelola koleksi buku perpustakaan sekolah</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.books.export') }}" class="px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-medium hover:shadow-lg hover:shadow-green-500/30 transition flex items-center gap-2">
            📥 Export Excel
        </a>
        <a href="{{ route('admin.books.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-primary-dark to-green-700 text-white rounded-xl font-medium hover:shadow-lg transition flex items-center gap-2">
            ➕ Tambah Buku
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl p-5 mb-6 border border-gray-100 shadow-sm">
    <form action="{{ route('admin.books.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1 relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, ISBN..." 
                class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
        </div>
        <select name="category" class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark">
            <option value="">🏷️ Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->icon ?? '' }} {{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-6 py-3 bg-gray-800 text-white rounded-xl font-medium hover:bg-gray-900 transition">Cari</button>
    </form>
</div>

<!-- Books Table -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-600 text-sm">
                    <th class="px-6 py-4 font-semibold">Buku</th>
                    <th class="px-6 py-4 font-semibold">Kategori</th>
                    <th class="px-6 py-4 font-semibold">Stok</th>
                    <th class="px-6 py-4 font-semibold">Lokasi</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($books as $book)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-18 bg-gradient-to-br from-primary-light to-gray-100 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden shadow">
                                @if($book->cover_image)
                                    <img src="{{ Storage::url($book->cover_image) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-2xl">📕</span>
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $book->title }}</p>
                                <p class="text-gray-500 text-sm">{{ $book->author }}</p>
                                @if($book->isbn)
                                    <p class="text-gray-400 text-xs mt-1">ISBN: {{ $book->isbn }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1.5 bg-primary-light text-primary-dark text-xs font-medium rounded-lg">
                            {{ $book->category->icon ?? '📚' }} {{ $book->category->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-bold {{ $book->available_stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $book->available_stock }}
                            </span>
                            <span class="text-gray-400">/</span>
                            <span class="text-gray-600">{{ $book->stock }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($book->shelf_location)
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 font-medium rounded-lg text-sm">
                                📍 {{ $book->shelf_location }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.books.edit', $book) }}" class="p-2.5 text-blue-600 hover:bg-blue-50 rounded-xl transition" title="Edit">
                                ✏️
                            </a>
                            <form action="{{ route('admin.books.destroy', $book) }}" method="POST" onsubmit="return confirm('Hapus buku ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2.5 text-red-600 hover:bg-red-50 rounded-xl transition" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="text-5xl mb-4">📚</div>
                        <p class="text-gray-500 font-medium">Tidak ada buku ditemukan</p>
                        <p class="text-gray-400 text-sm mt-1">Tambahkan buku baru untuk memulai</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($books->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
        {{ $books->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
