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

        $student = Student::find($request->nis);

        if (!$student) {
            return back()->withErrors([
                'nis' => 'NIS tidak ditemukan dalam sistem.',
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
