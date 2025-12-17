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
<body class="antialiased bg-gray-50 min-h-screen" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" x-cloak
             @click="sidebarOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>
        
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="w-72 bg-gradient-to-b from-primary-dark to-green-900 fixed left-0 top-0 h-screen text-white overflow-y-auto z-50 transition-transform duration-300 ease-in-out">
            <!-- Close button for mobile -->
            <button @click="sidebarOpen = false" class="lg:hidden absolute top-4 right-4 p-2 text-white/70 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            
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
            <div class="px-4 mb-6">
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
            
            <nav class="px-4">
                <!-- Menu Utama -->
                <p class="px-4 text-white/40 text-xs uppercase tracking-wider mb-3">Menu Utama</p>
                
                <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false"
                   class="{{ request()->routeIs('admin.dashboard*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer mb-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                
                <a href="{{ route('admin.books.index') }}" @click="sidebarOpen = false"
                   class="{{ request()->routeIs('admin.books*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer mb-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Kelola Buku
                </a>
                
                <a href="{{ route('admin.borrowings.index') }}" @click="sidebarOpen = false"
                   class="{{ request()->routeIs('admin.borrowings*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer mb-1">
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
                
                <!-- Scan Barcode -->
                <a href="{{ route('admin.scan') }}" @click="sidebarOpen = false"
                   class="{{ request()->routeIs('admin.scan*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer mb-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Scan Barcode
                </a>
                
                <!-- Master Data Section -->
                <div class="mt-6 pt-6 border-t border-white/10">
                    <p class="px-4 text-white/40 text-xs uppercase tracking-wider mb-3">Master Data</p>
                    
                    <a href="{{ route('admin.students.index') }}" @click="sidebarOpen = false"
                       class="{{ request()->routeIs('admin.students*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer mb-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Siswa
                    </a>
                    
                    <a href="{{ route('admin.grades.index') }}" @click="sidebarOpen = false"
                       class="{{ request()->routeIs('admin.grades*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer mb-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Angkatan
                    </a>
                    
                    <a href="{{ route('admin.shelves.index') }}" @click="sidebarOpen = false"
                       class="{{ request()->routeIs('admin.shelves*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer mb-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        Rak
                    </a>
                    
                    <a href="{{ route('admin.categories.index') }}" @click="sidebarOpen = false"
                       class="{{ request()->routeIs('admin.categories*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer mb-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Kategori
                    </a>
                </div>
                
                <!-- Laporan -->
                <div class="mt-6 pt-6 border-t border-white/10">
                    <p class="px-4 text-white/40 text-xs uppercase tracking-wider mb-3">Laporan</p>
                    
                    <a href="{{ route('admin.reports.books') }}" @click="sidebarOpen = false"
                       class="{{ request()->routeIs('admin.reports.books') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer mb-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Laporan Buku
                    </a>
                    
                    <a href="{{ route('admin.reports.students') }}" @click="sidebarOpen = false"
                       class="{{ request()->routeIs('admin.reports.students') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium cursor-pointer mb-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Laporan Siswa
                    </a>
                </div>
                
                <!-- Other -->
                <div class="mt-6 pt-6 border-t border-white/10">
                    <p class="px-4 text-white/40 text-xs uppercase tracking-wider mb-3">Lainnya</p>
                    
                    <a href="{{ route('admin.attendance') }}" class="flex items-center gap-3 px-4 py-3 text-white/70 hover:bg-white/10 rounded-xl transition cursor-pointer mb-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        Generate QR Absensi
                    </a>
                    
                    <a href="{{ route('catalog.index') }}" class="flex items-center gap-3 px-4 py-3 text-white/70 hover:bg-white/10 rounded-xl transition cursor-pointer mb-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Lihat Katalog
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST">
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
            
            <div class="p-4 mt-8">
                <div class="bg-white/5 rounded-xl p-3 text-center">
                    <p class="text-white/40 text-xs">© {{ date('Y') }} SMAN 8 Pekanbaru</p>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="lg:ml-72 flex-1 min-h-screen flex flex-col w-full">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm px-4 lg:px-8 py-4 flex justify-between items-center sticky top-0 z-30 border-b border-gray-100">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                
                <div class="hidden sm:block">
                    <h1 class="text-lg lg:text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-gray-500 text-xs lg:text-sm">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
                
                <div class="flex items-center gap-2 lg:gap-4" x-data="{ open: false }">
                    <div class="text-right hidden sm:block">
                        <p class="font-medium text-gray-800 text-sm lg:text-base">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-gray-500 text-xs">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <div class="relative">
                        <button @click="open = !open" class="w-10 h-10 bg-gradient-to-br from-primary-dark to-green-600 rounded-full flex items-center justify-center text-white font-bold cursor-pointer hover:opacity-90 transition">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                            <div class="px-4 py-2 border-b sm:hidden">
                                <p class="font-medium text-gray-800">{{ auth()->user()->name ?? 'Admin' }}</p>
                                <p class="text-gray-500 text-xs">{{ auth()->user()->email ?? '' }}</p>
                            </div>
                            <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:bg-gray-50 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Pengaturan
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Page Title -->
            <div class="sm:hidden px-4 py-3 bg-white border-b">
                <h1 class="text-lg font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            </div>
            
            <!-- Page Content -->
            <div class="p-4 lg:p-8 flex-grow">
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm">{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm">{{ session('error') }}</span>
                    </div>
                @endif
                
                @yield('content')
            </div>
            
            <footer class="bg-white border-t border-gray-100 px-4 lg:px-8 py-4 mt-auto">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-2 text-gray-500 text-xs lg:text-sm">
                    <p>© {{ date('Y') }} Perpustakaan SMAN 8 Pekanbaru</p>
                    <p>Sistem Informasi Perpustakaan v1.0</p>
                </div>
            </footer>
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>
