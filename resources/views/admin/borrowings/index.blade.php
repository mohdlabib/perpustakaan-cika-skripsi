@extends('layouts.admin')

@section('page-title', 'Peminjaman')

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <p class="text-gray-500">Kelola peminjaman buku</p>
    </div>
    <a href="{{ route('admin.borrowings.create') }}" class="px-4 py-2 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Catat Peminjaman
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl p-4 mb-6 border border-gray-100">
    <form action="{{ route('admin.borrowings.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa atau judul buku..." 
            class="flex-1 px-4 py-2 border border-gray-200 rounded-xl">
        <select name="status" class="px-4 py-2 border border-gray-200 rounded-xl">
            <option value="">Semua Status</option>
            <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
        </select>
        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-xl">Filter</button>
    </form>
</div>

<!-- Borrowings Table -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-600 text-sm">
                    <th class="px-6 py-4 font-medium">Siswa</th>
                    <th class="px-6 py-4 font-medium">Buku</th>
                    <th class="px-6 py-4 font-medium">Tgl Pinjam</th>
                    <th class="px-6 py-4 font-medium">Batas</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($borrowings as $borrowing)
                <tr class="hover:bg-gray-50 transition {{ $borrowing->is_overdue ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">{{ $borrowing->student->name ?? '-' }}</p>
                        <p class="text-gray-400 text-sm">{{ $borrowing->student_nis }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-800">{{ $borrowing->book->title ?? '-' }}</p>
                        <p class="text-gray-400 text-sm">{{ $borrowing->book->author ?? '' }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $borrowing->borrow_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 {{ $borrowing->is_overdue ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        {{ $borrowing->due_date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($borrowing->status === 'returned')
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-lg">Dikembalikan</span>
                        @elseif($borrowing->is_overdue)
                            <span class="px-3 py-1 bg-red-100 text-red-600 text-xs rounded-lg">Terlambat {{ abs($borrowing->days_remaining) }} hari</span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-600 text-xs rounded-lg">Dipinjam</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-2">
                            @if($borrowing->status === 'borrowed')
                                <form action="{{ route('admin.borrowings.return', $borrowing) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                                        Kembalikan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        Tidak ada data peminjaman
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($borrowings->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $borrowings->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
