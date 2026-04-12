@extends('layouts.admin')

@section('page-title', 'Tambah Eksemplar - ' . $book->title)

@section('content')
<div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
    <a href="{{ route('admin.books.index') }}" class="hover:text-primary-dark transition">Kelola Buku</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.books.show', $book) }}" class="hover:text-primary-dark transition">{{ Str::limit($book->title, 30) }}</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-800 font-medium">Tambah Eksemplar</span>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Tambah Eksemplar Baru</h2>
            <p class="text-gray-500 text-sm">Untuk buku: <strong>{{ $book->title }}</strong></p>
        </div>

        <form action="{{ route('admin.books.copies.store', $book) }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Eksemplar</label>
                    <input type="text" name="copy_code" value="{{ old('copy_code') }}" placeholder="EKS-001"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                    @error('copy_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Inventaris</label>
                    <input type="text" name="inventory_code" value="{{ old('inventory_code') }}" placeholder="INV-001"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                    @error('inventory_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5" x-data="{ shelfId: '{{ old('shelf_id', '') }}', columns: [] }" x-init="if(shelfId) fetch('/admin/shelves/' + shelfId + '/columns').then(r => r.json()).then(d => columns = d)">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rak</label>
                    <select name="shelf_id" x-model="shelfId" @change="if(shelfId) fetch('/admin/shelves/' + shelfId + '/columns').then(r => r.json()).then(d => columns = d); else columns = []"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark cursor-pointer">
                        <option value="">Pilih Rak</option>
                        @foreach($shelves as $shelf)
                            <option value="{{ $shelf->id }}">{{ $shelf->code }} - {{ $shelf->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kolom Rak</label>
                    <select name="shelf_column_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark cursor-pointer">
                        <option value="">Pilih Kolom</option>
                        <template x-for="col in columns" :key="col.id">
                            <option :value="col.id" x-text="'Kolom ' + col.name"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi <span class="text-red-500">*</span></label>
                    <select name="condition" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark cursor-pointer">
                        <option value="baik" {{ old('condition', 'baik') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ old('condition') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="hilang" {{ old('condition') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Diterima</label>
                    <input type="date" name="received_date" value="{{ old('received_date') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ old('price') }}" min="0" step="100" placeholder="0"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea name="notes" rows="3" placeholder="Catatan tambahan tentang eksemplar ini..."
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.books.show', $book) }}" class="px-6 py-2.5 border border-gray-300 text-gray-600 rounded-xl font-medium hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition cursor-pointer">Simpan Eksemplar</button>
            </div>
        </form>
    </div>
</div>
@endsection
