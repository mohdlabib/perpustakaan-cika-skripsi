<?php

namespace App\Http\Controllers\Admin;

use App\Exports\VisitorsTemplateExport;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Student;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Visit;
use App\Models\Grade;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function books(Request $request)
    {
        $query = Book::with(['category', 'copies']);
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        $books = $query->with('borrowings')->orderBy('title')->paginate(15);
        $categories = Category::orderBy('name')->get();
        
        // Statistics
        $totalCopies = BookCopy::where('condition', '!=', 'hilang')->count();
        $stats = [
            'total' => Book::count(),
            'total_copies' => $totalCopies,
            'available' => $totalCopies - Borrowing::active()->count(),
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
    
    public function visitors(Request $request)
    {
        // Build query with filters
        $query = Visit::with('student.grade');
        
        // Date range filter
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)->startOfDay() 
            : now()->startOfMonth();
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : now()->endOfDay();
        
        $query->whereBetween('visited_at', [$startDate, $endDate]);
        
        // Keyword search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('visitor_name', 'like', "%{$search}%")
                  ->orWhere('student_nis', 'like', "%{$search}%")
                  ->orWhere('visitor_detail', 'like', "%{$search}%");
            });
        }
        
        // Get paginated visits
        $visits = $query->orderBy('visited_at', 'desc')->paginate(15);
        
        // Statistics
        $stats = [
            'today' => Visit::today()->count(),
            'this_week' => Visit::thisWeek()->count(),
            'this_month' => Visit::thisMonth()->count(),
            'total' => Visit::count(),
        ];
        
        // Daily data for chart (last 30 days)
        $dailyData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyData[] = [
                'date' => $date->format('d M'),
                'count' => Visit::whereDate('visited_at', $date->toDateString())->count(),
            ];
        }
        
        // Monthly data for chart (last 12 months)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $date->translatedFormat('M Y'),
                'count' => Visit::whereMonth('visited_at', $date->month)
                    ->whereYear('visited_at', $date->year)
                    ->count(),
            ];
        }
        
        // Top 5 visitors this month
        $topVisitors = Student::withCount(['visits' => function($q) {
                $q->whereMonth('visited_at', now()->month)
                  ->whereYear('visited_at', now()->year);
            }])
            ->having('visits_count', '>', 0)
            ->orderBy('visits_count', 'desc')
            ->limit(5)
            ->get();
        
        // Visits by grade
        $visitsByGrade = Grade::withCount(['students as visits_count' => function($q) use ($startDate, $endDate) {
                $q->join('visits', 'students_registry.nis', '=', 'visits.student_nis')
                  ->whereBetween('visits.visited_at', [$startDate, $endDate]);
            }])
            ->orderBy('name')
            ->get();
        
        // Peak hours (group by hour)
        $peakHours = Visit::selectRaw('HOUR(visited_at) as hour, COUNT(*) as count')
            ->whereMonth('visited_at', now()->month)
            ->whereYear('visited_at', now()->year)
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'hour' => sprintf('%02d:00 - %02d:59', $item->hour, $item->hour),
                    'count' => $item->count,
                ];
            });
        
        // Get grades for filter
        $grades = Grade::orderBy('name')->get();
        
        return view('admin.reports.visitors', compact(
            'visits', 
            'stats', 
            'dailyData', 
            'monthlyData', 
            'topVisitors', 
            'visitsByGrade',
            'peakHours',
            'grades',
            'startDate',
            'endDate'
        ));
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
    
    public function exportVisitors(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $search = $request->input('search');
        
        $filename = 'Laporan-Pengunjung-Perpustakaan-SMAN8-' . date('Y-m-d') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\VisitorsExport($startDate, $endDate, $search),
            $filename
        );
    }

    /**
     * Import visitors from Excel.
     */
    public function importVisitors(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $import = new \App\Imports\VisitsImport($file->getRealPath());
            \Maatwebsite\Excel\Facades\Excel::import($import, $file);

            $msg = "Import berhasil! {$import->getImportedCount()} kunjungan ditambahkan.";
            if ($import->getSkippedCount() > 0) {
                $msg .= " {$import->getSkippedCount()} baris di-skip.";
            }

            return redirect()->route('admin.reports.visitors')->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->route('admin.reports.visitors')
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Download visitors import template (XLSX, kolom identik dengan export).
     */
    public function downloadVisitorsTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new VisitorsTemplateExport(),
            'template-import-kunjungan.xlsx'
        );
    }
}
