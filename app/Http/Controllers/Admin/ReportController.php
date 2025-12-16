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
    
    public function exportBooks()
    {
        $books = Book::with('category')->orderBy('title')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-buku-' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($books) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            fputcsv($file, ['No', 'Judul', 'Pengarang', 'Kategori', 'ISBN', 'Penerbit', 'Tahun', 'Stok', 'Dipinjam', 'Tersedia']);
            
            foreach ($books as $index => $book) {
                $borrowed = $book->borrowings()->where('status', 'borrowed')->count();
                fputcsv($file, [
                    $index + 1,
                    $book->title,
                    $book->author,
                    $book->category->name ?? '-',
                    $book->isbn ?? '-',
                    $book->publisher ?? '-',
                    $book->publication_year ?? '-',
                    $book->stock,
                    $borrowed,
                    $book->stock - $borrowed,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    public function exportStudents()
    {
        $students = Student::with(['grade', 'borrowings'])->orderBy('name')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-siswa-' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            fputcsv($file, ['No', 'NIS', 'Nama', 'Kelas', 'Angkatan', 'Telepon', 'Total Peminjaman', 'Sedang Dipinjam']);
            
            foreach ($students as $index => $student) {
                $activeBorrowings = $student->borrowings->where('status', 'borrowed')->count();
                fputcsv($file, [
                    $index + 1,
                    $student->nis,
                    $student->name,
                    $student->class ?? '-',
                    $student->grade->name ?? '-',
                    $student->phone ?? '-',
                    $student->borrowings->count(),
                    $activeBorrowings,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
