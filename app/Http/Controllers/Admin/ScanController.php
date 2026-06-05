<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Student;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        return view('admin.scan.index');
    }
    
    public function process(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'type' => 'required|in:book,student,return',
        ]);
        
        $code = trim($request->code);
        $type = $request->type;
        
        if ($type === 'book') {
            // Search book by ISBN first
            $book = Book::where('isbn', $code)->first();
            
            // If not found by ISBN, search by copy_code in book_copies
            if (!$book) {
                $copy = BookCopy::where('copy_code', $code)
                    ->orWhere('inventory_code', $code)
                    ->first();
                if ($copy) {
                    $book = $copy->book;
                }
            }
            
            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku tidak ditemukan dengan kode: ' . $code,
                ]);
            }
            
            return response()->json([
                'success' => true,
                'type' => 'book',
                'data' => [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'stock' => $book->stock,
                    'available' => $book->available_stock,
                    'category' => $book->category->name ?? '-',
                ],
            ]);
        }
        
        if ($type === 'student') {
            // Search student by NIS
            $student = Student::find($code);
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan dengan NIS: ' . $code,
                ]);
            }
            
            $activeBorrowings = Borrowing::with('book')
                ->where('student_nis', $student->nis)
                ->where('status', 'borrowed')
                ->get();
            
            return response()->json([
                'success' => true,
                'type' => 'student',
                'data' => [
                    'nis' => $student->nis,
                    'name' => $student->name,
                    'class' => $student->class,
                    'grade' => $student->grade->name ?? '-',
                    'can_borrow' => $student->canBorrow(),
                    'active_borrowings' => $activeBorrowings->map(fn($b) => [
                        'id' => $b->id,
                        'book_title' => $b->book->title ?? '-',
                        'due_date' => $b->due_date->format('d M Y'),
                        'is_overdue' => $b->is_overdue,
                    ]),
                ],
            ]);
        }
        
        if ($type === 'return') {
            // Find active borrowing by book ISBN or copy_code
            $book = Book::where('isbn', $code)->first();
            $bookCopy = null;
            
            if (!$book) {
                $bookCopy = BookCopy::where('copy_code', $code)
                    ->orWhere('inventory_code', $code)
                    ->first();
                if ($bookCopy) {
                    $book = $bookCopy->book;
                }
            }
            
            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku tidak ditemukan dengan kode: ' . $code,
                ]);
            }
            
            // Find the active borrowing - prefer matching by book_copy_id if we found a specific copy
            $borrowingQuery = Borrowing::with('student')
                ->where('status', 'borrowed');
            
            if ($bookCopy) {
                $borrowingQuery->where('book_copy_id', $bookCopy->id);
            } else {
                $borrowingQuery->where('book_id', $book->id);
            }
            
            $borrowing = $borrowingQuery->first();
            
            if (!$borrowing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku ini tidak sedang dipinjam.',
                ]);
            }
            
            // Process return via model method (handles BookCopy release)
            $borrowing->markAsReturned();
            
            $borrowerName = $borrowing->borrower_type === 'teacher' 
                ? ($borrowing->borrower_name ?? 'Guru') 
                : ($borrowing->student->name ?? '-');
            
            return response()->json([
                'success' => true,
                'type' => 'return',
                'message' => "Buku '{$book->title}' berhasil dikembalikan oleh {$borrowerName}.",
                'data' => [
                    'book_title' => $book->title,
                    'student_name' => $borrowerName,
                    'borrow_date' => $borrowing->borrow_date?->format('d M Y') ?? '-',
                    'return_date' => now()->format('d M Y'),
                ],
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Tipe scan tidak valid.',
        ]);
    }
}

