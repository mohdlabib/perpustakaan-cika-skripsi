<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Visit;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Show QR scanner page.
     */
    public function scan()
    {
        return view('attendance.scan');
    }

    /**
     * Record attendance from QR scan (student).
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        // Verify the QR token - accept various valid tokens
        $validTokens = [
            'SCHOOLING_LIBRARY_CHECKIN_TOKEN',
            'PERPUS_SMAN8_PEKANBARU_CHECKIN', // Permanent token
            $this->generateDailyToken(), // Today's daily token
        ];
        
        // Also accept any token starting with PERPUS_CHECKIN_ (custom tokens)
        $isValid = in_array($request->token, $validTokens) || 
                   str_starts_with($request->token, 'PERPUS_CHECKIN_') ||
                   str_starts_with($request->token, 'PERPUS_SMAN8_');
        
        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Kode QR tidak valid.',
            ], 400);
        }

        // Check if student is logged in
        $nis = session('student_nis');
        if (!$nis) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }

        // Check if student exists
        $student = Student::find($nis);
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        // Check if already visited today
        if (Visit::hasVisitedToday($nis)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen hari ini.',
                'already_visited' => true,
            ], 400);
        }

        // Record the visit
        $visit = Visit::recordVisit($nis, $request->token);

        return response()->json([
            'success' => true,
            'message' => "Selamat datang di Perpustakaan, {$student->name}!",
            'visit' => [
                'id' => $visit->id,
                'visited_at' => $visit->visited_at->format('d M Y H:i'),
                'student_name' => $student->name,
            ],
        ]);
    }

    /**
     * Record guest attendance (non-student).
     */
    public function storeGuest(Request $request)
    {
        $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_institution' => 'nullable|string|max:255',
            'guest_purpose' => 'nullable|string|max:500',
        ]);

        $visit = Visit::recordGuestVisit(
            $request->guest_name,
            $request->guest_institution,
            $request->guest_purpose
        );

        return response()->json([
            'success' => true,
            'message' => "Selamat datang, {$request->guest_name}! Kunjungan berhasil dicatat.",
            'visit' => [
                'id' => $visit->id,
                'visited_at' => $visit->visited_at->format('d M Y H:i'),
                'guest_name' => $request->guest_name,
            ],
        ]);
    }

    /**
     * Get today's attendance statistics.
     */
    public function todayStats()
    {
        $todayCount = Visit::today()->count();
        $weekCount = Visit::thisWeek()->count();
        $guestTodayCount = Visit::today()->guests()->count();
        
        return response()->json([
            'today' => $todayCount,
            'this_week' => $weekCount,
            'guests_today' => $guestTodayCount,
        ]);
    }
    
    /**
     * Generate daily token for attendance.
     */
    private function generateDailyToken()
    {
        $date = now()->format('Y-m-d');
        return 'PERPUS_SMAN8_' . strtoupper(md5('attendance_' . $date . '_secret'));
    }
}
