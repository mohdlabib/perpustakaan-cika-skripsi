<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Student;
use App\Models\Borrowing;
use App\Models\Category;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function books(Request $request)
    {
        $query = Book::with('category');
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        $books = $query->with('borrowings')->orderBy('title')->paginate(15);
        $categories = Category::orderBy('name')->get();
        
        // Statistics
        $stats = [
            'total' => Book::count(),
            'available' => Book::sum('stock'),
            'borrowed' => Borrowing::active()->count(),
            'categories' => Category::count(),
        ];
        
        // Monthly data for chart
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $date->translatedFormat('M Y'),
                'borrowed' => Borrowing::whereMonth('borrow_date', $date->month)
                    ->whereYear('borrow_date', $date->year)
                    ->count(),
                'returned' => Borrowing::whereMonth('return_date', $date->month)
                    ->whereYear('return_date', $date->year)
                    ->whereNotNull('return_date')
                    ->count(),
            ];
        }
        
        return view('admin.reports.books', compact('books', 'categories', 'stats', 'monthlyData'));
    }
    
    public function students(Request $request)
    {
        $query = Student::with(['grade', 'borrowings']);
        
        if ($request->filled('grade')) {
            $query->where('grade_id', $request->grade);
        }
        
        $students = $query->orderBy('name')->paginate(15);
        $grades = \App\Models\Grade::orderBy('name')->get();
        
        // Statistics
        $stats = [
            'total' => Student::count(),
            'active_borrowers' => Borrowing::active()->distinct('student_nis')->count(),
            'total_borrowings' => Borrowing::count(),
            'overdue' => Borrowing::overdue()->count(),
        ];
        
        return view('admin.reports.students', compact('students', 'grades', 'stats'));
    }
    
    public function exportBooks(Request $request)
    {
        $category = $request->input('category');
        $filename = 'Laporan-Buku-Perpustakaan-SMAN8-' . date('Y-m-d') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\BooksExport($category),
            $filename
        );
    }
    
    public function exportStudents(Request $request)
    {
        $grade = $request->input('grade');
        $filename = 'Laporan-Siswa-Perpustakaan-SMAN8-' . date('Y-m-d') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StudentsExport($grade),
            $filename
        );
    }
}
