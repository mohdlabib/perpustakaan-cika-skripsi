<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Perpustakaan Sekolah')</title>
    
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
<body class="antialiased bg-cream min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-primary-dark to-green-800 text-white shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center group-hover:bg-white/30 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2zm0 2v16h12V4H6zm2 2h8v2H8V6zm0 4h8v2H8v-2zm0 4h5v2H8v-2z"/>
                            </svg>
                        </div>
                        <div class="hidden sm:block">
                            <span class="font-bold text-lg">Perpustakaan</span>
                            <span class="text-white/70 text-sm block -mt-1">Sekolah Digital</span>
                        </div>
                    </a>
                </div>
                
                <!-- Navigation Links -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('catalog.index') }}" class="px-4 py-2 rounded-xl hover:bg-white/10 transition font-medium {{ request()->routeIs('catalog.*') ? 'bg-white/20' : '' }}">
                        📚 Katalog
                    </a>
                    <a href="{{ route('attendance.scan') }}" class="px-4 py-2 rounded-xl hover:bg-white/10 transition font-medium {{ request()->routeIs('attendance.*') ? 'bg-white/20' : '' }}">
                        📷 Absensi
                    </a>
                    
                    @if(session('student'))
                        <div class="flex items-center gap-2 ml-2 pl-4 border-l border-white/20">
                            <div class="hidden sm:block text-right">
                                <span class="text-sm font-medium">{{ session('student')->name }}</span>
                                <span class="text-white/70 text-xs block">{{ session('student')->class ?? 'Siswa' }}</span>
                            </div>
                            <a href="{{ route('borrowings.my-books') }}" class="px-3 py-2 bg-accent-green rounded-xl text-sm font-medium hover:bg-green-600 transition flex items-center gap-1">
                                📖 <span class="hidden sm:inline">Buku Saya</span>
                            </a>
                            <form action="{{ route('student.logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 bg-red-500/80 rounded-xl text-sm hover:bg-red-600 transition" title="Keluar">
                                    🚪
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('student.login') }}" class="ml-2 px-5 py-2 bg-white text-primary-dark rounded-xl font-semibold hover:bg-gray-100 transition shadow-lg">
                            🔑 Login
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-xl animate-fade-in flex items-center gap-3" role="alert">
                <span class="text-xl">✅</span>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl animate-fade-in flex items-center gap-3" role="alert">
                <span class="text-xl">❌</span>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif
    
    <!-- Main Content (flex-grow to push footer down) -->
    <main class="flex-grow">
        @yield('content')
    </main>
    
    <!-- Footer (always at bottom) -->
    <footer class="bg-gradient-to-r from-primary-dark to-green-800 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            📚
                        </div>
                        <div>
                            <span class="font-bold text-lg">Perpustakaan Sekolah</span>
                            <span class="text-white/70 text-sm block">Sistem Manajemen Digital</span>
                        </div>
                    </div>
                    <p class="text-white/70 text-sm">Sistem perpustakaan digital untuk mendukung kegiatan belajar mengajar.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Menu Cepat</h4>
                    <ul class="space-y-2 text-white/70 text-sm">
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-white transition">📖 Katalog Buku</a></li>
                        <li><a href="{{ route('attendance.scan') }}" class="hover:text-white transition">📷 Scan Absensi</a></li>
                        <li><a href="{{ route('student.login') }}" class="hover:text-white transition">🔑 Login Siswa</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Jam Operasional</h4>
                    <ul class="space-y-2 text-white/70 text-sm">
                        <li>📅 Senin - Jumat: 07:00 - 16:00</li>
                        <li>📅 Sabtu: 07:00 - 12:00</li>
                        <li>📅 Minggu: Tutup</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 mt-8 pt-6 text-center text-white/50 text-sm">
                <p>© {{ date('Y') }} Perpustakaan Sekolah. Made with ❤️ for Education.</p>
            </div>
        </div>
    </footer>
    
    @stack('scripts')
</body>
</html>
