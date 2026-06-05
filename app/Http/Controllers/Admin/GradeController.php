<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::withCount('students')->orderBy('name')->paginate(15);
        return view('admin.grades.index', compact('grades'));
    }

    public function create()
    {
        return view('admin.grades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'level' => 'nullable|string|max:20',
            'academic_year' => 'nullable|integer|min:2000|max:2100',
            'is_active' => 'nullable',
        ]);
        
        $data = $request->only(['name', 'level', 'academic_year']);
        $data['is_active'] = $request->boolean('is_active');
        
        Grade::create($data);
        
        return redirect()->route('admin.grades.index')
            ->with('success', 'Angkatan berhasil ditambahkan.');
    }

    public function edit(Grade $grade)
    {
        return view('admin.grades.edit', compact('grade'));
    }

    public function update(Request $request, Grade $grade)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'level' => 'nullable|string|max:20',
            'academic_year' => 'nullable|integer|min:2000|max:2100',
            'is_active' => 'nullable',
        ]);
        
        $data = $request->only(['name', 'level', 'academic_year']);
        $data['is_active'] = $request->boolean('is_active');
        
        $grade->update($data);
        
        return redirect()->route('admin.grades.index')
            ->with('success', 'Angkatan berhasil diperbarui.');
    }

    public function destroy(Grade $grade)
    {
        try {
            $studentNisArray = $grade->students()->pluck('nis')->toArray();
            $deletedStudents = count($studentNisArray);
            
            if (!empty($studentNisArray)) {
                // Release any book copies that are currently borrowed by these students
                $activeBorrowings = \App\Models\Borrowing::whereIn('student_nis', $studentNisArray)
                    ->where('status', 'borrowed')
                    ->get();
                
                foreach ($activeBorrowings as $borrowing) {
                    if ($borrowing->book_copy_id && $borrowing->bookCopy) {
                        $borrowing->bookCopy->update(['is_available' => true]);
                    }
                }

                // Delete all borrowings for these students
                \App\Models\Borrowing::whereIn('student_nis', $studentNisArray)->delete();
                
                // Delete all visits for these students
                \App\Models\Visit::whereIn('student_nis', $studentNisArray)->delete();
                
                // Delete all students in this grade
                $grade->students()->delete();
            }
            
            $grade->delete();
            
            $message = 'Angkatan berhasil dihapus.';
            if ($deletedStudents > 0) {
                $message = "Angkatan berhasil dihapus beserta {$deletedStudents} siswa dan seluruh data terkait (peminjaman & kunjungan).";
            }
            
            return redirect()->route('admin.grades.index')
                ->with('success', $message);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.grades.index')
                ->with('error', 'Angkatan tidak dapat dihapus karena masih terkait dengan data lain.');
        }
    }
}

