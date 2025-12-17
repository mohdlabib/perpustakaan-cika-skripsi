@extends('layouts.admin')

@section('page-title', 'Scan Barcode')

@section('content')
<div x-data="barcodeScanner()" class="max-w-4xl mx-auto">
    <!-- Scan Type Selection -->
    <div class="bg-white rounded-2xl p-6 mb-6 border border-gray-100 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Pilih Mode Scan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <button @click="scanType = 'book'" 
                    :class="scanType === 'book' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-200 hover:border-blue-300'"
                    class="p-4 rounded-xl border-2 transition flex flex-col items-center gap-2 cursor-pointer">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="font-medium">Cari Buku</span>
                <span class="text-xs opacity-75">Scan ISBN/Kode Buku</span>
            </button>
            
            <button @click="scanType = 'student'" 
                    :class="scanType === 'student' ? 'bg-green-500 text-white border-green-500' : 'bg-white text-gray-700 border-gray-200 hover:border-green-300'"
                    class="p-4 rounded-xl border-2 transition flex flex-col items-center gap-2 cursor-pointer">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="font-medium">Cari Siswa</span>
                <span class="text-xs opacity-75">Scan Kartu/NIS</span>
            </button>
            
            <button @click="scanType = 'return'" 
                    :class="scanType === 'return' ? 'bg-purple-500 text-white border-purple-500' : 'bg-white text-gray-700 border-gray-200 hover:border-purple-300'"
                    class="p-4 rounded-xl border-2 transition flex flex-col items-center gap-2 cursor-pointer">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                <span class="font-medium">Pengembalian</span>
                <span class="text-xs opacity-75">Scan untuk return</span>
            </button>
        </div>
    </div>
    
    <!-- Scanner Area -->
    <div class="bg-white rounded-2xl p-6 mb-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Scanner</h2>
            <button @click="toggleCamera()" 
                    :class="cameraActive ? 'bg-red-500 hover:bg-red-600' : 'bg-primary-dark hover:bg-opacity-90'"
                    class="px-4 py-2 text-white rounded-xl font-medium transition flex items-center gap-2 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-text="cameraActive ? 'Stop Kamera' : 'Buka Kamera'"></span>
            </button>
        </div>
        
        <!-- Camera View -->
        <div class="mb-4">
            <!-- Camera Placeholder (shown when inactive) -->
            <div x-show="!cameraActive" class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl p-8 flex flex-col items-center justify-center min-h-[280px] border-2 border-dashed border-gray-300">
                <div class="w-20 h-20 bg-white/80 rounded-full flex items-center justify-center mb-4 shadow-lg">
                    <svg class="w-10 h-10 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-gray-600 font-medium text-lg mb-1">Kamera Tidak Aktif</p>
                <p class="text-gray-500 text-sm">Klik tombol "Buka Kamera" untuk memulai scanning</p>
            </div>
            
            <!-- Actual Camera Preview (shown when active) -->
            <div x-show="cameraActive" x-cloak class="relative">
                <div id="reader" class="rounded-xl overflow-hidden bg-black min-h-[280px]"></div>
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/60 text-white px-4 py-2 rounded-full text-sm backdrop-blur-sm">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                        Arahkan kamera ke barcode
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Manual Input -->
        <div class="flex gap-3">
            <input type="text" x-model="manualCode" @keydown.enter="processCode(manualCode)"
                   placeholder="Ketik kode manual atau scan barcode..."
                   class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent">
            <button @click="processCode(manualCode)" :disabled="loading || !manualCode"
                    class="px-6 py-3 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                <span x-show="!loading">Proses</span>
                <span x-show="loading">...</span>
            </button>
        </div>
    </div>
    
    <!-- Result Area -->
    <div x-show="result" x-cloak class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div :class="result?.success ? 'bg-green-100' : 'bg-red-100'" class="w-12 h-12 rounded-xl flex items-center justify-center">
                <svg x-show="result?.success" class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg x-show="!result?.success" class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800" x-text="result?.success ? 'Berhasil!' : 'Tidak Ditemukan'"></h3>
                <p class="text-gray-500 text-sm" x-text="result?.message || ''"></p>
            </div>
        </div>
        
        <!-- Book Result -->
        <template x-if="result?.type === 'book' && result?.data">
            <div class="bg-blue-50 rounded-xl p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-500 text-sm">Judul</p>
                        <p class="font-semibold text-gray-800" x-text="result.data.title"></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Pengarang</p>
                        <p class="font-medium text-gray-700" x-text="result.data.author"></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">ISBN</p>
                        <p class="font-mono text-gray-700" x-text="result.data.isbn || '-'"></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Stok Tersedia</p>
                        <p class="font-bold text-green-600" x-text="result.data.available + ' / ' + result.data.stock"></p>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <a :href="'/admin/books/' + result.data.id + '/edit'" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 cursor-pointer">Edit Buku</a>
                </div>
            </div>
        </template>
        
        <!-- Student Result -->
        <template x-if="result?.type === 'student' && result?.data">
            <div class="bg-green-50 rounded-xl p-4">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">Nama</p>
                        <p class="font-semibold text-gray-800" x-text="result.data.name"></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">NIS</p>
                        <p class="font-mono text-gray-700" x-text="result.data.nis"></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Kelas</p>
                        <p class="font-medium text-gray-700" x-text="result.data.class || '-'"></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Status</p>
                        <p :class="result.data.can_borrow ? 'text-green-600' : 'text-red-600'" class="font-medium" x-text="result.data.can_borrow ? 'Dapat meminjam' : 'Limit tercapai'"></p>
                    </div>
                </div>
                <template x-if="result.data.active_borrowings && result.data.active_borrowings.length > 0">
                    <div>
                        <p class="text-gray-700 font-medium mb-2">Buku yang sedang dipinjam:</p>
                        <div class="space-y-2">
                            <template x-for="borrow in result.data.active_borrowings" :key="borrow.id">
                                <div class="flex justify-between items-center bg-white rounded-lg p-3">
                                    <span x-text="borrow.book_title" class="text-gray-800"></span>
                                    <span :class="borrow.is_overdue ? 'text-red-600' : 'text-gray-500'" class="text-sm" x-text="'Due: ' + borrow.due_date"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </template>
        
        <!-- Return Result -->
        <template x-if="result?.type === 'return' && result?.data">
            <div class="bg-purple-50 rounded-xl p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-500 text-sm">Buku</p>
                        <p class="font-semibold text-gray-800" x-text="result.data.book_title"></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Peminjam</p>
                        <p class="font-medium text-gray-700" x-text="result.data.student_name"></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Tanggal Pinjam</p>
                        <p class="text-gray-700" x-text="result.data.borrow_date"></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Tanggal Kembali</p>
                        <p class="font-medium text-green-600" x-text="result.data.return_date"></p>
                    </div>
                </div>
            </div>
        </template>
        
        <button @click="result = null; manualCode = ''" class="mt-4 w-full py-3 border border-gray-200 text-gray-600 rounded-xl font-medium hover:bg-gray-50 transition cursor-pointer">
            Scan Lagi
        </button>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function barcodeScanner() {
    return {
        scanType: 'book',
        cameraActive: false,
        manualCode: '',
        loading: false,
        result: null,
        html5QrCode: null,
        
        async toggleCamera() {
            if (this.cameraActive) {
                this.stopCamera();
            } else {
                this.startCamera();
            }
        },
        
        async startCamera() {
            try {
                this.html5QrCode = new Html5Qrcode("reader");
                await this.html5QrCode.start(
                    { facingMode: "environment" },
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 150 },
                        aspectRatio: 1.5,
                        formatsToSupport: [
                            Html5QrcodeSupportedFormats.QR_CODE,
                            Html5QrcodeSupportedFormats.EAN_13,
                            Html5QrcodeSupportedFormats.EAN_8,
                            Html5QrcodeSupportedFormats.CODE_128,
                            Html5QrcodeSupportedFormats.CODE_39,
                            Html5QrcodeSupportedFormats.CODE_93,
                            Html5QrcodeSupportedFormats.UPC_A,
                            Html5QrcodeSupportedFormats.UPC_E,
                            Html5QrcodeSupportedFormats.ITF,
                            Html5QrcodeSupportedFormats.CODABAR,
                        ]
                    },
                    (decodedText) => {
                        this.processCode(decodedText);
                        this.stopCamera();
                    },
                    (errorMessage) => {
                        // Ignore scan errors
                    }
                );
                this.cameraActive = true;
            } catch (err) {
                console.error("Camera error:", err);
                alert("Gagal membuka kamera. Pastikan izin kamera diberikan.");
            }
        },
        
        async stopCamera() {
            if (this.html5QrCode) {
                try {
                    await this.html5QrCode.stop();
                } catch (err) {
                    console.error("Stop camera error:", err);
                }
            }
            this.cameraActive = false;
        },
        
        async processCode(code) {
            if (!code || this.loading) return;
            
            this.loading = true;
            this.result = null;
            
            try {
                const response = await axios.post('{{ route("admin.scan.process") }}', {
                    code: code.trim(),
                    type: this.scanType,
                });
                
                this.result = response.data;
                this.manualCode = '';
            } catch (error) {
                this.result = {
                    success: false,
                    message: error.response?.data?.message || 'Terjadi kesalahan saat memproses kode.',
                };
            }
            
            this.loading = false;
        }
    }
}
</script>
@endpush
@endsection

