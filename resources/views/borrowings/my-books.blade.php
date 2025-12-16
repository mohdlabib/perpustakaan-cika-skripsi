@extends('layouts.student')

@section('title', 'Buku Saya - Perpustakaan')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Buku Saya</h1>
        <p class="text-gray-600 mt-2">Daftar buku yang sedang Anda pinjam</p>
    </div>
    
    @php
        $active = $borrowings->where('status', 'borrowed');
        $returned = $borrowings->where('status', 'returned');
    @endphp
    
    <!-- Active Borrowings -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
            Sedang Dipinjam ({{ $active->count() }}/3)
        </h2>
        
        @if($active->count() > 0)
            <div class="space-y-4">
                @foreach($active as $borrowing)
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 {{ $borrowing->is_overdue ? 'border-red-300 bg-red-50' : '' }}">
                        <div class="flex gap-4">
                            <div class="w-16 h-20 bg-primary-light rounded-lg flex-shrink-0 flex items-center justify-center">
                                @if($borrowing->book->cover_image)
                                    <img src="{{ Storage::url($borrowing->book->cover_image) }}" class="w-full h-full object-cover rounded-lg">
                                @else
                                    <svg class="w-8 h-8 text-primary-dark/30" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">{{ $borrowing->book->title }}</h3>
                                <p class="text-gray-500 text-sm">{{ $borrowing->book->author }}</p>
                                <div class="flex flex-wrap gap-4 mt-2 text-sm">
                                    <span class="text-gray-500">
                                        Dipinjam: {{ $borrowing->borrow_date->format('d M Y') }}
                                    </span>
                                    <span class="{{ $borrowing->is_overdue ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                        Batas: {{ $borrowing->due_date->format('d M Y') }}
                                        @if($borrowing->is_overdue)
                                            (Terlambat {{ abs($borrowing->days_remaining) }} hari)
                                        @elseif($borrowing->days_remaining <= 2)
                                            ({{ $borrowing->days_remaining }} hari lagi)
                                        @endif
                                    </span>
                                </div>
                            </div>
                            @if($borrowing->is_overdue)
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-lg h-fit">
                                    Terlambat
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl p-8 text-center border border-gray-100">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p class="text-gray-500">Tidak ada buku yang sedang dipinjam</p>
                <a href="{{ route('catalog.index') }}" class="inline-block mt-4 px-6 py-2 bg-primary-dark text-white rounded-xl text-sm font-medium hover:bg-opacity-90 transition">
                    Jelajahi Katalog
                </a>
            </div>
        @endif
    </div>
    
    <!-- History -->
    @if($returned->count() > 0)
    <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
            Riwayat Pengembalian
        </h2>
        
        <div class="space-y-3">
            @foreach($returned->take(5) as $borrowing)
                <div class="bg-white rounded-xl p-4 border border-gray-100 flex items-center gap-4">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-700">{{ $borrowing->book->title }}</h4>
                        <p class="text-gray-400 text-sm">
                            {{ $borrowing->borrow_date->format('d M') }} - {{ $borrowing->return_date->format('d M Y') }}
                        </p>
                    </div>
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-lg">Dikembalikan</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
