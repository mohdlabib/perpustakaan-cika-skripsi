<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentLoginController extends Controller
{
    /**
     * Show student login form.
     */
    public function showLoginForm()
    {
        return view('auth.student-login');
    }

    /**
     * Handle student login (NIS only - no password).
     */
    public function login(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|max:20',
        ]);

        $student = Student::with('grade')->find($request->nis);

        if (!$student) {
            return back()->withErrors([
                'nis' => 'NIS tidak ditemukan dalam sistem.',
            ])->withInput();
        }

        // Check if student has a grade assigned
        if (!$student->grade) {
            return back()->withErrors([
                'nis' => 'Akun siswa belum memiliki angkatan. Hubungi admin.',
            ])->withInput();
        }

        // Check if grade is active
        if (!$student->grade->is_active) {
            return back()->withErrors([
                'nis' => 'Angkatan Anda (' . $student->grade->name . ') sudah tidak aktif. Hubungi admin jika ada kesalahan.',
            ])->withInput();
        }

        // Store student in session
        session(['student' => $student]);
        session(['student_nis' => $student->nis]);

        return redirect()->route('catalog.index')
            ->with('success', "Selamat datang, {$student->name}!");
    }

    /**
     * Logout student.
     */
    public function logout()
    {
        session()->forget(['student', 'student_nis']);
        
        return redirect()->route('student.login')
            ->with('success', 'Berhasil keluar.');
    }
}

