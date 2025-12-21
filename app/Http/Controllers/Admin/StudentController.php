<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('grade')->orderBy('name');
        
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('nis', 'like', "%{$request->search}%")
                  ->orWhere('class', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->filled('grade')) {
            $query->where('grade_id', $request->grade);
        }
        
        $students = $query->paginate(15);
        $grades = Grade::active()->get();
        
        return view('admin.students.index', compact('students', 'grades'));
    }

    public function create()
    {
        $grades = Grade::active()->get();
        return view('admin.students.create', compact('grades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|max:20|unique:students_registry,nis',
            'name' => 'required|string|max:100',
            'class' => 'nullable|string|max:20',
            'grade_id' => 'required|exists:grades,id',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);
        
        $data = $request->only(['nis', 'name', 'class', 'grade_id', 'phone']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        Student::create($data);
        
        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        $grades = Grade::active()->get();
        return view('admin.students.edit', compact('student', 'grades'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'class' => 'nullable|string|max:20',
            'grade_id' => 'required|exists:grades,id',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);
        
        $data = $request->only(['name', 'class', 'grade_id', 'phone']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $student->update($data);
        
        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        
        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }
}
