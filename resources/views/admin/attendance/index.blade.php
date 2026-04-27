@extends('layouts.admin')

@section('page-title', 'Generate QR Pengunjung')

@section('content')
<div x-data="qrGenerator()">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $todayVisits }}</div>
                    <div class="text-gray-500 text-sm">Kunjungan Hari Ini</div>
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
                    <div class="text-2xl font-bold text-gray-800">{{ $weekVisits }}</div>
                    <div class="text-gray-500 text-sm">Minggu Ini</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $monthVisits }}</div>
                    <div class="text-gray-500 text-sm">Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- QR Generator -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                Generate QR Code Pengunjung
            </h2>
            
            <!-- QR Type Selection -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                <button @click="generateQr('daily')" 
                        :class="selectedType === 'daily' ? 'ring-2 ring-blue-500 bg-blue-50' : 'hover:bg-gray-50'"
                        class="p-4 rounded-xl border border-gray-200 transition cursor-pointer text-center">
                    <svg class="w-8 h-8 mx-auto mb-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium text-gray-800 block">QR Harian</span>
                    <span class="text-xs text-gray-500">Berubah setiap hari</span>
                </button>
                
                <button @click="generateQr('permanent')" 
                        :class="selectedType === 'permanent' ? 'ring-2 ring-green-500 bg-green-50' : 'hover:bg-gray-50'"
                        class="p-4 rounded-xl border border-gray-200 transition cursor-pointer text-center">
                    <svg class="w-8 h-8 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="font-medium text-gray-800 block">QR Permanen</span>
                    <span class="text-xs text-gray-500">Tidak berubah</span>
                </button>
            </div>
            
            <!-- QR Code Display -->
            <div x-show="qrData" x-cloak class="text-center">
                <div class="bg-gray-50 rounded-2xl p-6 mb-4">
                    <p class="text-sm text-gray-500 mb-3" x-text="qrData?.label"></p>
                    <img :src="qrData?.qr_url" alt="QR Code" class="w-64 h-64 mx-auto rounded-xl shadow-lg">
                    <p class="text-xs text-gray-400 mt-3 font-mono break-all" x-text="qrData?.token"></p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <button @click="downloadQr()" class="flex-1 py-3 bg-primary-dark text-white rounded-xl font-medium hover:bg-opacity-90 transition cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download QR
                    </button>
                    <button @click="printQr()" class="flex-1 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print QR
                    </button>
                </div>
            </div>
            
            <!-- Instructions -->
            <div x-show="!qrData" class="text-center py-8 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <p>Pilih jenis QR Code di atas untuk generate</p>
            </div>
        </div>
        
        <!-- Recent Visits -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Kunjungan Hari Ini
            </h2>
            
            @if($recentVisits->count() > 0)
                <div class="space-y-3">
                    @foreach($recentVisits as $visit)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        @if($visit->visitor_type === 'guest')
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center font-bold text-purple-700">
                                {{ substr($visit->guest_name ?? 'T', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 truncate">{{ $visit->guest_name ?? 'Tamu' }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-xs font-medium rounded">Tamu</span>
                                    <span class="text-gray-500 text-xs truncate">{{ $visit->guest_institution ?? '-' }}</span>
                                </div>
                            </div>
                        @else
                            <div class="w-10 h-10 bg-primary-light rounded-full flex items-center justify-center font-bold text-primary-dark">
                                {{ substr($visit->student->name ?? '?', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 truncate">{{ $visit->student->name ?? '-' }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-xs font-medium rounded">Siswa</span>
                                    <span class="text-gray-500 text-xs">{{ $visit->student->nis ?? '-' }}</span>
                                </div>
                            </div>
                        @endif
                        <div class="text-right flex-shrink-0">
                            <p class="text-gray-800 font-medium">{{ $visit->visited_at->format('H:i') }}</p>
                            <p class="text-gray-400 text-xs">{{ $visit->visited_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p>Belum ada kunjungan hari ini</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Instructions Card -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-2xl p-6">
        <h3 class="font-semibold text-blue-800 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Petunjuk Penggunaan
        </h3>
        <ul class="text-blue-700 text-sm space-y-2">
            <li class="flex items-start gap-2">
                <span class="font-bold">1.</span>
                <span>Generate QR Code dengan memilih jenis (Harian/Permanen)</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="font-bold">2.</span>
                <span>Download atau Print QR Code</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="font-bold">3.</span>
                <span>Pasang QR Code di meja/dinding perpustakaan</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="font-bold">4.</span>
                <span>Siswa login dengan NIS, lalu scan QR untuk catat kehadiran di: <code class="bg-blue-100 px-2 py-0.5 rounded">/attendance/scan</code></span>
            </li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
function qrGenerator() {
    return {
        selectedType: null,
        qrData: null,
        loading: false,
        
        async generateQr(type) {
            this.loading = true;
            this.selectedType = type;
            
            try {
                const response = await axios.post('{{ route("admin.attendance.generate") }}', { type });
                this.qrData = response.data;
            } catch (error) {
                showToast('Gagal generate QR Code', 'error');
            }
            
            this.loading = false;
        },
        
        downloadQr() {
            if (!this.qrData?.qr_url) return;
            
            const link = document.createElement('a');
            link.href = this.qrData.qr_url;
            link.download = 'qr-pengunjung-perpustakaan-' + new Date().toISOString().split('T')[0] + '.png';
            link.click();
        },
        
        printQr() {
            if (!this.qrData?.qr_url) return;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print QR Code Pengunjung</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            text-align: center; 
                            padding: 40px;
                        }
                        h1 { font-size: 28px; margin-bottom: 10px; }
                        h2 { font-size: 20px; color: #666; margin-bottom: 30px; }
                        img { width: 300px; height: 300px; }
                        p { margin-top: 20px; color: #888; font-size: 14px; }
                        .footer { margin-top: 40px; font-size: 12px; color: #aaa; }
                    </style>
                </head>
                <body>
                    <h1>📚 Perpustakaan Jendela Ilmu</h1>
                    <h2>Scan untuk Catat Kehadiran Pengunjung</h2>
                    <img src="${this.qrData.qr_url}" alt="QR Code">
                    <p>${this.qrData.label}</p>
                    <div class="footer">
                        <p>Login dengan NIS di /login/student lalu scan QR ini di /attendance/scan</p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    }
}
</script>
@endpush
@endsection
