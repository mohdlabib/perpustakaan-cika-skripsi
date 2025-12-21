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

        // Check if student can borrow more books (including pending requests)
        $activeCount = Borrowing::where('student_nis', $nis)
            ->whereIn('status', ['borrowed', 'pending'])
            ->count();
        
        if ($activeCount >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki 3 peminjaman aktif atau menunggu approval.',
            ], 400);
        }

        // Check if book is available
        if (!$book->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, buku ini sedang tidak tersedia.',
            ], 400);
        }

        // Check if student already has pending or borrowed this book
        $alreadyRequested = Borrowing::where('student_nis', $nis)
            ->where('book_id', $book->id)
            ->whereIn('status', ['borrowed', 'pending'])
            ->exists();

        if ($alreadyRequested) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah meminjam atau mengajukan peminjaman buku ini.',
            ], 400);
        }

        // Create borrowing request with pending status
        $borrowing = Borrowing::create([
            'student_nis' => $nis,
            'book_id' => $book->id,
            'borrow_date' => null, // Will be set when approved
            'due_date' => null, // Will be set by admin
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Permintaan peminjaman buku '{$book->title}' berhasil diajukan. Menunggu persetujuan admin.",
            'borrowing' => [
                'id' => $borrowing->id,
                'book_title' => $book->title,
                'status' => 'pending',
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
