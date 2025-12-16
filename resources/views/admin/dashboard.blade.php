@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="text-3xl font-bold text-primary-dark">{{ $stats['total_books'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Total Buku</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="text-3xl font-bold text-blue-600">{{ $stats['total_students'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Total Siswa</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="text-3xl font-bold text-accent-green">{{ $stats['active_borrowings'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Sedang Dipinjam</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="text-3xl font-bold text-purple-600">{{ $stats['today_visits'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Pengunjung Hari Ini</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 {{ $stats['overdue_borrowings'] > 0 ? 'bg-red-50 border-red-200' : '' }}">
        <div class="text-3xl font-bold {{ $stats['overdue_borrowings'] > 0 ? 'text-red-600' : 'text-gray-600' }}">{{ $stats['overdue_borrowings'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Terlambat</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="text-3xl font-bold text-green-600">{{ $stats['available_books'] }}</div>
        <div class="text-gray-500 text-sm mt-1">Buku Tersedia</div>
    </div>
</div>

<!-- Top 3 Sections -->
<div class="grid md:grid-cols-2 gap-6 mb-8">
    <!-- Top 3 Most Borrowed Books -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="text-xl">📚</span> Top 3 Buku Terpopuler
        </h3>
        <div class="space-y-3">
            @forelse($topBooks as $book)
                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                        {{ $book['rank'] == 1 ? 'bg-yellow-400 text-yellow-900' : '' }}
                        {{ $book['rank'] == 2 ? 'bg-gray-300 text-gray-700' : '' }}
                        {{ $book['rank'] == 3 ? 'bg-amber-600 text-white' : '' }}">
                        {{ $book['rank'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 truncate">{{ $book['title'] }}</p>
                        <p class="text-gray-500 text-sm">{{ $book['author'] }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-primary-dark">{{ $book['borrow_count'] }}</span>
                        <p class="text-gray-400 text-xs">pinjaman</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-center py-4">Belum ada data</p>
            @endforelse
        </div>
    </div>
    
    <!-- Top 3 Most Frequent Visitors -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="text-xl">🏆</span> Top 3 Pengunjung Aktif
        </h3>
        <div class="space-y-3">
            @forelse($topVisitors as $visitor)
                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                        {{ $visitor['rank'] == 1 ? 'bg-yellow-400 text-yellow-900' : '' }}
                        {{ $visitor['rank'] == 2 ? 'bg-gray-300 text-gray-700' : '' }}
                        {{ $visitor['rank'] == 3 ? 'bg-amber-600 text-white' : '' }}">
                        {{ $visitor['rank'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 truncate">{{ $visitor['name'] }}</p>
                        <p class="text-gray-500 text-sm">{{ $visitor['class'] }} • {{ $visitor['nis'] }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-purple-600">{{ $visitor['visit_count'] }}</span>
                        <p class="text-gray-400 text-xs">kunjungan</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-center py-4">Belum ada data</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Charts -->
<div class="grid md:grid-cols-2 gap-6 mb-8">
    <!-- Borrowing Chart -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Peminjaman (6 Bulan)</h3>
        <canvas id="borrowingChart" height="200"></canvas>
    </div>
    
    <!-- Visits Chart -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Kunjungan (6 Bulan)</h3>
        <canvas id="visitsChart" height="200"></canvas>
    </div>
</div>

<!-- Recent Borrowings -->
<div class="bg-white rounded-2xl p-6 border border-gray-100">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Peminjaman Terbaru</h3>
        <a href="{{ route('admin.borrowings.index') }}" class="text-primary-dark hover:underline text-sm">Lihat Semua →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-gray-500 text-sm border-b">
                    <th class="pb-3 font-medium">Siswa</th>
                    <th class="pb-3 font-medium">Buku</th>
                    <th class="pb-3 font-medium">Tanggal Pinjam</th>
                    <th class="pb-3 font-medium">Batas</th>
                    <th class="pb-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBorrowings as $borrowing)
                <tr class="border-b border-gray-50">
                    <td class="py-3">
                        <p class="font-medium text-gray-800">{{ $borrowing->student->name ?? '-' }}</p>
                        <p class="text-gray-400 text-xs">{{ $borrowing->student_nis }}</p>
                    </td>
                    <td class="py-3 text-gray-600">{{ Str::limit($borrowing->book->title ?? '-', 30) }}</td>
                    <td class="py-3 text-gray-600">{{ $borrowing->borrow_date->format('d M Y') }}</td>
                    <td class="py-3 text-gray-600">{{ $borrowing->due_date->format('d M Y') }}</td>
                    <td class="py-3">
                        @if($borrowing->status === 'returned')
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-lg">Dikembalikan</span>
                        @elseif($borrowing->is_overdue)
                            <span class="px-2 py-1 bg-red-100 text-red-600 text-xs rounded-lg">Terlambat</span>
                        @else
                            <span class="px-2 py-1 bg-green-100 text-green-600 text-xs rounded-lg">Dipinjam</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-400">Belum ada data peminjaman</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Borrowing Chart
    const borrowingData = @json($monthlyBorrowings);
    new Chart(document.getElementById('borrowingChart'), {
        type: 'bar',
        data: {
            labels: borrowingData.map(d => d.month),
            datasets: [{
                label: 'Peminjaman',
                data: borrowingData.map(d => d.count),
                backgroundColor: '#4CAF50',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
    
    // Visits Chart
    const visitsData = @json($monthlyVisits);
    new Chart(document.getElementById('visitsChart'), {
        type: 'line',
        data: {
            labels: visitsData.map(d => d.month),
            datasets: [{
                label: 'Kunjungan',
                data: visitsData.map(d => d.count),
                borderColor: '#7C3AED',
                backgroundColor: 'rgba(124, 58, 237, 0.1)',
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
@endpush
@endsection
