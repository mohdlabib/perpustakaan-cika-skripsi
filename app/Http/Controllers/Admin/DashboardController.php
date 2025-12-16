<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Student;
use App\Models\Visit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index()
    {
        // Statistics
        $stats = [
            'total_books' => Book::count(),
            'total_students' => Student::count(),
            'active_borrowings' => Borrowing::active()->count(),
            'today_visits' => Visit::today()->count(),
            'overdue_borrowings' => Borrowing::overdue()->count(),
            'available_books' => Book::where('stock', '>', 0)->count(),
        ];

        // Top 3 Most Borrowed Books
        $topBooks = Book::withCount('borrowings')
            ->orderByDesc('borrowings_count')
            ->take(3)
            ->get()
            ->map(function ($book, $index) {
                return [
                    'rank' => $index + 1,
                    'title' => $book->title,
                    'author' => $book->author,
                    'category' => $book->category->name ?? '-',
                    'borrow_count' => $book->borrowings_count,
                    'cover' => $book->cover_image,
                ];
            });

        // Top 3 Most Frequent Visitors
        $topVisitors = Student::withCount('visits')
            ->orderByDesc('visits_count')
            ->take(3)
            ->get()
            ->map(function ($student, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $student->name,
                    'class' => $student->class ?? '-',
                    'nis' => $student->nis,
                    'visit_count' => $student->visits_count,
                ];
            });

        // Monthly borrowing chart data (last 6 months)
        $monthlyBorrowings = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Borrowing::whereYear('borrow_date', $date->year)
                ->whereMonth('borrow_date', $date->month)
                ->count();
            $monthlyBorrowings->push([
                'month' => $date->format('M Y'),
                'count' => $count,
            ]);
        }

        // Monthly visits chart data (last 6 months)
        $monthlyVisits = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Visit::whereYear('visited_at', $date->year)
                ->whereMonth('visited_at', $date->month)
                ->count();
            $monthlyVisits->push([
                'month' => $date->format('M Y'),
                'count' => $count,
            ]);
        }

        // Recent borrowings
        $recentBorrowings = Borrowing::with(['student', 'book'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'topBooks',
            'topVisitors',
            'monthlyBorrowings',
            'monthlyVisits',
            'recentBorrowings'
        ));
    }

    /**
     * Get chart data via AJAX.
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type', 'borrowings');
        $period = $request->get('period', 6);

        $data = collect();
        for ($i = $period - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            
            if ($type === 'borrowings') {
                $count = Borrowing::whereYear('borrow_date', $date->year)
                    ->whereMonth('borrow_date', $date->month)
                    ->count();
            } else {
                $count = Visit::whereYear('visited_at', $date->year)
                    ->whereMonth('visited_at', $date->month)
                    ->count();
            }
            
            $data->push([
                'label' => $date->format('M'),
                'value' => $count,
            ]);
        }

        return response()->json($data);
    }
}
