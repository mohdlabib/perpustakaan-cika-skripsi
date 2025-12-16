@extends('layouts.admin')

@section('page-title', 'Kelola Rak')

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">Daftar Rak</h2>
        <p class="text-gray-500 text-sm">Kelola lokasi rak perpustakaan</p>
    </div>
    <a href="{{ route('admin.shelves.create') }}" class="px-4 py-2.5 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition flex items-center gap-2 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Rak
    </a>
</div>

<!-- Shelves Table -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-600 text-sm">
                    <th class="px-6 py-4 font-semibold">Kode</th>
                    <th class="px-6 py-4 font-semibold">Nama Rak</th>
                    <th class="px-6 py-4 font-semibold">Lokasi</th>
                    <th class="px-6 py-4 font-semibold">Kapasitas</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($shelves as $shelf)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 font-bold rounded-lg">{{ $shelf->code }}</span>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $shelf->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $shelf->location ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $shelf->capacity ?? '-' }} buku</td>
                    <td class="px-6 py-4">
                        @if($shelf->is_active)
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-lg">Aktif</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.shelves.edit', $shelf) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.shelves.destroy', $shelf) }}" method="POST" onsubmit="return confirm('Hapus rak ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition cursor-pointer">
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
                    <td colspan="6" class="px-6 py-16 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Tidak ada rak ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
        {{ $shelves->withQueryString()->links() }}
    </div>
</div>
@endsection
