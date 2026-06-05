<?php

namespace App\Http\Middleware;

use App\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentGrade
{
    /**
     * Check if the logged-in student's grade is still active.
     * If the grade was deactivated or the student was deleted, flush the session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $nis = session('student_nis');
        
        if ($nis) {
            $student = Student::with('grade')->find($nis);
            
            // Student was deleted
            if (!$student) {
                session()->forget(['student', 'student_nis']);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun siswa tidak ditemukan. Silakan hubungi admin.',
                    ], 401);
                }
                
                return redirect()->route('student.login')
                    ->withErrors(['nis' => 'Akun siswa tidak ditemukan. Silakan hubungi admin.']);
            }

            // Grade not assigned or deactivated
            if (!$student->grade || !$student->grade->is_active) {
                session()->forget(['student', 'student_nis']);
                
                $gradeName = $student->grade->name ?? 'Unknown';
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Angkatan Anda ({$gradeName}) sudah tidak aktif. Hubungi admin.",
                    ], 403);
                }
                
                return redirect()->route('student.login')
                    ->withErrors(['nis' => "Angkatan Anda ({$gradeName}) sudah tidak aktif. Hubungi admin jika ada kesalahan."]);
            }

            // Refresh student data in session (in case name/class changed)
            session(['student' => $student]);
        }

        return $next($request);
    }
}
