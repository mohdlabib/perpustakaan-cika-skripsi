@extends('layouts.student')

@section('title', $book->title . ' - Perpustakaan')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ showBorrowModal: false }">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="{{ route('catalog.index') }}" class="hover:text-primary-dark">Katalog</a></li>
            <li>/</li>
            <li><a href="{{ route('catalog.index', ['category' => $book->category_id]) }}" class="hover:text-primary-dark">{{ $book->category->name ?? '-' }}</a></li>
            <li>/</li>
            <li class="text-gray-800 font-medium">{{ Str::limit($book->title, 30) }}</li>
        </ol>
    </nav>
    
    <!-- Book Detail -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="grid md:grid-cols-3 gap-8 p-8">
            <!-- Book Cover -->
            <div class="md:col-span-1">
                <div class="aspect-[3/4] bg-gradient-to-br from-primary-light to-gray-100 rounded-2xl overflow-hidden">
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
            </div>
            
            <!-- Book Info -->
            <div class="md:col-span-2">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <span class="px-3 py-1 bg-primary-light text-primary-dark text-sm font-medium rounded-lg">
                            {{ $book->category->name ?? '-' }}
                        </span>
                    </div>
                    @if($book->is_available)
                        <span class="px-4 py-2 bg-green-100 text-green-700 font-medium rounded-xl">
                            ✓ Tersedia ({{ $book->available_stock }} buku)
                        </span>
                    @else
                        <span class="px-4 py-2 bg-red-100 text-red-700 font-medium rounded-xl">
                            ✗ Semua Dipinjam
                        </span>
                    @endif
                </div>
                
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $book->title }}</h1>
                <p class="text-xl text-gray-600 mb-6">oleh {{ $book->author }}</p>
                
                <!-- Book Details Table -->
                <div class="grid sm:grid-cols-2 gap-4 mb-6">
                    @if($book->isbn)
                    <div class="flex gap-3">
                        <span class="text-gray-500">ISBN:</span>
                        <span class="font-medium">{{ $book->isbn }}</span>
                    </div>
                    @endif
                    @if($book->publisher)
                    <div class="flex gap-3">
                        <span class="text-gray-500">Penerbit:</span>
                        <span class="font-medium">{{ $book->publisher }}</span>
                    </div>
                    @endif
                    @if($book->publication_year)
                    <div class="flex gap-3">
                        <span class="text-gray-500">Tahun:</span>
                        <span class="font-medium">{{ $book->publication_year }}</span>
                    </div>
                    @endif
                    @if($book->shelf_location)
                    <div class="flex gap-3">
                        <span class="text-gray-500">Lokasi Rak:</span>
                        <span class="font-medium bg-yellow-100 px-2 py-0.5 rounded">{{ $book->shelf_location }}</span>
                    </div>
                    @endif
                </div>
                
                @if($book->description)
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Sinopsis</h3>
                    <p class="text-gray-600 leading-relaxed">{{ $book->description }}</p>
                </div>
                @endif
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4">
                    @if(session('student'))
                        @if($book->is_available)
                            <button 
                                @click="showBorrowModal = true"
                                class="px-8 py-3 bg-accent-green text-white rounded-xl font-semibold hover:bg-opacity-90 transition transform hover:scale-105 active:scale-95">
                                📚 Pinjam Buku
                            </button>
                        @else
                            <button disabled class="px-8 py-3 bg-gray-300 text-gray-500 rounded-xl font-semibold cursor-not-allowed">
                                Tidak Tersedia
                            </button>
                        @endif
                    @else
                        <a href="{{ route('student.login') }}" class="px-8 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition">
                            Login untuk Meminjam
                        </a>
                    @endif
                    
                    <a href="{{ route('catalog.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:border-primary-dark hover:text-primary-dark transition">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Books -->
    @if($relatedBooks->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Buku Lainnya di Kategori Ini</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($relatedBooks as $related)
                <a href="{{ route('catalog.show', $related) }}" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100">
                    <div class="aspect-[3/4] bg-gradient-to-br from-primary-light to-gray-100">
                        @if($related->cover_image)
                            <img src="{{ Storage::url($related->cover_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-primary-dark/30" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="font-medium text-gray-800 line-clamp-2 text-sm group-hover:text-primary-dark">{{ $related->title }}</h3>
                        <p class="text-gray-500 text-xs mt-1">{{ $related->author }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Borrow Modal -->
    <div x-show="showBorrowModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="showBorrowModal = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 animate-fade-in" @click.stop>
            <h3 class="text-xl font-bold text-gray-800 mb-4">Konfirmasi Peminjaman</h3>
            <p class="text-gray-600 mb-6">
                Anda akan meminjam buku <strong>"{{ $book->title }}"</strong>. 
                Buku harus dikembalikan dalam waktu <strong>7 hari</strong>.
            </p>
            <div class="flex gap-4">
                <button @click="borrowBook()" class="flex-1 py-3 bg-accent-green text-white rounded-xl font-semibold hover:bg-opacity-90 transition">
                    Ya, Pinjam
                </button>
                <button @click="showBorrowModal = false" class="flex-1 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function borrowBook() {
    axios.post('{{ route("borrowings.store") }}', {
        book_id: {{ $book->id }}
    })
    .then(response => {
        if (response.data.success) {
            window.notify.success(response.data.message);
            setTimeout(() => location.reload(), 1500);
        }
    })
    .catch(error => {
        const message = error.response?.data?.message || 'Terjadi kesalahan';
        window.notify.error(message);
    });
}
</script>
@endpush
@endsection
