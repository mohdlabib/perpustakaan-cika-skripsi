@extends('layouts.student')

@section('title', 'Absensi QR - Perpustakaan')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8" x-data="qrScanner()">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Absensi Perpustakaan</h1>
        <p class="text-gray-600 mt-2">Scan QR Code untuk mencatat kehadiran</p>
    </div>
    
    @if(session('student'))
        <!-- Logged in student -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-primary-light rounded-full flex items-center justify-center">
                    <span class="text-primary-dark font-bold text-lg">{{ substr(session('student')->name, 0, 1) }}</span>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ session('student')->name }}</p>
                    <p class="text-gray-500 text-sm">NIS: {{ session('student')->nis }}</p>
                </div>
            </div>
        </div>
        
        <!-- QR Scanner -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-6">
                <div x-show="!scanning && !success" class="text-center">
                    <div class="w-20 h-20 bg-accent-green/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Siap untuk Scan</h3>
                    <p class="text-gray-500 mb-6">Klik tombol di bawah untuk membuka kamera</p>
                    <button @click="startScanner()" class="px-8 py-3 bg-accent-green text-white rounded-xl font-semibold hover:bg-opacity-90 transition">
                        🎥 Mulai Scan
                    </button>
                </div>
                
                <div x-show="scanning" x-cloak>
                    <div id="qr-reader" class="rounded-xl overflow-hidden"></div>
                    <button @click="stopScanner()" class="w-full mt-4 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition">
                        Batal
                    </button>
                </div>
                
                <div x-show="success" x-cloak class="text-center py-8">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-fade-in">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-green-600 mb-2">Absensi Berhasil!</h3>
                    <p class="text-gray-500" x-text="successMessage"></p>
                </div>
                
                <div x-show="error" x-cloak class="text-center py-8">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-red-600 mb-2">Gagal</h3>
                    <p class="text-gray-500" x-text="errorMessage"></p>
                    <button @click="reset()" class="mt-4 px-6 py-2 bg-gray-200 text-gray-600 rounded-xl hover:bg-gray-300 transition">
                        Coba Lagi
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Demo QR Section -->
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-center">
            <h4 class="font-semibold text-yellow-800 mb-2">Mode Demo</h4>
            <p class="text-yellow-700 text-sm mb-4">Untuk demo, Anda bisa klik tombol di bawah untuk simulasi scan QR:</p>
            <button @click="demoScan()" class="px-6 py-2 bg-yellow-500 text-white rounded-xl text-sm font-medium hover:bg-yellow-600 transition">
                Simulasi Scan QR
            </button>
        </div>
    @else
        <!-- Not logged in -->
        <div class="bg-white rounded-2xl shadow-sm p-8 text-center border border-gray-100">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Login Diperlukan</h3>
            <p class="text-gray-500 mb-6">Silakan login dengan NIS untuk melakukan absensi</p>
            <a href="{{ route('student.login') }}" class="inline-block px-8 py-3 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition">
                Login Sekarang
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function qrScanner() {
    return {
        scanning: false,
        success: false,
        error: false,
        successMessage: '',
        errorMessage: '',
        scanner: null,
        
        startScanner() {
            this.scanning = true;
            this.success = false;
            this.error = false;
            
            this.scanner = new Html5QrcodeScanner("qr-reader", {
                qrbox: { width: 250, height: 250 },
                fps: 10,
            });
            
            this.scanner.render((decodedText) => {
                this.handleScan(decodedText);
            }, (error) => {
                // Ignore errors during scanning
            });
        },
        
        stopScanner() {
            if (this.scanner) {
                this.scanner.clear();
            }
            this.scanning = false;
        },
        
        async handleScan(token) {
            this.stopScanner();
            
            try {
                const response = await axios.post('{{ route("attendance.store") }}', { token });
                if (response.data.success) {
                    this.success = true;
                    this.successMessage = response.data.message;
                }
            } catch (error) {
                this.error = true;
                this.errorMessage = error.response?.data?.message || 'Terjadi kesalahan';
            }
        },
        
        demoScan() {
            this.handleScan('SCHOOLING_LIBRARY_CHECKIN_TOKEN');
        },
        
        reset() {
            this.scanning = false;
            this.success = false;
            this.error = false;
        }
    }
}
</script>
@endpush
@endsection
