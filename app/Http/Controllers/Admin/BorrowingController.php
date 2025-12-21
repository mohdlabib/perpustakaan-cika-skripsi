<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Book;
use App\Models\Student;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    /**
     * Display borrowings list.
     */
    public function index(Request $request)
    {
        $query = Borrowing::with(['student', 'book']);

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->overdue();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Search by student name or book title
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->whereHas('student', function ($sq) use ($term) {
                    $sq->where('name', 'like', "%{$term}%")
                       ->orWhere('nis', 'like', "%{$term}%");
                })->orWhereHas('book', function ($bq) use ($term) {
                    $bq->where('title', 'like', "%{$term}%");
                });
            });
        }

        $borrowings = $query->latest()->paginate(15);

        return view('admin.borrowings.index', compact('borrowings'));
    }

    /**
     * Show create borrowing form.
     */
    public function create()
    {
        $students = Student::orderBy('name')->get();
        $books = Book::available()->orderBy('title')->get();
        
        return view('admin.borrowings.create', compact('students', 'books'));
    }

    /**
     * Store new borrowing.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_nis' => 'required|exists:students_registry,nis',
            'book_id' => 'required|exists:books,id',
            'due_date' => 'required|date|after:today',
        ]);

        $student = Student::find($validated['student_nis']);
        $book = Book::find($validated['book_id']);

        // Check if student can borrow
        if (!$student->canBorrow()) {
            return back()->withErrors(['student_nis' => 'Siswa sudah meminjam maksimal 3 buku.']);
        }

        // Check if book is available
        if (!$book->is_available) {
            return back()->withErrors(['book_id' => 'Buku tidak tersedia.']);
        }

        Borrowing::create([
            'student_nis' => $validated['student_nis'],
            'book_id' => $validated['book_id'],
            'borrow_date' => now(),
            'due_date' => $validated['due_date'],
            'status' => 'borrowed',
        ]);

        return redirect()->route('admin.borrowings.index')
            ->with('success', 'Peminjaman berhasil dicatat.');
    }

    /**
     * Mark borrowing as returned.
     */
    public function returnBook(Borrowing $borrowing)
    {
        $borrowing->markAsReturned();

        return redirect()->route('admin.borrowings.index')
            ->with('success', 'Buku berhasil dikembalikan.');
    }

    /**
     * Show borrowing details.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['student', 'book.category']);
        return view('admin.borrowings.show', compact('borrowing'));
    }

    /**
     * Search recommendations for autocomplete.
     */
    public function searchRecommendations(Request $request)
    {
        $q = $request->get('q', '');
        
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $recommendations = [];

        // Search students
        $students = Student::where('name', 'like', "%{$q}%")
            ->orWhere('nis', 'like', "%{$q}%")
            ->limit(5)
            ->get();

        foreach ($students as $student) {
            $recommendations[] = [
                'id' => 'student_' . $student->nis,
                'type' => 'student',
                'name' => $student->name,
                'sub' => 'NIS: ' . $student->nis,
            ];
        }

        // Search books
        $books = Book::where('title', 'like', "%{$q}%")
            ->orWhere('author', 'like', "%{$q}%")
            ->limit(5)
            ->get();

        foreach ($books as $book) {
            $recommendations[] = [
                'id' => 'book_' . $book->id,
                'type' => 'book',
                'name' => $book->title,
                'sub' => $book->author ?? 'Tidak ada penulis',
            ];
        }

        return response()->json($recommendations);
    }
}
