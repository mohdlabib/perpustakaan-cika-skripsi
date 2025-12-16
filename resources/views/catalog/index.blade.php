@extends('layouts.student')

@section('title', 'Katalog Buku - Perpustakaan')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="catalogSearch()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Katalog Buku</h1>
        <p class="text-gray-600 mt-2">Temukan buku favoritmu dari koleksi perpustakaan</p>
    </div>
    
    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-8 border border-gray-100">
        <form action="{{ route('catalog.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari judul, penulis, atau ISBN..."
                    class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                    x-model="searchQuery"
                    @input.debounce.300ms="liveSearch"
                >
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            
            <!-- Category Filter -->
            <select name="category" class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition min-w-[180px]">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} ({{ $category->books_count }})
                    </option>
                @endforeach
            </select>
            
            <!-- Available Only -->
            <label class="flex items-center gap-2 px-4 py-3 bg-gray-50 rounded-xl cursor-pointer">
                <input type="checkbox" name="available" value="1" {{ request('available') ? 'checked' : '' }} class="w-4 h-4 text-primary-dark rounded">
                <span class="text-gray-700 whitespace-nowrap">Tersedia</span>
            </label>
            
            <!-- Search Button -->
            <button type="submit" class="px-6 py-3 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition">
                Cari
            </button>
        </form>
    </div>
    
    <!-- Category Pills -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('catalog.index') }}" 
           class="px-4 py-2 rounded-full text-sm font-medium transition {{ !request('category') ? 'bg-primary-dark text-white' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
            Semua
        </a>
        @foreach($categories as $category)
            <a href="{{ route('catalog.index', ['category' => $category->id]) }}" 
               class="px-4 py-2 rounded-full text-sm font-medium transition {{ request('category') == $category->id ? 'bg-primary-dark text-white' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
                {{ $category->icon ?? '' }} {{ $category->name }}
            </a>
        @endforeach
    </div>
    
    <!-- Results Count -->
    <div class="mb-4 text-gray-600">
        Menampilkan {{ $books->total() }} buku
    </div>
    
    <!-- Books Grid (Shopee Style) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @forelse($books as $book)
            <a href="{{ route('catalog.show', $book) }}" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <!-- Book Cover -->
                <div class="aspect-[3/4] bg-gradient-to-br from-primary-light to-gray-100 relative overflow-hidden">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-primary-dark/30" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2zm0 2v16h12V4H6zm2 2h8v2H8V6z"/>
                            </svg>
                        </div>
                    @endif
                    
                    <!-- Availability Badge -->
                    <div class="absolute top-2 right-2">
                        @if($book->is_available)
                            <span class="px-2 py-1 bg-green-500 text-white text-xs font-medium rounded-lg">
                                Tersedia
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-500 text-white text-xs font-medium rounded-lg">
                                Dipinjam
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Book Info -->
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 line-clamp-2 group-hover:text-primary-dark transition">
                        {{ $book->title }}
                    </h3>
                    <p class="text-gray-500 text-sm mt-1 line-clamp-1">{{ $book->author }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2 py-0.5 bg-primary-light text-primary-dark text-xs rounded-full">
                            {{ $book->category->name ?? '-' }}
                        </span>
                    </div>
                    <div class="mt-2 text-xs text-gray-400">
                        Stok: {{ $book->available_stock }}/{{ $book->stock }}
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p class="text-gray-500">Tidak ada buku yang ditemukan.</p>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div class="mt-8">
        {{ $books->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
function catalogSearch() {
    return {
        searchQuery: '{{ request("search") }}',
        results: [],
        loading: false,
        
        async liveSearch() {
            if (this.searchQuery.length < 2) {
                this.results = [];
                return;
            }
            
            this.loading = true;
            try {
                const response = await axios.get('{{ route("catalog.search") }}', {
                    params: { q: this.searchQuery }
                });
                this.results = response.data.results;
            } catch (error) {
                console.error('Search error:', error);
            }
            this.loading = false;
        }
    }
}
</script>
@endpush
@endsection
