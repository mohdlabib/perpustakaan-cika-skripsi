<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Perpustakaan SMAN 8 Pekanbaru')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="antialiased bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-72 bg-gradient-to-b from-primary-dark to-green-900 fixed left-0 top-0 h-screen text-white overflow-y-auto">
            <div class="p-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    @if(file_exists(public_path('logo-sekolah.png')))
                        <img src="{{ asset('logo-sekolah.png') }}" alt="Logo" class="w-12 h-12 rounded-xl object-contain bg-white p-1">
                    @else
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <span class="font-bold text-lg">Perpustakaan</span>
                        <span class="text-white/70 text-xs block">SMAN 8 Pekanbaru</span>
                    </div>
                </a>
            </div>
            
            <!-- Quick Stats -->
            <div class="px-4 mb-4">
                <div class="bg-white/10 rounded-2xl p-4">
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div>
                            <div class="text-2xl font-bold">{{ \App\Models\Book::count() }}</div>
                            <div class="text-white/60 text-xs">Buku</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">{{ \App\Models\Borrowing::active()->count() }}</div>
                            <div class="text-white/60 text-xs">Dipinjam</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <nav class="px-4 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="{{ request()->routeIs('admin.dashboard*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                
                <!-- Kelola Buku -->
                <a href="{{ route('admin.books.index') }}" 
                   class="{{ request()->routeIs('admin.books*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Kelola Buku
                </a>
                
                <!-- Peminjaman -->
                <a href="{{ route('admin.borrowings.index') }}" 
                   class="{{ request()->routeIs('admin.borrowings*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Peminjaman
                    @if(\App\Models\Borrowing::overdue()->count() > 0)
                        <span class="ml-auto px-2 py-0.5 bg-red-500 text-white text-xs rounded-full">
                            {{ \App\Models\Borrowing::overdue()->count() }}
                        </span>
                    @endif
                </a>
                
                <!-- Master Data Section -->
                <div class="pt-6 mt-4 border-t border-white/10">
                    <p class="px-4 text-white/40 text-xs uppercase tracking-wider mb-3">Master Data</p>
                    
                    <a href="{{ route('admin.students.index') }}" 
                       class="{{ request()->routeIs('admin.students*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Siswa
                    </a>
                    
                    <a href="{{ route('admin.grades.index') }}" 
                       class="{{ request()->routeIs('admin.grades*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Angkatan
                    </a>
                    
                    <a href="{{ route('admin.shelves.index') }}" 
                       class="{{ request()->routeIs('admin.shelves*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        Rak
                    </a>
                    
                    <a href="{{ route('admin.categories.index') }}" 
                       class="{{ request()->routeIs('admin.categories*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Kategori
                    </a>
                </div>
                
                <!-- Other -->
                <div class="pt-6 mt-4 border-t border-white/10">
                    <p class="px-4 text-white/40 text-xs uppercase tracking-wider mb-3">Lainnya</p>
                    
                    <a href="{{ route('catalog.index') }}" class="flex items-center gap-3 px-4 py-3 text-white/70 hover:bg-white/10 rounded-xl transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Lihat Katalog
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST" class="mt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-red-300 hover:bg-red-500/20 rounded-xl transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
            
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <div class="bg-white/5 rounded-xl p-3 text-center">
                    <p class="text-white/40 text-xs">© {{ date('Y') }} SMAN 8 Pekanbaru</p>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="ml-72 flex-1 min-h-screen flex flex-col">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm px-8 py-4 flex justify-between items-center sticky top-0 z-40 border-b border-gray-100">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-gray-500 text-sm">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="font-medium text-gray-800">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-gray-500 text-xs">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-dark to-green-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                </div>
            </div>
            
            <!-- Page Content -->
            <div class="p-8 flex-grow">
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
            
            <footer class="bg-white border-t border-gray-100 px-8 py-4 mt-auto">
                <div class="flex justify-between items-center text-gray-500 text-sm">
                    <p>© {{ date('Y') }} Perpustakaan SMAN 8 Pekanbaru</p>
                    <p>Sistem Informasi Perpustakaan v1.0</p>
                </div>
            </footer>
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>
