<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perpustakaan Jendela Ilmu</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gradient-to-br from-primary-light via-cream to-white min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Hero Section -->
        <div class="flex-grow flex items-center justify-center py-12 px-4">
            <div class="max-w-4xl w-full text-center">
                <!-- Logo -->
                <div class="mb-8">
                    @if(file_exists(public_path('logo-baru.png')))
                        <img src="{{ asset('logo-baru.png') }}" alt="Logo Perpustakaan Jendela Ilmu" class="w-32 h-32 mx-auto object-contain">
                    @else
                        <div class="w-32 h-32 mx-auto bg-primary-dark rounded-3xl flex items-center justify-center">
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <!-- Title -->
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    Perpustakaan Jendela Ilmu
                </h1>
                <h2 class="text-2xl md:text-3xl font-semibold text-primary-dark mb-6">
                    SMAN 8 Pekanbaru
                </h2>
                <p class="text-gray-600 text-lg mb-12 max-w-2xl mx-auto">
                    Sistem Informasi Perpustakaan Digital Jendela Ilmu untuk mendukung kegiatan belajar mengajar
                </p>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12 max-w-3xl mx-auto">
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        <div class="text-3xl font-bold text-primary-dark">{{ \App\Models\Book::count() }}</div>
                        <div class="text-gray-500 text-sm">Koleksi Buku</div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        <div class="text-3xl font-bold text-green-600">{{ \App\Models\Category::count() }}</div>
                        <div class="text-gray-500 text-sm">Kategori</div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        <div class="text-3xl font-bold text-blue-600">{{ \App\Models\Student::count() }}</div>
                        <div class="text-gray-500 text-sm">Anggota</div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        <div class="text-3xl font-bold text-purple-600">{{ \App\Models\Borrowing::count() }}</div>
                        <div class="text-gray-500 text-sm">Peminjaman</div>
                    </div>
                </div>
                
                <!-- Action Cards -->
                <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                    <!-- Katalog Buku -->
                    <a href="{{ route('catalog.index') }}" class="group bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 cursor-pointer">
                        <div class="w-16 h-16 bg-primary-light rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-primary-dark transition">
                            <svg class="w-8 h-8 text-primary-dark group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Katalog Buku</h3>
                        <p class="text-gray-500 text-sm">Jelajahi koleksi buku perpustakaan</p>
                    </a>
                    
                    <!-- Scan Pengunjung -->
                    <a href="{{ route('attendance.scan') }}" class="group bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 cursor-pointer">
                        <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition">
                            <svg class="w-8 h-8 text-green-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Scan Pengunjung</h3>
                        <p class="text-gray-500 text-sm">Catat kehadiran dengan QR code</p>
                    </a>
                    
                    <!-- Login Siswa -->
                    <a href="{{ route('student.login') }}" class="group bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 cursor-pointer">
                        <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition">
                            <svg class="w-8 h-8 text-green-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Login Siswa</h3>
                        <p class="text-gray-500 text-sm">Masuk dengan NIS untuk meminjam buku</p>
                    </a>
                </div>
                
                <!-- Admin Link -->
                <div class="mt-12">
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-primary-dark transition text-sm cursor-pointer">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Login Admin
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 py-6">
            <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
                <p>© {{ date('Y') }} Perpustakaan Jendela Ilmu. Sistem Informasi Perpustakaan Digital.</p>
            </div>
        </footer>
    </div>
</body>
</html>
