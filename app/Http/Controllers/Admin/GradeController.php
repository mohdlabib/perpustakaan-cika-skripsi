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
            'is_active' => 'boolean',
        ]);
        
        Grade::create($request->all());
        
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
            'is_active' => 'boolean',
        ]);
        
        $grade->update($request->all());
        
        return redirect()->route('admin.grades.index')
            ->with('success', 'Angkatan berhasil diperbarui.');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        
        return redirect()->route('admin.grades.index')
            ->with('success', 'Angkatan berhasil dihapus.');
    }
}
