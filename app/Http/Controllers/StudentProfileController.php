<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentProfileController extends Controller
{
    /**
     * Show student profile edit form.
     */
    public function edit()
    {
        $student = $this->getLoggedStudent();
        
        if (!$student) {
            return redirect()->route('student.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('student.profile', compact('student'));
    }

    /**
     * Update student profile (class field).
     */
    public function update(Request $request)
    {
        $student = $this->getLoggedStudent();
        
        if (!$student) {
            return redirect()->route('student.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'class' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
        ]);

        $student->update([
            'class' => $request->class,
            'phone' => $request->phone,
        ]);

        // Update session
        session(['student' => $student->fresh()]);

        return redirect()->route('student.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Get logged-in student from session.
     */
    protected function getLoggedStudent(): ?Student
    {
        $nis = session('student_nis');
        if (!$nis) {
            $student = session('student');
            $nis = $student?->nis;
        }
        
        return $nis ? Student::find($nis) : null;
    }
}
