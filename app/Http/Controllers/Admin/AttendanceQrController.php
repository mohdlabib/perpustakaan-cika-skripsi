<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Visit;

class AttendanceQrController extends Controller
{
    public function index()
    {
        // Get today's attendance stats
        $todayVisits = Visit::whereDate('visited_at', today())->count();
        $weekVisits = Visit::whereBetween('visited_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $monthVisits = Visit::whereMonth('visited_at', now()->month)->whereYear('visited_at', now()->year)->count();
        
        // Get recent visits
        $recentVisits = Visit::with('student')
            ->whereDate('visited_at', today())
            ->orderByDesc('visited_at')
            ->limit(10)
            ->get();
        
        // Generate daily token
        $dailyToken = $this->generateDailyToken();
        
        return view('admin.attendance.index', compact('todayVisits', 'weekVisits', 'monthVisits', 'recentVisits', 'dailyToken'));
    }
    
    public function generateQr(Request $request)
    {
        $type = $request->input('type', 'daily'); // daily, permanent, custom
        
        if ($type === 'daily') {
            $token = $this->generateDailyToken();
            $label = 'QR Harian - ' . now()->format('d M Y');
        } elseif ($type === 'permanent') {
            $token = 'PERPUS_SMAN8_PEKANBARU_CHECKIN';
            $label = 'QR Permanen';
        } else {
            $token = 'PERPUS_CHECKIN_' . Str::random(8);
            $label = 'QR Custom - ' . now()->format('d M Y H:i');
        }
        
        return response()->json([
            'success' => true,
            'token' => $token,
            'label' => $label,
            'qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($token),
        ]);
    }
    
    private function generateDailyToken()
    {
        // Generate a unique token for today
        $date = now()->format('Y-m-d');
        return 'PERPUS_SMAN8_' . strtoupper(md5('attendance_' . $date . '_secret'));
    }
}
