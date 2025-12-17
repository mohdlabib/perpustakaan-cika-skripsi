@extends('layouts.student')

@section('title', 'Katalog Buku - Perpustakaan')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ }">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Katalog Buku</h1>
        <p class="text-gray-600 text-lg">Temukan buku favoritmu dari koleksi perpustakaan</p>
    </div>
    
    <!-- Stats Bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $books->total() }}</div>
                    <div class="text-gray-500 text-sm">Total Buku</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Book::where('stock', '>', 0)->count() }}</div>
                    <div class="text-gray-500 text-sm">Tersedia</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Category::count() }}</div>
                    <div class="text-gray-500 text-sm">Kategori</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Borrowing::active()->count() }}</div>
                    <div class="text-gray-500 text-sm">Dipinjam</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-8 border border-gray-100">
        <form action="{{ route('catalog.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari judul, penulis, atau ISBN..."
                    class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                >
            </div>
            
            <select name="category" class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition min-w-[180px]">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} ({{ $category->books_count }})
                    </option>
                @endforeach
            </select>
            
            <label class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                <input type="checkbox" name="available" value="1" {{ request('available') ? 'checked' : '' }} class="w-4 h-4 text-primary-dark rounded">
                <span class="text-gray-700 font-medium whitespace-nowrap">Tersedia Saja</span>
            </label>
            
            <button type="submit" class="px-6 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Cari
            </button>
        </form>
    </div>
    
    <!-- Category Pills -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('catalog.index') }}" 
           class="px-4 py-2 rounded-full text-sm font-medium transition {{ !request('category') ? 'bg-primary-dark text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
            Semua
        </a>
        @foreach($categories as $category)
            <a href="{{ route('catalog.index', ['category' => $category->id]) }}" 
               class="px-4 py-2 rounded-full text-sm font-medium transition {{ request('category') == $category->id ? 'bg-primary-dark text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                {{ $category->name }}
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
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reset Filter
            </a>
        @endif
    </div>
    
    <!-- Books Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
        @forelse($books as $book)
            <a href="{{ route('catalog.show', $book) }}" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <div class="aspect-[3/4] bg-gradient-to-br from-primary-light to-gray-100 relative overflow-hidden">
                    @if($book->cover_image)
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-primary-dark/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="absolute top-3 right-3">
                        @if($book->is_available)
                            <span class="px-2.5 py-1 bg-green-500 text-white text-xs font-bold rounded-lg shadow-lg flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                Tersedia
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-red-500 text-white text-xs font-bold rounded-lg shadow-lg">
                                Dipinjam
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 line-clamp-2 group-hover:text-primary-dark transition mb-1">
                        {{ $book->title }}
                    </h3>
                    <p class="text-gray-500 text-sm line-clamp-1 mb-2">{{ $book->author }}</p>
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 bg-primary-light text-primary-dark text-xs font-medium rounded">
                            {{ Str::limit($book->category->name ?? '-', 12) }}
                        </span>
                        <span class="text-xs text-gray-400">
                            {{ $book->available_stock }}/{{ $book->stock }}
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-16 text-center border border-gray-100">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Tidak ada buku ditemukan</h3>
                <p class="text-gray-500 mb-4">Coba ubah filter atau kata kunci pencarian</p>
                <a href="{{ route('catalog.index') }}" class="inline-block px-6 py-2 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition">
                    Reset Pencarian
                </a>
            </div>
        @endforelse
    </div>
    
    @if($books->hasPages())
    <div class="mt-8">
        {{ $books->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
