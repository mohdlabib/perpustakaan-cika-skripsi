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
    <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
        <div class="grid md:grid-cols-3 gap-8 p-8">
            <!-- Book Cover -->
            <div class="md:col-span-1">
                <div class="aspect-[3/4] bg-gradient-to-br from-primary-light to-gray-100 rounded-2xl overflow-hidden shadow-md">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-24 h-24 text-primary-dark/30" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2zm0 2v16h12V4H6zm2 2h8v2H8V6z"/>
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
                    <div class="bg-accent-green/10 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-accent-green">{{ $book->available_stock }}</div>
                        <div class="text-xs text-gray-600">Tersedia</div>
                    </div>
                </div>
            </div>
            
            <!-- Book Info -->
            <div class="md:col-span-2">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <span class="px-3 py-1.5 bg-primary-light text-primary-dark text-sm font-medium rounded-lg">
                        {{ $book->category->icon ?? '📚' }} {{ $book->category->name ?? '-' }}
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
                    <h3 class="font-semibold text-gray-800 mb-4">📋 Informasi Buku</h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @if($book->isbn)
                        <div class="flex items-center gap-3 bg-white rounded-lg p-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">📄</div>
                            <div>
                                <div class="text-xs text-gray-500">ISBN</div>
                                <div class="font-medium text-gray-800">{{ $book->isbn }}</div>
                            </div>
                        </div>
                        @endif
                        @if($book->publisher)
                        <div class="flex items-center gap-3 bg-white rounded-lg p-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">🏢</div>
                            <div>
                                <div class="text-xs text-gray-500">Penerbit</div>
                                <div class="font-medium text-gray-800">{{ $book->publisher }}</div>
                            </div>
                        </div>
                        @endif
                        @if($book->publication_year)
                        <div class="flex items-center gap-3 bg-white rounded-lg p-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600">📅</div>
                            <div>
                                <div class="text-xs text-gray-500">Tahun Terbit</div>
                                <div class="font-medium text-gray-800">{{ $book->publication_year }}</div>
                            </div>
                        </div>
                        @endif
                        @if($book->shelf_location)
                        <div class="flex items-center gap-3 bg-white rounded-lg p-3">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">📍</div>
                            <div>
                                <div class="text-xs text-gray-500">Lokasi Rak</div>
                                <div class="font-bold text-yellow-700 bg-yellow-100 px-2 py-0.5 rounded">{{ $book->shelf_location }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                @if($book->description)
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-800 mb-3">📝 Sinopsis</h3>
                    <p class="text-gray-600 leading-relaxed bg-gray-50 rounded-xl p-4">{{ $book->description }}</p>
                </div>
                @endif
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 pt-4 border-t border-gray-100">
                    @if(session('student'))
                        @if($book->is_available)
                            <button type="button" @click="showModal = true"
                                class="px-8 py-3.5 bg-gradient-to-r from-accent-green to-green-600 text-white rounded-xl font-semibold hover:shadow-lg hover:shadow-green-500/30 transition-all duration-300 transform hover:scale-105 active:scale-95 flex items-center gap-2">
                                📚 Pinjam Buku
                            </button>
                        @else
                            <button disabled class="px-8 py-3.5 bg-gray-300 text-gray-500 rounded-xl font-semibold cursor-not-allowed">
                                ❌ Tidak Tersedia
                            </button>
                        @endif
                    @else
                        <a href="{{ route('student.login') }}" class="px-8 py-3.5 bg-gradient-to-r from-primary-dark to-green-800 text-white rounded-xl font-semibold hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                            🔑 Login untuk Meminjam
                        </a>
                    @endif
                    
                    <a href="{{ route('catalog.index') }}" class="px-8 py-3.5 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:border-primary-dark hover:text-primary-dark transition-all duration-300">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Books -->
    @if($relatedBooks->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">📖 Buku Lainnya di Kategori Ini</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($relatedBooks as $related)
                <a href="{{ route('catalog.show', $related) }}" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="aspect-[3/4] bg-gradient-to-br from-primary-light to-gray-100">
                        @if($related->cover_image)
                            <img src="{{ Storage::url($related->cover_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-primary-dark/30" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z"/>
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
             class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl" @click.stop>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-accent-green/20 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl">
                    📚
                </div>
                <h3 class="text-2xl font-bold text-gray-800">Konfirmasi Peminjaman</h3>
            </div>
            
            <div class="bg-gray-50 rounded-2xl p-4 mb-6">
                <p class="text-gray-600 text-center">
                    Anda akan meminjam buku:<br>
                    <strong class="text-gray-800 text-lg">{{ $book->title }}</strong>
                </p>
                <div class="mt-4 flex items-center justify-center gap-2 text-sm text-orange-600 bg-orange-50 rounded-lg p-2">
                    ⏰ Batas pengembalian: <strong>7 hari</strong>
                </div>
            </div>
            
            <div class="flex gap-4">
                <button type="button" @click="borrowBook()" :disabled="loading" 
                    class="flex-1 py-3.5 bg-gradient-to-r from-accent-green to-green-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-text="loading ? '⏳ Memproses...' : '✅ Ya, Pinjam'"></span>
                </button>
                <button type="button" @click="showModal = false" class="flex-1 py-3.5 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition">
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
        
        async borrowBook() {
            this.loading = true;
            try {
                const response = await axios.post('{{ route("borrowings.store") }}', {
                    book_id: {{ $book->id }}
                });
                if (response.data.success) {
                    this.showModal = false;
                    window.notify.success(response.data.message);
                    setTimeout(() => location.reload(), 1500);
                }
            } catch (error) {
                const message = error.response?.data?.message || 'Terjadi kesalahan';
                window.notify.error(message);
            }
            this.loading = false;
        }
    }
}
</script>
@endpush
@endsection
