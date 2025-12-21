@extends('layouts.admin')

@section('page-title', 'Kelola Buku')

@section('content')
<!-- Stats Cards - Same style as Dashboard -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ $books->total() }}</div>
                <div class="text-gray-500 text-sm">Total Buku</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Book::where('stock', '>', 0)->count() }}</div>
                <div class="text-gray-500 text-sm">Tersedia</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Category::count() }}</div>
                <div class="text-gray-500 text-sm">Kategori</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Borrowing::active()->count() }}</div>
                <div class="text-gray-500 text-sm">Dipinjam</div>
            </div>
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
        <a href="{{ route('admin.books.export') }}" class="px-4 py-2.5 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Excel
        </a>
        <a href="{{ route('admin.books.create') }}" class="px-4 py-2.5 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Buku
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl p-5 mb-6 border border-gray-100 shadow-sm">
    <form action="{{ route('admin.books.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1 relative">
            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, ISBN..." 
                class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
        </div>
        <select name="category" class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-6 py-3 bg-gray-800 text-white rounded-xl font-medium hover:bg-gray-900 transition">
            Cari
        </button>
    </form>
</div>

<!-- Books Table -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm" x-data="bookDetailModal()">
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
                                    <img src="{{ $book->cover_url }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-primary-dark/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
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
                            {{ $book->category->name ?? '-' }}
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
                                {{ $book->shelf_location }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-2">
                            <button @click="showDetail({{ $book->id }})" class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition cursor-pointer" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <a href="{{ route('admin.books.edit', $book) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.books.destroy', $book) }}" method="POST" onsubmit="return confirm('Hapus buku ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Tidak ada buku ditemukan</p>
                        <p class="text-gray-400 text-sm mt-1">Tambahkan buku baru untuk memulai</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
        {{ $books->withQueryString()->links() }}
    </div>

    <!-- Book Detail Modal -->
    <div x-show="isOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <!-- Background overlay with blur effect -->
            <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-all" @click="closeModal()"></div>

            <!-- Modal panel -->
            <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" 
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                
                <!-- Loading State -->
                <div x-show="loading" class="p-10 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 border-4 border-primary-dark/20 border-t-primary-dark rounded-full animate-spin"></div>
                    <p class="text-gray-500">Memuat detail buku...</p>
                </div>

                <!-- Book Detail Content -->
                <div x-show="!loading && book">
                    <!-- Compact Header with Cover & Stock -->
                    <div class="bg-gradient-to-r from-primary-dark to-green-700 p-4 text-white">
                        <div class="flex gap-4">
                            <!-- Cover -->
                            <div class="w-20 h-28 bg-white/20 rounded-lg overflow-hidden flex-shrink-0 shadow-lg">
                                <template x-if="book?.cover_url">
                                    <img :src="book.cover_url" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!book?.cover_url">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>
                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-lg leading-tight truncate" x-text="book?.title"></h3>
                                <p class="text-white/80 text-sm mt-0.5" x-text="book?.author || '-'"></p>
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <span class="px-2 py-0.5 bg-white/20 rounded text-xs" x-text="book?.category?.name || '-'"></span>
                                    <template x-if="book?.edition">
                                        <span class="px-2 py-0.5 bg-white/10 rounded text-xs" x-text="book?.edition"></span>
                                    </template>
                                </div>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-white/60 text-xs">Stok:</span>
                                    <span class="font-bold text-lg" x-text="book?.available_stock"></span>
                                    <span class="text-white/60">/</span>
                                    <span class="text-white/80" x-text="book?.stock"></span>
                                </div>
                            </div>
                            <!-- Close -->
                            <button @click="closeModal()" class="p-1 hover:bg-white/20 rounded-lg transition cursor-pointer h-fit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Compact Content -->
                    <div class="max-h-[50vh] overflow-y-auto p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Kode & Identifikasi -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Kode & Identifikasi</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">ISBN/ISSN</span>
                                        <span class="font-medium text-gray-800 text-right" x-text="book?.isbn || '-'"></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Kode Eksemplar</span>
                                        <span class="font-medium text-gray-800 text-right" x-text="book?.item_code || '-'"></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">No. Inventaris</span>
                                        <span class="font-medium text-gray-800 text-right" x-text="book?.inventory_code || '-'"></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Klasifikasi</span>
                                        <span class="font-medium text-gray-800 text-right" x-text="book?.classification || '-'"></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">No. Panggil</span>
                                        <span class="font-medium text-gray-800 text-right" x-text="book?.call_number || '-'"></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Lokasi Rak</span>
                                        <template x-if="book?.shelf_location">
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-medium" x-text="book?.shelf_location"></span>
                                        </template>
                                        <template x-if="!book?.shelf_location">
                                            <span class="text-gray-400">-</span>
                                        </template>
                                    </div>
                                    <div class="flex justify-between py-1.5">
                                        <span class="text-gray-500">Kolom</span>
                                        <template x-if="book?.shelf_column">
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium" x-text="'Kolom ' + book?.shelf_column?.name"></span>
                                        </template>
                                        <template x-if="!book?.shelf_column">
                                            <span class="text-gray-400">-</span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Penerbitan & Lainnya -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Penerbitan</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Penerbit</span>
                                        <span class="font-medium text-gray-800 text-right truncate max-w-32" x-text="book?.publisher || '-'"></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Tahun Terbit</span>
                                        <span class="font-medium text-gray-800 text-right" x-text="book?.publication_year || '-'"></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Tempat Terbit</span>
                                        <span class="font-medium text-gray-800 text-right truncate max-w-32" x-text="book?.publication_place || '-'"></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Deskripsi Fisik</span>
                                        <span class="font-medium text-gray-800 text-right truncate max-w-32" x-text="book?.physical_description || '-'"></span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-gray-100">
                                        <span class="text-gray-500">Tgl Diterima</span>
                                        <span class="font-medium text-gray-800 text-right" x-text="book?.received_date || '-'"></span>
                                    </div>
                                    <div class="flex justify-between py-1.5">
                                        <span class="text-gray-500">Harga</span>
                                        <template x-if="book?.price">
                                            <span class="font-medium text-gray-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(book?.price)"></span>
                                        </template>
                                        <template x-if="!book?.price">
                                            <span class="text-gray-400">-</span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sinopsis -->
                        <div x-show="book?.description" class="mt-4 pt-4 border-t border-gray-100">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Sinopsis</h4>
                            <p class="text-gray-700 text-sm leading-relaxed line-clamp-3" x-text="book?.description"></p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-4 py-3 flex justify-end gap-2 border-t border-gray-100">
                        <button @click="closeModal()" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-100 transition cursor-pointer">
                            Tutup
                        </button>
                        <a :href="'/admin/books/' + book?.id + '/edit'" class="px-4 py-2 bg-primary-dark text-white rounded-lg text-sm font-medium hover:bg-opacity-90 transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function bookDetailModal() {
    return {
        isOpen: false,
        loading: false,
        book: null,

        async showDetail(bookId) {
            this.isOpen = true;
            this.loading = true;
            this.book = null;

            try {
                const response = await axios.get(`/admin/books/${bookId}/detail`);
                this.book = response.data;
            } catch (error) {
                console.error('Error loading book detail:', error);
                alert('Gagal memuat detail buku');
                this.closeModal();
            }

            this.loading = false;
        },

        closeModal() {
            this.isOpen = false;
            this.book = null;
        }
    }
}
</script>
@endpush
@endsection

