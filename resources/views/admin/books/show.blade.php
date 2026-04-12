@extends('layouts.admin')

@section('page-title', $book->title . ' - Detail Buku')

@section('content')
<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
    <a href="{{ route('admin.books.index') }}" class="hover:text-primary-dark transition">Kelola Buku</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-800 font-medium">{{ Str::limit($book->title, 40) }}</span>
</div>

<!-- Book Header Card -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-primary-dark to-green-700 p-6 text-white">
        <div class="flex gap-6">
            <!-- Cover -->
            <div class="w-28 h-40 bg-white/20 rounded-xl overflow-hidden flex-shrink-0 shadow-lg">
                @if($book->cover_url)
                    <img src="{{ $book->cover_url }}" class="w-full h-full object-cover" alt="{{ $book->title }}">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                @endif
            </div>
            <!-- Info -->
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold leading-tight">{{ $book->title }}</h1>
                <p class="text-white/80 mt-1">{{ $book->author }}</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="px-3 py-1 bg-white/20 rounded-lg text-sm">{{ $book->category->name ?? '-' }}</span>
                    @if($book->edition)
                        <span class="px-3 py-1 bg-white/10 rounded-lg text-sm">{{ $book->edition }}</span>
                    @endif
                    @if($book->isbn)
                        <span class="px-3 py-1 bg-white/10 rounded-lg text-sm">ISBN: {{ $book->isbn }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-6 mt-4">
                    <div>
                        <span class="text-white/60 text-sm">Total Eksemplar</span>
                        <p class="text-2xl font-bold">{{ $book->stock }}</p>
                    </div>
                    <div>
                        <span class="text-white/60 text-sm">Tersedia</span>
                        <p class="text-2xl font-bold text-green-300">{{ $book->available_stock }}</p>
                    </div>
                    <div>
                        <span class="text-white/60 text-sm">Dipinjam</span>
                        <p class="text-2xl font-bold text-yellow-300">{{ $book->stock - $book->available_stock }}</p>
                    </div>
                </div>
            </div>
            <!-- Actions -->
            <div class="flex flex-col gap-2">
                <a href="{{ route('admin.books.edit', $book) }}" class="px-4 py-2 bg-white/20 rounded-lg text-sm font-medium hover:bg-white/30 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Buku
                </a>
            </div>
        </div>
    </div>

    <!-- Book Details Grid -->
    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
        <div>
            <span class="text-gray-500">Penerbit</span>
            <p class="font-medium text-gray-800 mt-1">{{ $book->publisher ?? '-' }}</p>
        </div>
        <div>
            <span class="text-gray-500">Tahun Terbit</span>
            <p class="font-medium text-gray-800 mt-1">{{ $book->publication_year ?? '-' }}</p>
        </div>
        <div>
            <span class="text-gray-500">Tempat Terbit</span>
            <p class="font-medium text-gray-800 mt-1">{{ $book->publication_place ?? '-' }}</p>
        </div>
        <div>
            <span class="text-gray-500">Klasifikasi</span>
            <p class="font-medium text-gray-800 mt-1">{{ $book->classification ?? '-' }}</p>
        </div>
        <div>
            <span class="text-gray-500">No. Panggil</span>
            <p class="font-medium text-gray-800 mt-1">{{ $book->call_number ?? '-' }}</p>
        </div>
        <div>
            <span class="text-gray-500">Deskripsi Fisik</span>
            <p class="font-medium text-gray-800 mt-1">{{ $book->physical_description ?? '-' }}</p>
        </div>
        @if($book->description)
        <div class="col-span-2">
            <span class="text-gray-500">Sinopsis</span>
            <p class="font-medium text-gray-800 mt-1 line-clamp-3">{{ $book->description }}</p>
        </div>
        @endif
    </div>
</div>

<!-- Copies Section -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex justify-between items-center p-6 border-b border-gray-100">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Eksemplar</h2>
            <p class="text-gray-500 text-sm">{{ $copies->total() }} eksemplar terdaftar</p>
        </div>
        <a href="{{ route('admin.books.copies.create', $book) }}" class="px-4 py-2.5 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Eksemplar
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-600 text-sm">
                    <th class="px-6 py-3 font-semibold">No</th>
                    <th class="px-6 py-3 font-semibold">Kode Eksemplar</th>
                    <th class="px-6 py-3 font-semibold">No. Inventaris</th>
                    <th class="px-6 py-3 font-semibold">Lokasi Rak</th>
                    <th class="px-6 py-3 font-semibold">Kondisi</th>
                    <th class="px-6 py-3 font-semibold">Status</th>
                    <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($copies as $index => $copy)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-500">{{ $copies->firstItem() + $index }}</td>
                    <td class="px-6 py-4">
                        <span class="font-mono font-medium text-gray-800">{{ $copy->copy_code ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-mono text-gray-600">{{ $copy->inventory_code ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($copy->shelf_display)
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-lg">{{ $copy->shelf_display }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $condColors = ['baik' => 'green', 'rusak' => 'yellow', 'hilang' => 'red'];
                            $condLabels = ['baik' => 'Baik', 'rusak' => 'Rusak', 'hilang' => 'Hilang'];
                            $cc = $condColors[$copy->condition] ?? 'gray';
                        @endphp
                        <span class="px-2 py-1 bg-{{ $cc }}-100 text-{{ $cc }}-700 text-xs font-medium rounded-lg">
                            {{ $condLabels[$copy->condition] ?? $copy->condition }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-{{ $copy->status_color }}-100 text-{{ $copy->status_color }}-700 text-xs font-medium rounded-lg">
                            {{ $copy->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.books.copies.edit', [$book, $copy]) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.books.copies.destroy', [$book, $copy]) }}" method="POST" onsubmit="return confirmDelete(this, 'eksemplar {{ $copy->copy_code ?? '#'.$copy->id }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p class="text-gray-500 font-medium">Belum ada eksemplar</p>
                        <a href="{{ route('admin.books.copies.create', $book) }}" class="text-primary-dark text-sm font-medium hover:underline mt-1 inline-block">Tambah eksemplar pertama →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($copies->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
        {{ $copies->links() }}
    </div>
    @endif
</div>
@endsection
