@extends('layouts.admin')

@section('page-title', 'Peminjaman')

@section('content')
<!-- Stats Cards - Same style as Dashboard -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm {{ \App\Models\Borrowing::pending()->count() > 0 ? 'border-yellow-200 bg-yellow-50' : '' }}">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 {{ \App\Models\Borrowing::pending()->count() > 0 ? 'bg-yellow-100' : 'bg-yellow-50' }} rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 {{ \App\Models\Borrowing::pending()->count() > 0 ? 'text-yellow-600' : 'text-yellow-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold {{ \App\Models\Borrowing::pending()->count() > 0 ? 'text-yellow-600' : 'text-gray-800' }}">{{ \App\Models\Borrowing::pending()->count() }}</div>
                <div class="text-gray-500 text-sm">Menunggu</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Borrowing::count() }}</div>
                <div class="text-gray-500 text-sm">Total</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Borrowing::active()->count() }}</div>
                <div class="text-gray-500 text-sm">Dipinjam</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm {{ \App\Models\Borrowing::overdue()->count() > 0 ? 'border-red-200 bg-red-50' : '' }}">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 {{ \App\Models\Borrowing::overdue()->count() > 0 ? 'bg-red-100' : 'bg-orange-100' }} rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 {{ \App\Models\Borrowing::overdue()->count() > 0 ? 'text-red-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold {{ \App\Models\Borrowing::overdue()->count() > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ \App\Models\Borrowing::overdue()->count() }}</div>
                <div class="text-gray-500 text-sm">Terlambat</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Borrowing::returned()->count() }}</div>
                <div class="text-gray-500 text-sm">Dikembalikan</div>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">Manajemen Peminjaman</h2>
        <p class="text-gray-500 text-sm">Kelola peminjaman dan pengembalian buku</p>
    </div>
    <a href="{{ route('admin.borrowings.create') }}" class="px-4 py-2.5 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Catat Peminjaman
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl p-5 mb-6 border border-gray-100 shadow-sm" x-data="searchRecommendation()">
    <form action="{{ route('admin.borrowings.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1 relative">
            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" x-model="query" @input.debounce.300ms="search()" @focus="showDropdown = query.length >= 2" @click.away="showDropdown = false"
                value="{{ request('search') }}" placeholder="Cari nama siswa atau judul buku..." 
                class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                autocomplete="off">
            
            <!-- Search Recommendations Dropdown -->
            <div x-show="showDropdown && recommendations.length > 0" x-cloak
                class="absolute z-50 top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto">
                <template x-for="item in recommendations" :key="item.id">
                    <button type="button" @click="selectRecommendation(item)" 
                        class="w-full px-4 py-3 text-left hover:bg-gray-50 flex items-center gap-3 border-b border-gray-100 last:border-0 transition">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                            :class="item.type === 'student' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600'">
                            <span x-text="item.type === 'student' ? '👤' : '📚'"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-800 truncate" x-text="item.name"></p>
                            <p class="text-gray-500 text-xs" x-text="item.sub"></p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-lg"
                            :class="item.type === 'student' ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600'"
                            x-text="item.type === 'student' ? 'Siswa' : 'Buku'"></span>
                    </button>
                </template>
            </div>
            
            <!-- Loading indicator -->
            <div x-show="loading" class="absolute right-4 top-1/2 -translate-y-1/2">
                <svg class="animate-spin w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        <select name="status" class="px-4 py-3 border border-gray-200 rounded-xl cursor-pointer">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Approval</option>
            <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
        </select>
        <button type="submit" class="px-6 py-3 bg-gray-800 text-white rounded-xl font-medium hover:bg-gray-900 transition cursor-pointer">
            Filter
        </button>
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
                        {{ $borrowing->borrow_date ? $borrowing->borrow_date->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 {{ $borrowing->is_overdue ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        {{ $borrowing->due_date ? $borrowing->due_date->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($borrowing->status === 'pending')
                            <span class="px-3 py-1.5 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-lg inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Menunggu
                            </span>
                        @elseif($borrowing->status === 'returned')
                            <span class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Dikembalikan
                            </span>
                        @elseif($borrowing->status === 'rejected')
                            <span class="px-3 py-1.5 bg-red-100 text-red-600 text-xs font-medium rounded-lg inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Ditolak
                            </span>
                        @elseif($borrowing->is_overdue)
                            <span class="px-3 py-1.5 bg-red-100 text-red-600 text-xs font-medium rounded-lg inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Terlambat {{ abs($borrowing->days_remaining) }} hari
                            </span>
                        @else
                            <span class="px-3 py-1.5 bg-green-100 text-green-600 text-xs font-medium rounded-lg inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Dipinjam
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-2">
                            @if($borrowing->status === 'pending')
                                <!-- Approve Modal Trigger -->
                                <button type="button" onclick="openApproveModal({{ $borrowing->id }}, '{{ $borrowing->student->name ?? '' }}', '{{ $borrowing->book->title ?? '' }}')" 
                                    class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg font-medium hover:bg-green-700 transition flex items-center gap-1 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Setujui
                                </button>
                                <form action="{{ route('admin.borrowings.reject', $borrowing) }}" method="POST" onsubmit="return confirm('Tolak peminjaman ini?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="px-3 py-2 bg-red-600 text-white text-sm rounded-lg font-medium hover:bg-red-700 transition flex items-center gap-1 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Tolak
                                    </button>
                                </form>
                            @elseif($borrowing->status === 'borrowed')
                                <form action="{{ route('admin.borrowings.return', $borrowing) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg font-medium hover:bg-green-700 transition flex items-center gap-1 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Kembalikan
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Tidak ada data peminjaman</p>
                        <p class="text-gray-400 text-sm mt-1">Catat peminjaman baru untuk memulai</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
        {{ $borrowings->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
function searchRecommendation() {
    return {
        query: '{{ request("search") }}',
        recommendations: [],
        showDropdown: false,
        loading: false,
        
        async search() {
            if (this.query.length < 2) {
                this.recommendations = [];
                this.showDropdown = false;
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await axios.get('{{ route("admin.borrowings.search-recommendations") }}', {
                    params: { q: this.query }
                });
                this.recommendations = response.data;
                this.showDropdown = true;
            } catch (error) {
                console.error('Search error:', error);
            }
            
            this.loading = false;
        },
        
        selectRecommendation(item) {
            this.query = item.name;
            this.showDropdown = false;
        }
    }
}
</script>
@endpush

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-all" onclick="closeApproveModal()"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-green-600 p-5 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold">Setujui Peminjaman</h3>
                        <p class="text-white/70 text-sm">Tentukan tanggal pengembalian</p>
                    </div>
                </div>
            </div>
            
            <form id="approveForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                <div class="text-sm text-gray-600 bg-gray-50 rounded-xl p-4">
                    <p><strong>Siswa:</strong> <span id="approveStudentName">-</span></p>
                    <p><strong>Buku:</strong> <span id="approveBookTitle">-</span></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Batas Pengembalian <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="due_date" id="approveDueDate" required
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        value="{{ now()->addDays(7)->format('Y-m-d') }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                </div>
                
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeApproveModal()" class="flex-1 px-4 py-3 border border-gray-300 text-gray-600 rounded-xl font-medium hover:bg-gray-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition cursor-pointer">
                        Setujui Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openApproveModal(id, studentName, bookTitle) {
    document.getElementById('approveForm').action = '/admin/borrowings/' + id + '/approve';
    document.getElementById('approveStudentName').textContent = studentName;
    document.getElementById('approveBookTitle').textContent = bookTitle;
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}
</script>
@endsection


