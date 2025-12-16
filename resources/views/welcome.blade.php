<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Perpustakaan Sekolah') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-cream min-h-screen flex items-center justify-center">
    <div class="max-w-6xl mx-auto px-4 py-12">
        <div class="text-center mb-12 animate-fade-in">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-primary-dark rounded-2xl mb-6">
                <svg class="w-14 h-14 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2zm0 2v16h12V4H6zm2 2h8v2H8V6zm0 4h8v2H8v-2zm0 4h5v2H8v-2z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-primary-dark mb-3">Perpustakaan Sekolah</h1>
            <p class="text-gray-600 text-lg">Sistem Manajemen Perpustakaan Digital</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Catalog Card -->
            <a href="{{ route('catalog.index') }}" class="group bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <div class="w-14 h-14 bg-primary-light rounded-xl flex items-center justify-center mb-4 group-hover:bg-primary-dark group-hover:text-white transition-colors">
                    <svg class="w-7 h-7 text-primary-dark group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Katalog Buku</h2>
                <p class="text-gray-600">Jelajahi koleksi buku perpustakaan dan temukan buku favoritmu.</p>
            </a>
            
            <!-- QR Attendance Card -->
            <a href="{{ route('attendance.scan') }}" class="group bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <div class="w-14 h-14 bg-accent-green/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-accent-green group-hover:text-white transition-colors">
                    <svg class="w-7 h-7 text-accent-green group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Absensi QR</h2>
                <p class="text-gray-600">Scan QR untuk mencatat kehadiran di perpustakaan.</p>
            </a>
            
            <!-- Login Card -->
            <a href="{{ route('student.login') }}" class="group bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-7 h-7 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Login Siswa</h2>
                <p class="text-gray-600">Masuk dengan NIS untuk meminjam buku dan melihat riwayat.</p>
            </a>
        </div>
        
        <div class="text-center mt-12">
            <a href="/admin" class="text-gray-500 hover:text-primary-dark transition text-sm">
                Login sebagai Admin →
            </a>
        </div>
    </div>
</body>
</html>
