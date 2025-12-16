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
     * Record attendance from QR scan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        // Verify the QR token
        if ($request->token !== 'SCHOOLING_LIBRARY_CHECKIN_TOKEN') {
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
     * Get today's attendance statistics.
     */
    public function todayStats()
    {
        $todayCount = Visit::today()->count();
        $weekCount = Visit::thisWeek()->count();
        
        return response()->json([
            'today' => $todayCount,
            'this_week' => $weekCount,
        ]);
    }
}
