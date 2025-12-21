@extends('layouts.admin')

@section('page-title', isset($shelf) ? 'Edit Rak' : 'Tambah Rak')

@section('content')
<div class="max-w-xl mx-auto">
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="{{ route('admin.shelves.index') }}" class="hover:text-primary-dark transition">Rak</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-medium">{{ isset($shelf) ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-primary-dark to-green-700 px-8 py-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ isset($shelf) ? 'Edit Rak' : 'Tambah Rak Baru' }}</h2>
                    <p class="text-white/70 text-sm">Kelola lokasi rak dan kolom perpustakaan</p>
                </div>
            </div>
        </div>

        <form action="{{ isset($shelf) ? route('admin.shelves.update', $shelf) : route('admin.shelves.store') }}" method="POST" class="p-8 space-y-6" x-data="columnsManager()">
            @csrf
            @if(isset($shelf))
                @method('PUT')
            @endif
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Rak <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code', $shelf->code ?? '') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                        placeholder="Contoh: A-01">
                    @error('code')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Rak <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $shelf->name ?? '') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                        placeholder="Contoh: Rak Fiksi">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $shelf->location ?? '') }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                    placeholder="Contoh: Lantai 1">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="2"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                    placeholder="Deskripsi rak (opsional)">{{ old('description', $shelf->description ?? '') }}</textarea>
            </div>

            <!-- Columns Section -->
            <div class="border-t border-gray-100 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">Kolom Rak</h3>
                        <p class="text-gray-500 text-sm">Tambahkan kolom dalam rak ini (A, B, C atau 1, 2, 3)</p>
                    </div>
                    <button type="button" @click="addColumn()" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg font-medium hover:bg-blue-700 transition flex items-center gap-1 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Kolom
                    </button>
                </div>
                
                <div class="space-y-2">
                    <template x-for="(column, index) in columns" :key="index">
                        <div class="flex gap-2">
                            <input type="text" :name="'columns[' + index + ']'" x-model="columns[index]" required
                                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition text-sm"
                                placeholder="Nama kolom (contoh: A, B, atau 1)">
                            <button type="button" @click="removeColumn(index)" class="p-2.5 text-red-600 hover:bg-red-50 rounded-xl transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                    <p x-show="columns.length === 0" class="text-gray-400 text-sm italic py-2">Belum ada kolom. Klik tombol "Tambah Kolom" untuk menambahkan.</p>
                </div>
            </div>

            <div class="flex gap-4 pt-4 border-t border-gray-100">
                <button type="submit" class="px-8 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
                <a href="{{ route('admin.shelves.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition cursor-pointer">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function columnsManager() {
    return {
        columns: @json(old('columns', isset($shelf) ? $shelf->columns->pluck('name')->toArray() : [])),
        addColumn() {
            this.columns.push('');
        },
        removeColumn(index) {
            this.columns.splice(index, 1);
        }
    }
}
</script>
@endpush
@endsection

