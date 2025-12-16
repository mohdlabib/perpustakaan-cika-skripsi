<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
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
        
        $code = $request->code;
        $type = $request->type;
        
        if ($type === 'book') {
            // Search book by ISBN or item_code
            $book = Book::where('isbn', $code)
                ->orWhere('item_code', $code)
                ->first();
            
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
            // Find active borrowing by book ISBN/code
            $book = Book::where('isbn', $code)
                ->orWhere('item_code', $code)
                ->first();
            
            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku tidak ditemukan dengan kode: ' . $code,
                ]);
            }
            
            $borrowing = Borrowing::with('student')
                ->where('book_id', $book->id)
                ->where('status', 'borrowed')
                ->first();
            
            if (!$borrowing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku ini tidak sedang dipinjam.',
                ]);
            }
            
            // Process return
            $borrowing->update([
                'status' => 'returned',
                'return_date' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'type' => 'return',
                'message' => "Buku '{$book->title}' berhasil dikembalikan oleh {$borrowing->student->name}.",
                'data' => [
                    'book_title' => $book->title,
                    'student_name' => $borrowing->student->name ?? '-',
                    'borrow_date' => $borrowing->borrow_date->format('d M Y'),
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
