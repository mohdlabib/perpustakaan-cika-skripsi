@extends('layouts.admin')

@section('page-title', 'Laporan Buku')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
                <div class="text-gray-500 text-sm">Total Judul</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['total_copies'] }}</div>
                <div class="text-gray-500 text-sm">Total Eksemplar</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['available'] }}</div>
                <div class="text-gray-500 text-sm">Tersedia</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['borrowed'] }}</div>
                <div class="text-gray-500 text-sm">Sedang Dipinjam</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ $stats['categories'] }}</div>
                <div class="text-gray-500 text-sm">Kategori</div>
            </div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm mb-8">
    <div class="flex justify-between items-center mb-6">
        <h3 class="font-semibold text-gray-800">Statistik Peminjaman per Bulan</h3>
        <button onclick="downloadChart()" class="px-4 py-2 bg-primary-dark text-white rounded-xl text-sm font-medium hover:bg-opacity-90 transition flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download Grafik
        </button>
    </div>
    <div class="h-80">
        <canvas id="borrowingChart"></canvas>
    </div>
</div>

<!-- Actions -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">Data Buku</h2>
        <p class="text-gray-500 text-sm">Daftar seluruh koleksi buku perpustakaan</p>
    </div>
    <a href="{{ route('admin.reports.books.export', ['category' => request('category')]) }}" class="px-4 py-2.5 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition flex items-center gap-2 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Export Excel
    </a>
</div>

<!-- Books Table -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-600 text-sm">
                    <th class="px-6 py-4 font-semibold">No</th>
                    <th class="px-6 py-4 font-semibold">Judul</th>
                    <th class="px-6 py-4 font-semibold">Pengarang</th>
                    <th class="px-6 py-4 font-semibold">Kategori</th>
                    <th class="px-6 py-4 font-semibold">Stok</th>
                    <th class="px-6 py-4 font-semibold">Dipinjam</th>
                    <th class="px-6 py-4 font-semibold">Tersedia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($books as $index => $book)
                @php $borrowed = $book->borrowings->where('status', 'borrowed')->count(); @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-500">{{ ($books->currentPage() - 1) * $books->perPage() + $index + 1 }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ Str::limit($book->title, 40) }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $book->author }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-primary-light text-primary-dark text-xs font-medium rounded-lg">{{ $book->category->name ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-800 font-medium">{{ $book->stock }}</td>
                    <td class="px-6 py-4">
                        @if($borrowed > 0)
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-lg">{{ $borrowed }}</span>
                        @else
                            <span class="text-gray-400">0</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-lg">{{ $book->stock - $borrowed }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
        {{ $books->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('borrowingChart').getContext('2d');
const monthlyData = @json($monthlyData);

const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: monthlyData.map(d => d.month),
        datasets: [
            {
                label: 'Dipinjam',
                data: monthlyData.map(d => d.borrowed),
                backgroundColor: 'rgba(22, 163, 74, 0.8)',
                borderRadius: 8,
            },
            {
                label: 'Dikembalikan',
                data: monthlyData.map(d => d.returned),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderRadius: 8,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

function downloadChart() {
    const link = document.createElement('a');
    link.download = 'statistik-peminjaman-' + new Date().toISOString().split('T')[0] + '.png';
    link.href = document.getElementById('borrowingChart').toDataURL('image/png');
    link.click();
}
</script>
@endpush
@endsection
