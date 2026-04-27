@extends('layouts.admin')

@section('page-title', 'Kelola Siswa')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Student::count() }}</div>
                <div class="text-gray-500 text-sm">Total Siswa</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Grade::active()->count() }}</div>
                <div class="text-gray-500 text-sm">Angkatan Aktif</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Borrowing::active()->count() }}</div>
                <div class="text-gray-500 text-sm">Buku Dipinjam</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Visit::whereDate('visited_at', today())->count() }}</div>
                <div class="text-gray-500 text-sm">Kunjungan Hari Ini</div>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">Daftar Siswa</h2>
        <p class="text-gray-500 text-sm">Kelola data siswa perpustakaan</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.students.template') }}" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Template
        </a>
        <button onclick="document.getElementById('importStudentModal').classList.remove('hidden')" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Import Excel
        </button>
        <a href="{{ route('admin.students.export') }}" class="px-4 py-2.5 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Export Excel
        </a>
        <a href="{{ route('admin.students.create') }}" class="px-4 py-2.5 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Siswa
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl p-5 mb-6 border border-gray-100 shadow-sm">
    <form action="{{ route('admin.students.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1 relative">
            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..." 
                class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
        </div>
        <select name="grade" class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark cursor-pointer">
            <option value="">Semua Angkatan</option>
            @foreach($grades as $grade)
                <option value="{{ $grade->id }}" {{ request('grade') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-6 py-3 bg-gray-800 text-white rounded-xl font-medium hover:bg-gray-900 transition cursor-pointer">
            Cari
        </button>
    </form>
</div>

<!-- Students Table -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-600 text-sm">
                    <th class="px-6 py-4 font-semibold">Siswa</th>
                    <th class="px-6 py-4 font-semibold">NIS</th>
                    <th class="px-6 py-4 font-semibold">Kelas</th>
                    <th class="px-6 py-4 font-semibold">Angkatan</th>
                    <th class="px-6 py-4 font-semibold">Telepon</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($students as $student)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-light rounded-full flex items-center justify-center font-bold text-primary-dark">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $student->name }}</p>
                                @if($student->password)
                                    <span class="text-xs text-green-600">Password aktif</span>
                                @else
                                    <span class="text-xs text-gray-400">Belum ada password</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-gray-600">{{ $student->nis }}</td>
                    <td class="px-6 py-4">
                        @if($student->class)
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-lg">{{ $student->class }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($student->grade)
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg">{{ $student->grade->name }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $student->phone ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.students.edit', $student) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition cursor-pointer" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.students.destroy', $student) }}" method="POST" onsubmit="return confirmDelete(this, 'siswa {{ $student->name }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Tidak ada siswa ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
        {{ $students->withQueryString()->links() }}
    </div>
</div>

<!-- Import Modal -->
<div id="importStudentModal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('importStudentModal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-blue-600 p-5 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold">Import Data Siswa</h3>
                        <p class="text-white/70 text-sm">Upload file Excel (.xlsx, .xls, .csv)</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Excel</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-2">Format kolom: NIS, Nama, Kelas, Telepon. 
                        <a href="{{ route('admin.students.template') }}" class="text-blue-600 underline">Download template</a>
                    </p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('importStudentModal').classList.add('hidden')" class="flex-1 px-4 py-3 border border-gray-300 text-gray-600 rounded-xl font-medium hover:bg-gray-50 transition cursor-pointer">
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
