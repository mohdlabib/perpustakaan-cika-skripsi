<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Perpustakaan Sekolah')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="antialiased bg-cream min-h-screen">
    <!-- Navigation -->
    <nav class="bg-primary-dark text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2zm0 2v16h12V4H6zm2 2h8v2H8V6zm0 4h8v2H8v-2zm0 4h5v2H8v-2z"/>
                        </svg>
                        <span class="font-bold text-lg hidden sm:block">Perpustakaan Sekolah</span>
                    </a>
                </div>
                
                <!-- Navigation Links -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('catalog.index') }}" class="px-3 py-2 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('catalog.*') ? 'bg-white/20' : '' }}">
                        Katalog
                    </a>
                    <a href="{{ route('attendance.scan') }}" class="px-3 py-2 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('attendance.*') ? 'bg-white/20' : '' }}">
                        Absensi
                    </a>
                    
                    @if(session('student'))
                        <div class="flex items-center gap-3 ml-4 pl-4 border-l border-white/20">
                            <span class="text-sm">{{ session('student')->name }}</span>
                            <a href="{{ route('borrowings.my-books') }}" class="px-3 py-1 bg-accent-green rounded-lg text-sm hover:bg-opacity-90 transition">
                                Buku Saya
                            </a>
                            <form action="{{ route('student.logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-red-500 rounded-lg text-sm hover:bg-red-600 transition">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('student.login') }}" class="px-4 py-2 bg-accent-green rounded-lg font-medium hover:bg-opacity-90 transition">
                            Login
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg animate-fade-in" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg animate-fade-in" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-primary-dark text-white mt-12 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm opacity-75">© {{ date('Y') }} Perpustakaan Sekolah. All rights reserved.</p>
        </div>
    </footer>
    
    @stack('scripts')
</body>
</html>
