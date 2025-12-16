@extends('layouts.admin')

@section('page-title', 'Peminjaman')

@section('content')
<!-- Info Panel Widget -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ \App\Models\Borrowing::count() }}</div>
                <div class="text-blue-100 text-sm mt-1">Total Peminjaman</div>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">📋</div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ \App\Models\Borrowing::active()->count() }}</div>
                <div class="text-green-100 text-sm mt-1">Sedang Dipinjam</div>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">📖</div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ \App\Models\Borrowing::overdue()->count() }}</div>
                <div class="text-red-100 text-sm mt-1">Terlambat</div>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">⚠️</div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-2xl p-5 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ \App\Models\Borrowing::returned()->count() }}</div>
                <div class="text-gray-100 text-sm mt-1">Dikembalikan</div>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">✅</div>
        </div>
    </div>
</div>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">Manajemen Peminjaman</h2>
        <p class="text-gray-500 text-sm">Kelola peminjaman dan pengembalian buku</p>
    </div>
    <a href="{{ route('admin.borrowings.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-primary-dark to-green-700 text-white rounded-xl font-medium hover:shadow-lg transition flex items-center gap-2">
        ➕ Catat Peminjaman
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl p-5 mb-6 border border-gray-100 shadow-sm">
    <form action="{{ route('admin.borrowings.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1 relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa atau judul buku..." 
                class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark">
        </div>
        <select name="status" class="px-4 py-3 border border-gray-200 rounded-xl">
            <option value="">📊 Semua Status</option>
            <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>📖 Dipinjam</option>
            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>✅ Dikembalikan</option>
            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>⚠️ Terlambat</option>
        </select>
        <button type="submit" class="px-6 py-3 bg-gray-800 text-white rounded-xl font-medium hover:bg-gray-900 transition">Filter</button>
    </form>
</div>

<!-- Borrowings Table -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-600 text-sm">
                    <th class="px-6 py-4 font-semibold">Siswa</th>
                    <th class="px-6 py-4 font-semibold">Buku</th>
                    <th class="px-6 py-4 font-semibold">Tgl Pinjam</th>
                    <th class="px-6 py-4 font-semibold">Batas</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($borrowings as $borrowing)
                <tr class="hover:bg-gray-50 transition {{ $borrowing->is_overdue ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-light rounded-full flex items-center justify-center font-bold text-primary-dark">
                                {{ substr($borrowing->student->name ?? 'X', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $borrowing->student->name ?? '-' }}</p>
                                <p class="text-gray-400 text-sm">{{ $borrowing->student_nis }} • {{ $borrowing->student->class ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-800 font-medium">{{ Str::limit($borrowing->book->title ?? '-', 25) }}</p>
                        <p class="text-gray-400 text-sm">{{ $borrowing->book->author ?? '' }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <div class="flex items-center gap-2">
                            <span>📅</span>
                            {{ $borrowing->borrow_date->format('d M Y') }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 {{ $borrowing->is_overdue ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            <span>⏰</span>
                            {{ $borrowing->due_date->format('d M Y') }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($borrowing->status === 'returned')
                            <span class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg inline-flex items-center gap-1">
                                ✅ Dikembalikan
                            </span>
                        @elseif($borrowing->is_overdue)
                            <span class="px-3 py-1.5 bg-red-100 text-red-600 text-xs font-medium rounded-lg inline-flex items-center gap-1 animate-pulse">
                                ⚠️ Terlambat {{ abs($borrowing->days_remaining) }} hari
                            </span>
                        @else
                            <span class="px-3 py-1.5 bg-green-100 text-green-600 text-xs font-medium rounded-lg inline-flex items-center gap-1">
                                📖 Dipinjam
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-2">
                            @if($borrowing->status === 'borrowed')
                                <form action="{{ route('admin.borrowings.return', $borrowing) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm rounded-xl font-medium hover:shadow-lg transition flex items-center gap-1">
                                        ✅ Kembalikan
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-sm">Selesai</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="text-5xl mb-4">📋</div>
                        <p class="text-gray-500 font-medium">Tidak ada data peminjaman</p>
                        <p class="text-gray-400 text-sm mt-1">Catat peminjaman baru untuk memulai</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($borrowings->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
        {{ $borrowings->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
