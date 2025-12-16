<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Student;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    /**
     * Request to borrow a book.
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        // Check if student is logged in
        $nis = session('student_nis');
        if (!$nis) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }

        $student = Student::find($nis);
        $book = Book::findOrFail($request->book_id);

        // Check if student can borrow more books
        if (!$student->canBorrow()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah meminjam maksimal 3 buku. Kembalikan buku terlebih dahulu.',
            ], 400);
        }

        // Check if book is available
        if (!$book->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, buku ini sedang tidak tersedia.',
            ], 400);
        }

        // Check if student already borrowed this book
        $alreadyBorrowed = Borrowing::where('student_nis', $nis)
            ->where('book_id', $book->id)
            ->where('status', 'borrowed')
            ->exists();

        if ($alreadyBorrowed) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah meminjam buku ini.',
            ], 400);
        }

        // Create borrowing record
        $borrowing = Borrowing::create([
            'student_nis' => $nis,
            'book_id' => $book->id,
            'borrow_date' => now(),
            'due_date' => now()->addDays(7), // 1 week loan period
            'status' => 'borrowed',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil meminjam buku '{$book->title}'. Harap kembalikan sebelum {$borrowing->due_date->format('d M Y')}.",
            'borrowing' => [
                'id' => $borrowing->id,
                'book_title' => $book->title,
                'due_date' => $borrowing->due_date->format('d M Y'),
            ],
        ]);
    }

    /**
     * Get student's active borrowings.
     */
    public function myBooks()
    {
        $nis = session('student_nis');
        if (!$nis) {
            return redirect()->route('student.login');
        }

        $borrowings = Borrowing::with('book.category')
            ->where('student_nis', $nis)
            ->orderByDesc('borrow_date')
            ->get();

        return view('borrowings.my-books', compact('borrowings'));
    }
}
