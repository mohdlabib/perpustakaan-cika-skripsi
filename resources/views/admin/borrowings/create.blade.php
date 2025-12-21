@extends('layouts.admin')

@section('page-title', 'Catat Peminjaman')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="{{ route('admin.borrowings.index') }}" class="hover:text-primary-dark transition">Peminjaman</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-medium">Catat Peminjaman</li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-dark to-green-700 px-8 py-6 text-white rounded-t-2xl">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold">Catat Peminjaman Baru</h2>
                    <p class="text-white/70 text-sm">Catat peminjaman buku oleh siswa</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.borrowings.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <!-- Student Selection - Searchable -->
            <div x-data="studentSelector()">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Pilih Siswa <span class="text-red-500">*</span>
                    </span>
                </label>
                <div class="relative">
                    <input type="hidden" name="student_nis" x-model="selectedId" required>
                    <div class="relative">
                        <input type="text" 
                            x-model="search"
                            @focus="showDropdown = true"
                            @click.away="showDropdown = false"
                            @keydown.escape="showDropdown = false"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition pr-10"
                            placeholder="Cari siswa berdasarkan nama atau NIS...">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Dropdown -->
                    <div x-show="showDropdown && filteredItems.length > 0" x-cloak
                        class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl max-h-64 overflow-y-auto">
                        <template x-for="item in filteredItems" :key="item.nis">
                            <div @click="selectItem(item)" 
                                class="px-4 py-3 bg-white hover:bg-primary-light cursor-pointer transition border-b border-gray-100 last:border-b-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800" x-text="item.name"></p>
                                        <p class="text-xs text-gray-500">
                                            <span x-text="item.nis"></span> • <span x-text="item.class || '-'"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <!-- No results -->
                    <div x-show="showDropdown && search.length > 0 && filteredItems.length === 0" x-cloak
                        class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl p-4 text-center text-gray-500">
                        Tidak ada siswa yang ditemukan
                    </div>
                </div>
                
                <!-- Selected Display -->
                <div x-show="selectedId" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-blue-800" x-text="selectedName"></p>
                            <p class="text-xs text-blue-600" x-text="selectedInfo"></p>
                        </div>
                    </div>
                    <button type="button" @click="clearSelection()" class="text-blue-600 hover:text-blue-800 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                @error('student_nis')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <!-- Book Selection - Searchable -->
            <div x-data="bookSelector()">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Pilih Buku <span class="text-red-500">*</span>
                    </span>
                </label>
                <div class="relative">
                    <input type="hidden" name="book_id" x-model="selectedId" required>
                    <div class="relative">
                        <input type="text" 
                            x-model="search"
                            @focus="showDropdown = true"
                            @click.away="showDropdown = false"
                            @keydown.escape="showDropdown = false"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition pr-10"
                            placeholder="Cari buku berdasarkan judul atau penulis...">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Dropdown -->
                    <div x-show="showDropdown && filteredItems.length > 0" x-cloak
                        class="absolute z-40 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl max-h-64 overflow-y-auto">
                        <template x-for="item in filteredItems" :key="item.id">
                            <div @click="selectItem(item)" 
                                class="px-4 py-3 bg-white hover:bg-primary-light cursor-pointer transition border-b border-gray-100 last:border-b-0"
                                :class="{ 'opacity-50 pointer-events-none': item.available_stock <= 0 }">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-800 truncate" x-text="item.title"></p>
                                        <p class="text-xs text-gray-500 truncate" x-text="item.author"></p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="px-2 py-1 rounded-lg text-xs font-medium"
                                            :class="item.available_stock > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                            x-text="'Stok: ' + item.available_stock"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <!-- No results -->
                    <div x-show="showDropdown && search.length > 0 && filteredItems.length === 0" x-cloak
                        class="absolute z-40 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl p-4 text-center text-gray-500">
                        Tidak ada buku yang ditemukan
                    </div>
                </div>
                
                <!-- Selected Display -->
                <div x-show="selectedId" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-green-800" x-text="selectedName"></p>
                            <p class="text-xs text-green-600" x-text="selectedInfo"></p>
                        </div>
                    </div>
                    <button type="button" @click="clearSelection()" class="text-green-600 hover:text-green-800 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                @error('book_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <!-- Due Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Tanggal Batas Pengembalian <span class="text-red-500">*</span>
                    </span>
                </label>
                <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}" required
                    min="{{ now()->addDay()->format('Y-m-d') }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                @error('due_date')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-700">
                    <p class="font-medium">Informasi Peminjaman</p>
                    <ul class="mt-1 space-y-1 text-blue-600">
                        <li>• Setiap siswa maksimal meminjam 3 buku</li>
                        <li>• Durasi peminjaman default adalah 7 hari</li>
                        <li>• Keterlambatan akan tercatat di sistem</li>
                    </ul>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-4 pt-4 border-t border-gray-100">
                <button type="submit" class="px-8 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Peminjaman
                </button>
                <a href="{{ route('admin.borrowings.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function studentSelector() {
    return {
        items: @json($students),
        search: '',
        showDropdown: false,
        selectedId: '{{ old('student_nis') }}',
        selectedName: '',
        selectedInfo: '',
        
        init() {
            if (this.selectedId) {
                const item = this.items.find(i => i.nis == this.selectedId);
                if (item) {
                    this.selectedName = item.name;
                    this.selectedInfo = `NIS: ${item.nis} • Kelas: ${item.class || '-'}`;
                    this.search = item.name;
                }
            }
        },
        
        get filteredItems() {
            if (!this.search) return this.items.slice(0, 20);
            const query = this.search.toLowerCase();
            return this.items.filter(item => 
                item.name.toLowerCase().includes(query) ||
                item.nis.toLowerCase().includes(query) ||
                (item.class && item.class.toLowerCase().includes(query))
            ).slice(0, 20);
        },
        
        selectItem(item) {
            this.selectedId = item.nis;
            this.selectedName = item.name;
            this.selectedInfo = `NIS: ${item.nis} • Kelas: ${item.class || '-'}`;
            this.search = item.name;
            this.showDropdown = false;
        },
        
        clearSelection() {
            this.selectedId = '';
            this.selectedName = '';
            this.selectedInfo = '';
            this.search = '';
        }
    }
}

function bookSelector() {
    return {
        items: @json($books),
        search: '',
        showDropdown: false,
        selectedId: '{{ old('book_id') }}',
        selectedName: '',
        selectedInfo: '',
        
        init() {
            if (this.selectedId) {
                const item = this.items.find(i => i.id == this.selectedId);
                if (item) {
                    this.selectedName = item.title;
                    this.selectedInfo = `${item.author} • Stok tersedia: ${item.available_stock}`;
                    this.search = item.title;
                }
            }
        },
        
        get filteredItems() {
            if (!this.search) return this.items.slice(0, 20);
            const query = this.search.toLowerCase();
            return this.items.filter(item => 
                item.title.toLowerCase().includes(query) ||
                item.author.toLowerCase().includes(query)
            ).slice(0, 20);
        },
        
        selectItem(item) {
            if (item.available_stock <= 0) return;
            this.selectedId = item.id;
            this.selectedName = item.title;
            this.selectedInfo = `${item.author} • Stok tersedia: ${item.available_stock}`;
            this.search = item.title;
            this.showDropdown = false;
        },
        
        clearSelection() {
            this.selectedId = '';
            this.selectedName = '';
            this.selectedInfo = '';
            this.search = '';
        }
    }
}
</script>
@endpush
@endsection
