<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Perpustakaan')</title>
    
    <!-- Google Fonts -->
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
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl">
                        📚
                    </div>
                    <div>
                        <span class="font-bold text-lg">Admin Panel</span>
                        <span class="text-white/60 text-xs block">Perpustakaan Sekolah</span>
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
            
            <nav class="px-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="{{ request()->routeIs('admin.dashboard*') ? 'bg-white/20 text-white shadow-lg' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3.5 rounded-xl transition font-medium">
                    <span class="text-xl">🏠</span>
                    Dashboard
                </a>
                
                <a href="{{ route('admin.books.index') }}" 
                   class="{{ request()->routeIs('admin.books*') ? 'bg-white/20 text-white shadow-lg' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3.5 rounded-xl transition font-medium">
                    <span class="text-xl">📚</span>
                    Kelola Buku
                </a>
                
                <a href="{{ route('admin.borrowings.index') }}" 
                   class="{{ request()->routeIs('admin.borrowings*') ? 'bg-white/20 text-white shadow-lg' : 'text-white/70 hover:bg-white/10' }} flex items-center gap-3 px-4 py-3.5 rounded-xl transition font-medium">
                    <span class="text-xl">📋</span>
                    Peminjaman
                    @if(\App\Models\Borrowing::overdue()->count() > 0)
                        <span class="ml-auto px-2 py-0.5 bg-red-500 text-white text-xs rounded-full animate-pulse">
                            {{ \App\Models\Borrowing::overdue()->count() }}
                        </span>
                    @endif
                </a>
                
                <div class="pt-6 mt-6 border-t border-white/10">
                    <p class="px-4 text-white/40 text-xs uppercase tracking-wider mb-3">Lainnya</p>
                    
                    <a href="{{ route('catalog.index') }}" class="flex items-center gap-3 px-4 py-3 text-white/70 hover:bg-white/10 rounded-xl transition">
                        <span class="text-xl">🌐</span>
                        Lihat Katalog
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-red-300 hover:bg-red-500/20 rounded-xl transition">
                            <span class="text-xl">🚪</span>
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
            
            <!-- Footer -->
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <div class="bg-white/5 rounded-xl p-3 text-center">
                    <p class="text-white/40 text-xs">© {{ date('Y') }} Perpustakaan</p>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="ml-72 flex-1 min-h-screen flex flex-col">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm px-8 py-4 flex justify-between items-center sticky top-0 z-40 border-b border-gray-100">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-gray-500 text-sm">{{ now()->format('l, d F Y') }}</p>
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
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-xl mb-6 animate-fade-in flex items-center gap-3">
                        <span class="text-xl">✅</span>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl mb-6 animate-fade-in flex items-center gap-3">
                        <span class="text-xl">❌</span>
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
            
            <!-- Footer -->
            <footer class="bg-white border-t border-gray-100 px-8 py-4 mt-auto">
                <div class="flex justify-between items-center text-gray-500 text-sm">
                    <p>© {{ date('Y') }} Perpustakaan Sekolah - Admin Panel</p>
                    <p>Made with ❤️ for Education</p>
                </div>
            </footer>
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>
