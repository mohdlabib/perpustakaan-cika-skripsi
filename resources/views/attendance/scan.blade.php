@extends('layouts.student')

@section('title', 'Pengunjung - Perpustakaan Jendela Ilmu')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8" x-data="attendancePage()">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Pengunjung Perpustakaan Jendela Ilmu</h1>
        <p class="text-gray-600 mt-2">Catat kehadiran Anda di perpustakaan</p>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex bg-white rounded-2xl p-1.5 mb-6 border border-gray-100 shadow-sm">
        @if(session('student'))
            <button @click="activeTab = 'scan'" 
                :class="activeTab === 'scan' ? 'bg-primary-dark text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'"
                class="flex-1 py-3 px-4 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                Scan QR (Siswa)
            </button>
        @endif
        <button @click="activeTab = 'guest'" 
            :class="activeTab === 'guest' ? 'bg-primary-dark text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'"
            class="flex-1 py-3 px-4 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Kunjungan Tamu
        </button>
    </div>
    
    @if(session('student'))
        {{-- ===== TAB: QR Scanner (Siswa) ===== --}}
        <div x-show="activeTab === 'scan'" x-cloak>
            {{-- Student Info --}}
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
            
            {{-- QR Scanner --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div x-show="!scanning && !scanSuccess && !scanError" class="text-center">
                        <div class="w-20 h-20 bg-accent-green/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Siap untuk Scan</h3>
                        <p class="text-gray-500 mb-6">Klik tombol di bawah untuk membuka kamera</p>
                        <button @click="startScanner()" class="px-8 py-3 bg-accent-green text-white rounded-xl font-semibold hover:bg-opacity-90 transition cursor-pointer">
                            🎥 Mulai Scan
                        </button>
                    </div>
                    
                    <div x-show="scanning" x-cloak>
                        <div id="qr-reader" class="rounded-xl overflow-hidden"></div>
                        <button @click="stopScanner()" class="w-full mt-4 py-3 border-2 border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                    
                    <div x-show="scanSuccess" x-cloak class="text-center py-8">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-green-600 mb-2">Kehadiran Tercatat!</h3>
                        <p class="text-gray-500" x-text="scanSuccessMessage"></p>
                    </div>
                    
                    <div x-show="scanError" x-cloak class="text-center py-8">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-red-600 mb-2">Gagal</h3>
                        <p class="text-gray-500" x-text="scanErrorMessage"></p>
                        <button @click="resetScan()" class="mt-4 px-6 py-2 bg-gray-200 text-gray-600 rounded-xl hover:bg-gray-300 transition cursor-pointer">
                            Coba Lagi
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- Demo QR Section --}}
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-2xl p-6 text-center">
                <h4 class="font-semibold text-blue-800 mb-2">Mode Demo</h4>
                <p class="text-blue-700 text-sm mb-4">Untuk demo, Anda bisa klik tombol di bawah untuk simulasi scan QR:</p>
                <button @click="demoScan()" class="px-6 py-2 bg-blue-500 text-white rounded-xl text-sm font-medium hover:bg-blue-600 transition cursor-pointer">
                    Simulasi Scan QR
                </button>
            </div>
        </div>
    @endif

    {{-- ===== TAB: Kunjungan Tamu ===== --}}
    <div x-show="activeTab === 'guest'" x-cloak>
        {{-- Guest Success --}}
        <div x-show="guestSuccess" x-cloak class="bg-white rounded-2xl shadow-sm p-8 text-center border border-gray-100 mb-6">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-green-600 mb-2">Kunjungan Tercatat!</h3>
            <p class="text-gray-500" x-text="guestSuccessMessage"></p>
            <button @click="resetGuest()" class="mt-6 px-6 py-2.5 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition cursor-pointer">
                Catat Kunjungan Lain
            </button>
        </div>

        {{-- Guest Form --}}
        <div x-show="!guestSuccess" class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-primary-dark to-green-700 p-6 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Form Kunjungan Tamu</h3>
                        <p class="text-white/70 text-sm">Isi formulir di bawah untuk mencatat kunjungan Anda</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-5">
                {{-- Error display --}}
                <div x-show="guestError" x-cloak class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm" x-text="guestErrorMessage"></span>
                </div>

                {{-- Nama Pengunjung --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="guestForm.guest_name" placeholder="Masukkan nama lengkap Anda"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition"
                        :class="guestErrors.guest_name ? 'border-red-300' : ''">
                    <p x-show="guestErrors.guest_name" x-text="guestErrors.guest_name" class="text-red-500 text-xs mt-1"></p>
                </div>

                {{-- Instansi/Asal --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Asal Instansi / Sekolah
                    </label>
                    <input type="text" x-model="guestForm.guest_institution" placeholder="Contoh: Universitas Riau, SMAN 1 Pekanbaru"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition">
                </div>

                {{-- Tujuan Kunjungan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tujuan Kunjungan
                    </label>
                    <select x-model="guestForm.guest_purpose"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-dark focus:border-transparent transition cursor-pointer">
                        <option value="">Pilih tujuan kunjungan</option>
                        <option value="Membaca buku">Membaca buku</option>
                        <option value="Mengerjakan tugas">Mengerjakan tugas</option>
                        <option value="Referensi penelitian">Referensi penelitian</option>
                        <option value="Studi banding">Studi banding</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                {{-- Submit Button --}}
                <button @click="submitGuest()" :disabled="guestLoading"
                    class="w-full py-3.5 bg-primary-dark text-white rounded-xl font-semibold hover:bg-opacity-90 transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="guestLoading">
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <template x-if="!guestLoading">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <span x-text="guestLoading ? 'Menyimpan...' : 'Catat Kunjungan'"></span>
                </button>
            </div>
        </div>

        {{-- Info Card --}}
        @if(!session('student'))
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-2xl p-5">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-blue-800 font-semibold text-sm">Siswa SMAN 8 Pekanbaru?</p>
                    <p class="text-blue-700 text-sm mt-1">
                        <a href="{{ route('student.login') }}" class="underline font-medium hover:text-blue-900 transition">Login dengan NIS</a> 
                        untuk mencatat kehadiran otomatis via scan QR Code.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Today's Stats --}}
    <div class="mt-8 bg-white rounded-2xl shadow-sm p-5 border border-gray-100" x-data="attendanceStats()" x-init="loadStats()">
        <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Statistik Hari Ini
        </h4>
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center p-3 bg-green-50 rounded-xl">
                <div class="text-2xl font-bold text-green-700" x-text="stats.today">-</div>
                <div class="text-xs text-green-600 mt-1">Hari Ini</div>
            </div>
            <div class="text-center p-3 bg-blue-50 rounded-xl">
                <div class="text-2xl font-bold text-blue-700" x-text="stats.this_week">-</div>
                <div class="text-xs text-blue-600 mt-1">Minggu Ini</div>
            </div>
            <div class="text-center p-3 bg-purple-50 rounded-xl">
                <div class="text-2xl font-bold text-purple-700" x-text="stats.guests_today">-</div>
                <div class="text-xs text-purple-600 mt-1">Tamu Hari Ini</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function attendancePage() {
    return {
        activeTab: '{{ session("student") ? "scan" : "guest" }}',
        
        // QR Scanner state
        scanning: false,
        scanSuccess: false,
        scanError: false,
        scanSuccessMessage: '',
        scanErrorMessage: '',
        scanner: null,
        
        // Guest form state
        guestForm: {
            guest_name: '',
            guest_institution: '',
            guest_purpose: '',
        },
        guestLoading: false,
        guestSuccess: false,
        guestSuccessMessage: '',
        guestError: false,
        guestErrorMessage: '',
        guestErrors: {},
        
        // QR Scanner methods
        async startScanner() {
            this.scanning = true;
            this.scanSuccess = false;
            this.scanError = false;
            
            this.scanner = new Html5Qrcode("qr-reader");
            
            const config = {
                qrbox: { width: 250, height: 250 },
                fps: 10,
            };
            
            try {
                await this.scanner.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText) => {
                        this.handleScan(decodedText);
                    },
                    (error) => {}
                );
            } catch (err) {
                console.error("Camera error:", err);
                this.scanError = true;
                this.scanErrorMessage = 'Gagal mengakses kamera. Pastikan izin kamera diberikan.';
                this.scanning = false;
            }
        },
        
        async stopScanner() {
            if (this.scanner) {
                try {
                    await this.scanner.stop();
                    this.scanner.clear();
                } catch (err) {
                    console.error("Stop scanner error:", err);
                }
            }
            this.scanning = false;
        },
        
        async handleScan(token) {
            this.stopScanner();
            
            try {
                const response = await axios.post('{{ route("attendance.store") }}', { token });
                if (response.data.success) {
                    this.scanSuccess = true;
                    this.scanSuccessMessage = response.data.message;
                }
            } catch (error) {
                this.scanError = true;
                this.scanErrorMessage = error.response?.data?.message || 'Terjadi kesalahan';
            }
        },
        
        demoScan() {
            this.handleScan('SCHOOLING_LIBRARY_CHECKIN_TOKEN');
        },
        
        resetScan() {
            this.scanning = false;
            this.scanSuccess = false;
            this.scanError = false;
        },
        
        // Guest form methods
        async submitGuest() {
            this.guestErrors = {};
            this.guestError = false;
            
            // Client-side validation
            if (!this.guestForm.guest_name || this.guestForm.guest_name.trim() === '') {
                this.guestErrors.guest_name = 'Nama wajib diisi.';
                return;
            }
            
            this.guestLoading = true;
            
            try {
                const response = await axios.post('{{ route("attendance.store-guest") }}', this.guestForm);
                if (response.data.success) {
                    this.guestSuccess = true;
                    this.guestSuccessMessage = response.data.message;
                    // Reset form
                    this.guestForm = { guest_name: '', guest_institution: '', guest_purpose: '' };
                }
            } catch (error) {
                if (error.response?.status === 422) {
                    const errors = error.response.data.errors || {};
                    for (const [key, msgs] of Object.entries(errors)) {
                        this.guestErrors[key] = msgs[0];
                    }
                } else {
                    this.guestError = true;
                    this.guestErrorMessage = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan kunjungan.';
                }
            }
            
            this.guestLoading = false;
        },
        
        resetGuest() {
            this.guestSuccess = false;
            this.guestSuccessMessage = '';
            this.guestError = false;
            this.guestErrors = {};
        }
    }
}

function attendanceStats() {
    return {
        stats: { today: '-', this_week: '-', guests_today: '-' },
        async loadStats() {
            try {
                const response = await axios.get('{{ route("attendance.stats") }}');
                this.stats = response.data;
            } catch (e) {
                console.error('Stats error:', e);
            }
        }
    }
}
</script>
@endpush
@endsection
