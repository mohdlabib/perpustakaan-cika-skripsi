<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BorrowingsTemplateExport;
use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Student;
use App\Exports\BorrowingsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

        // Search by student name, teacher name, or book title
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->whereHas('student', function ($sq) use ($term) {
                    $sq->where('name', 'like', "%{$term}%")
                       ->orWhere('nis', 'like', "%{$term}%");
                })->orWhereHas('book', function ($bq) use ($term) {
                    $bq->where('title', 'like', "%{$term}%");
                })->orWhere('borrower_name', 'like', "%{$term}%");
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
        $students = Student::whereHas('grade', fn($q) => $q->where('is_active', true))
            ->orderBy('name')->get()->map(function($s) {
                return ['nis' => $s->nis, 'name' => $s->name, 'class' => $s->class];
            })->values();
        
        $books = Book::available()->orderBy('title')->get()->map(function($b) {
            return ['id' => $b->id, 'title' => $b->title, 'author' => $b->author, 'available_stock' => $b->available_stock];
        })->values();
        
        return view('admin.borrowings.create', compact('students', 'books'));
    }

    /**
     * Store new borrowing (supports student and teacher).
     */
    public function store(Request $request)
    {
        $borrowerType = $request->input('borrower_type', 'student');

        // Common validation
        $rules = [
            'borrower_type' => 'required|in:student,teacher',
            'book_id' => 'required|exists:books,id',
            'due_date' => 'required|date|after:today',
        ];

        // Type-specific validation
        if ($borrowerType === 'teacher') {
            $rules['borrower_name'] = 'required|string|max:255';
            $rules['borrower_info'] = 'nullable|string|max:255';
        } else {
            $rules['student_nis'] = 'required|exists:students_registry,nis';
        }

        $validated = $request->validate($rules);

        $book = Book::find($validated['book_id']);

        // Find an available copy (condition='baik', is_available=true, not currently borrowed)
        $availableCopy = $book->copies()
            ->where('condition', 'baik')
            ->where('is_available', true)
            ->whereDoesntHave('borrowings', fn($q) => $q->where('status', 'borrowed'))
            ->first();

        if (!$availableCopy) {
            return back()->withErrors(['book_id' => 'Stok buku habis, tidak dapat dipinjam.'])->withInput();
        }

        // Student-specific checks
        if ($borrowerType === 'student') {
            $student = Student::find($validated['student_nis']);

            if (!$student->canBorrow()) {
                return back()->withErrors(['student_nis' => 'Siswa sudah meminjam maksimal 3 buku.'])->withInput();
            }
        }

        $borrowingData = [
            'borrower_type' => $borrowerType,
            'book_id' => $validated['book_id'],
            'book_copy_id' => $availableCopy->id,
            'borrow_date' => now(),
            'due_date' => $validated['due_date'],
            'status' => 'borrowed',
        ];

        if ($borrowerType === 'teacher') {
            $borrowingData['borrower_name'] = $validated['borrower_name'];
            $borrowingData['borrower_info'] = $validated['borrower_info'] ?? null;
        } else {
            $borrowingData['student_nis'] = $validated['student_nis'];
        }

        Borrowing::create($borrowingData);

        // Mark the copy as unavailable
        $availableCopy->update(['is_available' => false]);

        return redirect()->route('admin.borrowings.index')
            ->with('success', 'Peminjaman berhasil dicatat.');
    }

    /**
     * Approve borrowing request.
     */
    public function approve(Request $request, Borrowing $borrowing)
    {
        $request->validate([
            'due_date' => 'required|date|after:today',
        ]);

        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
        }

        // Find an available copy for this book
        $availableCopy = $borrowing->book->copies()
            ->where('condition', 'baik')
            ->where('is_available', true)
            ->whereDoesntHave('borrowings', fn($q) => $q->where('status', 'borrowed'))
            ->first();

        if (!$availableCopy) {
            return back()->with('error', 'Stok buku habis, tidak dapat disetujui.');
        }

        $borrowing->approve($request->due_date, auth()->id(), $availableCopy);

        return redirect()->route('admin.borrowings.index')
            ->with('success', "Peminjaman oleh {$borrowing->borrower_display_name} berhasil disetujui.");
    }

    /**
     * Reject borrowing request.
     */
    public function reject(Request $request, Borrowing $borrowing)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
        }

        $borrowing->reject($request->reason, auth()->id());

        return redirect()->route('admin.borrowings.index')
            ->with('success', "Peminjaman oleh {$borrowing->borrower_display_name} ditolak.");
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
        $borrowing->load(['student', 'book.category', 'bookCopy']);
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

    /**
     * Import borrowings from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $import = new \App\Imports\BorrowingsImport($file->getRealPath());
            \Maatwebsite\Excel\Facades\Excel::import($import, $file);

            $msg = "Import berhasil! {$import->getImportedCount()} peminjaman ditambahkan.";
            if ($import->getSkippedCount() > 0) {
                $reasons = $import->getSkipReasons();
                $uniqueReasons = array_unique($reasons);
                $reasonSummary = implode('; ', array_slice($uniqueReasons, 0, 3));
                $msg .= " {$import->getSkippedCount()} baris di-skip: {$reasonSummary}";
                if (count($uniqueReasons) > 3) {
                    $msg .= ' (dan ' . (count($uniqueReasons) - 3) . ' alasan lainnya)';
                }
            }

            // Report failures
            $failures = $import->failures();
            if ($failures->isNotEmpty()) {
                $failCount = $failures->count();
                $firstError = $failures->first()->errors()[0] ?? 'Unknown';
                $msg .= " {$failCount} baris gagal validasi (contoh: {$firstError}).";
            }

            return redirect()->route('admin.borrowings.index')->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->route('admin.borrowings.index')
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Download import template (XLSX, kolom identik dengan export).
     */
    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new BorrowingsTemplateExport(),
            'template-import-peminjaman.xlsx'
        );
    }

    /**
     * Export borrowings to Excel.
     */
    public function export(Request $request)
    {
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(
            new BorrowingsExport($status, $startDate, $endDate),
            'data-peminjaman-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
