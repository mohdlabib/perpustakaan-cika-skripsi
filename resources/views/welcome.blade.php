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
                        <div class="text-3xl font-bold text-blue-600">{{ \App\Models\Borrowing::count() }}</div>
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
                    
                    <!-- Kunjungan -->
                    <a href="{{ route('attendance.scan') }}" class="group bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 cursor-pointer">
                        <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition">
                            <svg class="w-8 h-8 text-green-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Kunjungan</h3>
                        <p class="text-gray-500 text-sm">Catat kehadiran tamu dan Login Siswa</p>
                    </a>
                    
                    <!-- Login Siswa -->
                    <a href="{{ route('student.login') }}" class="group bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 cursor-pointer">
                        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-600 transition">
                            <svg class="w-8 h-8 text-blue-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6.055M12 14l-9-5m9 5l9-5M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm0-9l9 5-9 5-9-5 9-5z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Login Siswa</h3>
                        <p class="text-gray-500 text-sm">Masuk dengan NIS untuk catat kehadiran dan meminjam buku</p>
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
