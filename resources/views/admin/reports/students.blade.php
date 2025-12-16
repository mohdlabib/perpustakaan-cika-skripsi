@extends('layouts.admin')

@section('page-title', 'Laporan Siswa')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
                <div class="text-gray-500 text-sm">Total Siswa</div>
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
                <div class="text-2xl font-bold text-gray-800">{{ $stats['active_borrowers'] }}</div>
                <div class="text-gray-500 text-sm">Peminjam Aktif</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['total_borrowings'] }}</div>
                <div class="text-gray-500 text-sm">Total Peminjaman</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm {{ $stats['overdue'] > 0 ? 'border-red-300 bg-red-50' : '' }}">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 {{ $stats['overdue'] > 0 ? 'bg-red-100' : 'bg-orange-100' }} rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 {{ $stats['overdue'] > 0 ? 'text-red-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold {{ $stats['overdue'] > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $stats['overdue'] }}</div>
                <div class="text-gray-500 text-sm">Terlambat</div>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">Data Siswa</h2>
        <p class="text-gray-500 text-sm">Daftar seluruh siswa anggota perpustakaan</p>
    </div>
    <a href="{{ route('admin.reports.students.export') }}" class="px-4 py-2.5 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition flex items-center gap-2 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Export CSV
    </a>
</div>

<!-- Students Table -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-600 text-sm">
                    <th class="px-6 py-4 font-semibold">No</th>
                    <th class="px-6 py-4 font-semibold">NIS</th>
                    <th class="px-6 py-4 font-semibold">Nama</th>
                    <th class="px-6 py-4 font-semibold">Kelas</th>
                    <th class="px-6 py-4 font-semibold">Angkatan</th>
                    <th class="px-6 py-4 font-semibold">Total Pinjam</th>
                    <th class="px-6 py-4 font-semibold">Sedang Dipinjam</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($students as $index => $student)
                @php $activeBorrowings = $student->borrowings->where('status', 'borrowed')->count(); @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-500">{{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}</td>
                    <td class="px-6 py-4 font-mono text-gray-600">{{ $student->nis }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $student->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $student->class ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if($student->grade)
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg">{{ $student->grade->name }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-800 font-medium">{{ $student->borrowings->count() }}</td>
                    <td class="px-6 py-4">
                        @if($activeBorrowings > 0)
                            <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded-lg">{{ $activeBorrowings }} buku</span>
                        @else
                            <span class="text-gray-400">0</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
        {{ $students->withQueryString()->links() }}
    </div>
</div>
@endsection
