@extends('layouts.admin')

@section('page-title', 'Laporan Pengunjung')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['today']) }}</div>
                <div class="text-gray-500 text-sm">Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['this_week']) }}</div>
                <div class="text-gray-500 text-sm">Minggu Ini</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['this_month']) }}</div>
                <div class="text-gray-500 text-sm">Bulan Ini</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['total']) }}</div>
                <div class="text-gray-500 text-sm">Total Kunjungan</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm mb-8">
    <form action="{{ route('admin.reports.visitors') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
        <div class="flex-1 w-full">
            <label class="block text-gray-600 text-sm font-medium mb-2">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
        </div>
        <div class="flex-1 w-full">
            <label class="block text-gray-600 text-sm font-medium mb-2">Tanggal Akhir</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
        </div>
        <div class="flex-1 w-full md:col-span-2 lg:col-span-1">
            <label class="block text-gray-600 text-sm font-medium mb-2">Kata Kunci</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..." 
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
        </div>
        <div class="flex gap-3 w-full md:w-auto mt-4 md:mt-0">
            <button type="submit" class="flex-1 md:flex-none px-6 py-2.5 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition flex justify-center items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
            <a href="{{ route('admin.reports.visitors') }}" class="flex-1 md:flex-none px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-medium hover:bg-gray-200 transition text-center cursor-pointer">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Action Bar: Import / Export / Template -->
<div class="flex flex-wrap gap-3 mb-6">
    {{-- Export --}}
    <a href="{{ route('admin.reports.visitors.export', request()->only('start_date','end_date','search')) }}"
       class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl text-sm font-medium hover:bg-green-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Export Excel
    </a>

    {{-- Download Template --}}
    <a href="{{ route('admin.reports.visitors.template') }}"
       class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Unduh Template
    </a>

    {{-- Import --}}
    <button onclick="document.getElementById('importVisitorModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-xl text-sm font-medium hover:bg-orange-600 transition cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/>
        </svg>
        Import Excel
    </button>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">
    {{ session('error') }}
</div>
@endif

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Daily Chart -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-semibold text-gray-800">Tren Kunjungan Harian</h3>
                <p class="text-gray-500 text-sm">30 hari terakhir</p>
            </div>
            <button onclick="downloadDailyChart()" class="p-2 text-gray-500 hover:text-primary-dark hover:bg-gray-100 rounded-lg transition cursor-pointer" title="Download">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </button>
        </div>
        <div class="h-64">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>
    
    <!-- Monthly Chart -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-semibold text-gray-800">Kunjungan per Bulan</h3>
                <p class="text-gray-500 text-sm">12 bulan terakhir</p>
            </div>
            <button onclick="downloadMonthlyChart()" class="p-2 text-gray-500 hover:text-primary-dark hover:bg-gray-100 rounded-lg transition cursor-pointer" title="Download">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </button>
        </div>
        <div class="h-64">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>
</div>

<!-- Info Cards Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Top Visitors -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Top 5 Pengunjung</h3>
                <p class="text-gray-500 text-xs">Bulan ini</p>
            </div>
        </div>
        <div class="space-y-3">
            @forelse($topVisitors as $index => $visitor)
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                    {{ $index === 0 ? 'bg-green-500 text-white' : ($index === 1 ? 'bg-gray-300 text-gray-700' : ($index === 2 ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-600')) }}">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-800 truncate">{{ $visitor->name }}</p>
                    <p class="text-gray-500 text-xs">{{ $visitor->grade->name ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 bg-primary-light text-primary-dark text-xs font-bold rounded-lg">
                        {{ $visitor->visits_count }}x
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-6 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-sm">Belum ada data</p>
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Visits by Grade -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Kunjungan per Angkatan</h3>
                <p class="text-gray-500 text-xs">Periode filter</p>
            </div>
        </div>
        <div class="space-y-3">
            @forelse($visitsByGrade->filter(fn($g) => $g->visits_count > 0) as $grade)
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $grade->name }}</span>
                        <span class="text-sm text-gray-500">{{ $grade->visits_count }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        @php
                            $maxVisits = $visitsByGrade->max('visits_count') ?: 1;
                            $percentage = ($grade->visits_count / $maxVisits) * 100;
                        @endphp
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500" 
                             style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-6 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="text-sm">Belum ada data</p>
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Peak Hours -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Jam Kunjungan Terpopuler</h3>
                <p class="text-gray-500 text-xs">Bulan ini</p>
            </div>
        </div>
        <div class="space-y-3">
            @forelse($peakHours as $index => $hour)
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ $hour['hour'] }}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-lg">
                        {{ $hour['count'] }} kunjungan
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-6 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm">Belum ada data</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Data Table Section -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">Riwayat Kunjungan</h2>
        <p class="text-gray-500 text-sm">Daftar kunjungan siswa dan tamu ke perpustakaan</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.reports.visitors.template') }}" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Template
        </a>
        <button onclick="document.getElementById('importVisitorModal').classList.remove('hidden')" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Import Excel
        </button>
        <a href="{{ route('admin.reports.visitors.export', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'search' => request('search')]) }}" 
           class="px-4 py-2.5 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Export Excel
        </a>
    </div>
</div>

<!-- Visits Table -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-600 text-sm">
                    <th class="px-6 py-4 font-semibold">No</th>
                    <th class="px-6 py-4 font-semibold">Waktu Kunjungan</th>
                    <th class="px-6 py-4 font-semibold">Tipe</th>
                    <th class="px-6 py-4 font-semibold">NIS</th>
                    <th class="px-6 py-4 font-semibold">Nama</th>
                    <th class="px-6 py-4 font-semibold">Kelas / Instansi</th>
                    <th class="px-6 py-4 font-semibold">Angkatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($visits as $index => $visit)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-500">{{ ($visits->currentPage() - 1) * $visits->perPage() + $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-primary-light rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $visit->visited_at->format('d M Y') }}</p>
                                <p class="text-gray-500 text-xs">{{ $visit->visited_at->format('H:i') }} WIB</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($visit->visitor_type === 'guest')
                            <span class="px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-lg">Tamu</span>
                        @else
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg">Siswa</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600 font-mono">{{ $visit->student_nis ?? '-' }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $visit->visitor_name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $visit->visitor_detail }}</td>
                    <td class="px-6 py-4">
                        @if($visit->visitor_type === 'student' && $visit->student && $visit->student->grade)
                            <span class="px-3 py-1 bg-primary-light text-primary-dark text-xs font-medium rounded-lg">
                                {{ $visit->student->grade->name }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-lg font-medium">Tidak ada data kunjungan</p>
                        <p class="text-sm">Belum ada kunjungan pada periode yang dipilih</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($visits->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-sm text-gray-500">Menampilkan {{ $visits->firstItem() }}-{{ $visits->lastItem() }} dari {{ $visits->total() }} data</p>
        {{ $visits->withQueryString()->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Daily Chart
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
const dailyData = @json($dailyData);

const dailyChart = new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: dailyData.map(d => d.date),
        datasets: [{
            label: 'Kunjungan',
            data: dailyData.map(d => d.count),
            borderColor: 'rgb(22, 163, 74)',
            backgroundColor: 'rgba(22, 163, 74, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 0,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: 'rgb(22, 163, 74)',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                grid: { display: false },
                ticks: {
                    maxTicksLimit: 10
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

// Monthly Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyData = @json($monthlyData);

const monthlyChart = new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: monthlyData.map(d => d.month),
        datasets: [{
            label: 'Kunjungan',
            data: monthlyData.map(d => d.count),
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderRadius: 8,
            barThickness: 24,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

function downloadDailyChart() {
    const link = document.createElement('a');
    link.download = 'tren-kunjungan-harian-' + new Date().toISOString().split('T')[0] + '.png';
    link.href = document.getElementById('dailyChart').toDataURL('image/png');
    link.click();
}

function downloadMonthlyChart() {
    const link = document.createElement('a');
    link.download = 'kunjungan-bulanan-' + new Date().toISOString().split('T')[0] + '.png';
    link.href = document.getElementById('monthlyChart').toDataURL('image/png');
    link.click();
}
</script>
@endpush

<!-- Import Modal -->
<div id="importVisitorModal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('importVisitorModal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-blue-600 p-5 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold">Import Data Kunjungan</h3>
                        <p class="text-white/70 text-sm">Upload file Excel (.xlsx, .xls, .csv)</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('admin.reports.visitors.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Excel</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100 cursor-pointer">
                    <div class="mt-3 p-3 bg-blue-50 rounded-xl text-xs text-blue-800 space-y-1">
                        <p class="font-medium">💡 Tips:</p>
                        <p>• Gunakan hasil <strong>Export Excel</strong> di atas sebagai template — langsung bisa diimport kembali</p>
                        <p>• Kolom: Tanggal, Tipe (Siswa/Tamu), NIS (siswa saja), Nama, Kelas/Instansi, Tujuan</p>
                        <p>• Kunjungan siswa yang sudah ada pada tanggal yang sama akan di-skip</p>
                        <p>• Atau download <a href="{{ route('admin.reports.visitors.template') }}" class="underline font-medium">template kosong</a></p>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('importVisitorModal').classList.add('hidden')" class="flex-1 px-4 py-3 border border-gray-300 text-gray-600 rounded-xl font-medium hover:bg-gray-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition cursor-pointer">
                        Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
