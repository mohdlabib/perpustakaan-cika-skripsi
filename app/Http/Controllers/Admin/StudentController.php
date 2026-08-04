<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentsTemplateExport;
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
        // Check active borrowings
        if ($student->activeBorrowings()->count() > 0) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Siswa tidak dapat dihapus karena masih memiliki peminjaman aktif.');
        }

        try {
            $student->delete();
            
            return redirect()->route('admin.students.index')
                ->with('success', 'Siswa berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Siswa tidak dapat dihapus karena masih terkait dengan data lain (peminjaman/kunjungan).');
        }
    }

    /**
     * Import students from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $import = new \App\Imports\StudentsImport($file->getRealPath());
            \Maatwebsite\Excel\Facades\Excel::import($import, $file);

            $msg = "Import berhasil! {$import->getImportedCount()} siswa baru ditambahkan.";
            if ($import->getUpdatedCount() > 0) {
                $msg .= " {$import->getUpdatedCount()} siswa diperbarui.";
            }
            if ($import->getSkippedCount() > 0) {
                $msg .= " {$import->getSkippedCount()} baris di-skip.";
            }

            // Report validation failures
            $failures = $import->failures();
            if ($failures->isNotEmpty()) {
                $failCount = $failures->count();
                $firstError = $failures->first()->errors()[0] ?? 'Unknown';
                $msg .= " {$failCount} baris gagal validasi (contoh: {$firstError}).";
            }

            // If nothing was imported, show a warning
            if ($import->getImportedCount() === 0 && $import->getUpdatedCount() === 0) {
                return redirect()->route('admin.students.index')
                    ->with('error', 'Tidak ada siswa yang berhasil di-import. Pastikan file menggunakan header yang benar (NIS, Nama, Kelas, dll). Download template untuk referensi.');
            }

            return redirect()->route('admin.students.index')->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Export students to Excel.
     */
    public function export(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StudentsExport($request->grade),
            'data-siswa-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Download import template (XLSX, kolom identik dengan export).
     */
    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new StudentsTemplateExport(),
            'template-import-siswa.xlsx'
        );
    }
}
