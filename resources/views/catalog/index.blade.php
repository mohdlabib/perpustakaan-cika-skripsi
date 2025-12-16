@extends('layouts.student')

@section('title', 'Katalog Buku - Perpustakaan')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="catalogSearch()">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">📚 Katalog Buku</h1>
        <p class="text-gray-600 text-lg">Temukan buku favoritmu dari koleksi perpustakaan</p>
    </div>
    
    <!-- Stats Bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-4 border border-gray-100 text-center shadow-sm">
            <div class="text-3xl font-bold text-primary-dark">{{ $books->total() }}</div>
            <div class="text-gray-500 text-sm">Total Buku</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 text-center shadow-sm">
            <div class="text-3xl font-bold text-green-600">{{ \App\Models\Book::where('stock', '>', 0)->count() }}</div>
            <div class="text-gray-500 text-sm">Tersedia</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 text-center shadow-sm">
            <div class="text-3xl font-bold text-purple-600">{{ \App\Models\Category::count() }}</div>
            <div class="text-gray-500 text-sm">Kategori</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 text-center shadow-sm">
            <div class="text-3xl font-bold text-orange-600">{{ \App\Models\Borrowing::active()->count() }}</div>
            <div class="text-gray-500 text-sm">Dipinjam</div>
        </div>
    </div>
    
    <!-- Search & Filters -->
    <div class="bg-white rounded-3xl shadow-lg p-6 mb-8 border border-gray-100">
        <form action="{{ route('catalog.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1 relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl">🔍</span>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari judul, penulis, atau ISBN..."
                    class="w-full pl-14 pr-4 py-4 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition text-lg"
                    x-model="searchQuery"
                >
            </div>
            
            <!-- Category Filter -->
            <select name="category" class="px-5 py-4 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition min-w-[200px] text-gray-700">
                <option value="">🏷️ Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->icon ?? '📁' }} {{ $category->name }} ({{ $category->books_count }})
                    </option>
                @endforeach
            </select>
            
            <!-- Available Only -->
            <label class="flex items-center gap-3 px-5 py-4 bg-gray-50 rounded-2xl cursor-pointer hover:bg-gray-100 transition">
                <input type="checkbox" name="available" value="1" {{ request('available') ? 'checked' : '' }} class="w-5 h-5 text-primary-dark rounded-lg">
                <span class="text-gray-700 font-medium whitespace-nowrap">✅ Tersedia Saja</span>
            </label>
            
            <!-- Search Button -->
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-primary-dark to-green-700 text-white rounded-2xl font-semibold hover:shadow-lg transition text-lg">
                Cari
            </button>
        </form>
    </div>
    
    <!-- Category Pills -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('catalog.index') }}" 
           class="px-5 py-2.5 rounded-full text-sm font-semibold transition shadow-sm {{ !request('category') ? 'bg-primary-dark text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
            📚 Semua
        </a>
        @foreach($categories as $category)
            <a href="{{ route('catalog.index', ['category' => $category->id]) }}" 
               class="px-5 py-2.5 rounded-full text-sm font-semibold transition shadow-sm {{ request('category') == $category->id ? 'bg-primary-dark text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                {{ $category->icon ?? '📁' }} {{ $category->name }}
            </a>
        @endforeach
    </div>
    
    <!-- Results Count -->
    <div class="mb-6 flex items-center justify-between">
        <p class="text-gray-600">
            Menampilkan <strong>{{ $books->count() }}</strong> dari <strong>{{ $books->total() }}</strong> buku
        </p>
        @if(request('search') || request('category') || request('available'))
            <a href="{{ route('catalog.index') }}" class="text-primary-dark hover:underline text-sm font-medium flex items-center gap-1">
                ✖️ Reset Filter
            </a>
        @endif
    </div>
    
    <!-- Books Grid (Shopee Style) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
        @forelse($books as $book)
            <a href="{{ route('catalog.show', $book) }}" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <!-- Book Cover -->
                <div class="aspect-[3/4] bg-gradient-to-br from-primary-light to-gray-100 relative overflow-hidden">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="text-6xl opacity-30">📕</span>
                        </div>
                    @endif
                    
                    <!-- Availability Badge -->
                    <div class="absolute top-3 right-3">
                        @if($book->is_available)
                            <span class="px-3 py-1.5 bg-green-500 text-white text-xs font-bold rounded-lg shadow-lg flex items-center gap-1">
                                <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                Tersedia
                            </span>
                        @else
                            <span class="px-3 py-1.5 bg-red-500 text-white text-xs font-bold rounded-lg shadow-lg">
                                Dipinjam
                            </span>
                        @endif
                    </div>
                    
                    <!-- Hover Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                        <span class="text-white font-semibold text-sm">Lihat Detail →</span>
                    </div>
                </div>
                
                <!-- Book Info -->
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 line-clamp-2 group-hover:text-primary-dark transition mb-1">
                        {{ $book->title }}
                    </h3>
                    <p class="text-gray-500 text-sm line-clamp-1 mb-2">{{ $book->author }}</p>
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 bg-primary-light text-primary-dark text-xs font-medium rounded-lg">
                            {{ $book->category->icon ?? '📁' }} {{ Str::limit($book->category->name ?? '-', 10) }}
                        </span>
                        <span class="text-xs text-gray-400">
                            {{ $book->available_stock }}/{{ $book->stock }}
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-16 text-center border border-gray-100">
                <div class="text-6xl mb-4">📚</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Tidak ada buku ditemukan</h3>
                <p class="text-gray-500 mb-6">Coba ubah filter atau kata kunci pencarian Anda</p>
                <a href="{{ route('catalog.index') }}" class="inline-block px-6 py-3 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition">
                    🔄 Reset Pencarian
                </a>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($books->hasPages())
    <div class="mt-8 bg-white rounded-2xl p-4 border border-gray-100">
        {{ $books->withQueryString()->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function catalogSearch() {
    return {
        searchQuery: '{{ request("search") }}',
    }
}
</script>
@endpush
@endsection
