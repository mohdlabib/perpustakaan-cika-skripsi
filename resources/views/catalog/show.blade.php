@extends('layouts.student')

@section('title', $book->title . ' - Perpustakaan')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="bookDetail()">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="{{ route('catalog.index') }}" class="hover:text-primary-dark transition">Katalog</a></li>
            <li class="text-gray-300">/</li>
            <li><a href="{{ route('catalog.index', ['category' => $book->category_id]) }}" class="hover:text-primary-dark transition">{{ $book->category->name ?? '-' }}</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-medium">{{ Str::limit($book->title, 30) }}</li>
        </ol>
    </nav>
    
    <!-- Book Detail Card -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="grid md:grid-cols-3 gap-8 p-8">
            <!-- Book Cover -->
            <div class="md:col-span-1">
                <div class="aspect-[3/4] bg-gradient-to-br from-primary-light to-gray-100 rounded-2xl overflow-hidden shadow-md">
                    @if($book->cover_image)
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-24 h-24 text-primary-dark/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <!-- Quick Stats -->
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="bg-primary-light/50 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-primary-dark">{{ $book->stock }}</div>
                        <div class="text-xs text-gray-600">Total Stok</div>
                    </div>
                    <div class="bg-green-100 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $book->available_stock }}</div>
                        <div class="text-xs text-gray-600">Tersedia</div>
                    </div>
                </div>
            </div>
            
            <!-- Book Info -->
            <div class="md:col-span-2">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <span class="px-3 py-1.5 bg-primary-light text-primary-dark text-sm font-medium rounded-lg">
                        {{ $book->category->name ?? '-' }}
                    </span>
                    @if($book->is_available)
                        <span class="px-4 py-2 bg-green-100 text-green-700 font-semibold rounded-xl inline-flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Tersedia
                        </span>
                    @else
                        <span class="px-4 py-2 bg-red-100 text-red-700 font-semibold rounded-xl inline-flex items-center gap-2">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            Semua Dipinjam
                        </span>
                    @endif
                </div>
                
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $book->title }}</h1>
                <p class="text-xl text-gray-600 mb-6">oleh <span class="font-medium">{{ $book->author }}</span></p>
                
                <!-- Book Details Grid -->
                <div class="bg-gray-50 rounded-2xl p-5 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Informasi Buku
                    </h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @if($book->isbn)
                        <div class="flex items-center gap-3 bg-white rounded-lg p-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">ISBN</div>
                                <div class="font-medium text-gray-800">{{ $book->isbn }}</div>
                            </div>
                        </div>
                        @endif
                        @if($book->publisher)
                        <div class="flex items-center gap-3 bg-white rounded-lg p-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Penerbit</div>
                                <div class="font-medium text-gray-800">{{ $book->publisher }}</div>
                            </div>
                        </div>
                        @endif
                        @if($book->publication_year)
                        <div class="flex items-center gap-3 bg-white rounded-lg p-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Tahun Terbit</div>
                                <div class="font-medium text-gray-800">{{ $book->publication_year }}</div>
                            </div>
                        </div>
                        @endif
                        @if($book->shelf_location)
                        <div class="flex items-center gap-3 bg-white rounded-lg p-3">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Lokasi Rak</div>
                                <div class="font-bold text-yellow-700 bg-yellow-100 px-2 py-0.5 rounded inline-block">{{ $book->shelf_location }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                @if($book->description)
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        Sinopsis
                    </h3>
                    <p class="text-gray-600 leading-relaxed bg-gray-50 rounded-xl p-4">{{ $book->description }}</p>
                </div>
                @endif
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 pt-4 border-t border-gray-100">
                    @if(session('student'))
                        @if($book->is_available)
                            <button type="button" @click="showModal = true"
                                class="px-8 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition flex items-center gap-2 cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Pinjam Buku
                            </button>
                        @else
                            <button disabled class="px-8 py-3 bg-gray-300 text-gray-500 rounded-xl font-semibold cursor-not-allowed flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                Tidak Tersedia
                            </button>
                        @endif
                    @else
                        <a href="{{ route('student.login') }}" class="px-8 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Login untuk Meminjam
                        </a>
                    @endif
                    
                    <a href="{{ route('catalog.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Books -->
    @if($relatedBooks->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Buku Lainnya di Kategori Ini
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($relatedBooks as $related)
                <a href="{{ route('catalog.show', $related) }}" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 cursor-pointer">
                    <div class="aspect-[3/4] bg-gradient-to-br from-primary-light to-gray-100">
                        @if($related->cover_image)
                            <img src="{{ $related->cover_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-primary-dark/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="font-medium text-gray-800 line-clamp-2 text-sm group-hover:text-primary-dark transition">{{ $related->title }}</h3>
                        <p class="text-gray-500 text-xs mt-1">{{ $related->author }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Borrow Modal -->
    <div x-show="showModal" x-cloak 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" 
         @click.self="showModal = false">
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl max-w-md w-full p-8 shadow-2xl" @click.stop>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">Konfirmasi Peminjaman</h3>
            </div>
            
            <div class="bg-gray-50 rounded-2xl p-4 mb-6">
                <p class="text-gray-600 text-center">
                    Anda akan meminjam buku:<br>
                    <strong class="text-gray-800 text-lg">{{ $book->title }}</strong>
                </p>
                <div class="mt-4 flex items-center justify-center gap-2 text-sm text-orange-600 bg-orange-50 rounded-lg p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Batas pengembalian: <strong>7 hari</strong>
                </div>
            </div>
            
            <!-- Error message -->
            <div x-show="errorMessage" x-cloak class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl mb-4 text-sm">
                <p x-text="errorMessage"></p>
            </div>
            
            <div class="flex gap-4">
                <button type="button" @click="borrowBook()" :disabled="loading" 
                    class="flex-1 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 cursor-pointer">
                    <template x-if="loading">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <template x-if="!loading">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Memproses...' : 'Ya, Pinjam'"></span>
                </button>
                <button type="button" @click="showModal = false" class="flex-1 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function bookDetail() {
    return {
        showModal: false,
        loading: false,
        errorMessage: '',
        
        async borrowBook() {
            this.loading = true;
            this.errorMessage = '';
            
            try {
                const response = await axios.post('{{ route("borrowings.store") }}', {
                    book_id: {{ $book->id }}
                });
                
                if (response.data.success) {
                    this.showModal = false;
                    // Show success notification
                    if (window.notify && window.notify.success) {
                        window.notify.success(response.data.message);
                    } else {
                        alert(response.data.message);
                    }
                    setTimeout(() => location.reload(), 1500);
                } else {
                    this.errorMessage = response.data.message || 'Terjadi kesalahan';
                }
            } catch (error) {
                console.error('Borrow error:', error);
                
                if (error.response) {
                    if (error.response.status === 401) {
                        this.errorMessage = 'Silakan login terlebih dahulu untuk meminjam buku.';
                        setTimeout(() => {
                            window.location.href = '{{ route("student.login") }}';
                        }, 2000);
                    } else {
                        this.errorMessage = error.response.data?.message || 'Terjadi kesalahan saat memproses peminjaman.';
                    }
                } else if (error.request) {
                    this.errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                } else {
                    this.errorMessage = 'Terjadi kesalahan: ' + error.message;
                }
            }
            
            this.loading = false;
        }
    }
}
</script>
@endpush
@endsection

