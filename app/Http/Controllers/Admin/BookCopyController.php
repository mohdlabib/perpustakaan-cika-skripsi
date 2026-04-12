<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Shelf;
use App\Models\ShelfColumn;
use Illuminate\Http\Request;

class BookCopyController extends Controller
{
    /**
     * Show form to create a new copy for a book.
     */
    public function create(Book $book)
    {
        $shelves = Shelf::active()->with('columns')->get();
        
        return view('admin.book-copies.create', compact('book', 'shelves'));
    }

    /**
     * Store a new copy.
     */
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'copy_code' => 'nullable|string|max:50',
            'inventory_code' => 'nullable|string|max:50',
            'shelf_id' => 'nullable|exists:shelves,id',
            'shelf_column_id' => 'nullable|exists:shelf_columns,id',
            'condition' => 'required|in:baik,rusak,hilang',
            'received_date' => 'nullable|date',
            'price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // Build shelf_location display string
        $shelfLocation = null;
        if ($validated['shelf_id'] ?? null) {
            $shelf = Shelf::find($validated['shelf_id']);
            $shelfLocation = $shelf ? $shelf->code : null;
        }

        $book->copies()->create([
            ...$validated,
            'shelf_location' => $shelfLocation,
            'is_available' => $validated['condition'] === 'baik',
        ]);

        return redirect()->route('admin.books.show', $book)
            ->with('success', 'Eksemplar baru berhasil ditambahkan.');
    }

    /**
     * Show form to edit a copy.
     */
    public function edit(Book $book, BookCopy $copy)
    {
        $shelves = Shelf::active()->with('columns')->get();
        
        return view('admin.book-copies.edit', compact('book', 'copy', 'shelves'));
    }

    /**
     * Update a copy.
     */
    public function update(Request $request, Book $book, BookCopy $copy)
    {
        $validated = $request->validate([
            'copy_code' => 'nullable|string|max:50',
            'inventory_code' => 'nullable|string|max:50',
            'shelf_id' => 'nullable|exists:shelves,id',
            'shelf_column_id' => 'nullable|exists:shelf_columns,id',
            'condition' => 'required|in:baik,rusak,hilang',
            'received_date' => 'nullable|date',
            'price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // Build shelf_location display string
        $shelfLocation = null;
        if ($validated['shelf_id'] ?? null) {
            $shelf = Shelf::find($validated['shelf_id']);
            $shelfLocation = $shelf ? $shelf->code : null;
        }

        $copy->update([
            ...$validated,
            'shelf_location' => $shelfLocation,
            'is_available' => $validated['condition'] === 'baik',
        ]);

        return redirect()->route('admin.books.show', $book)
            ->with('success', 'Eksemplar berhasil diperbarui.');
    }

    /**
     * Delete a copy.
     */
    public function destroy(Book $book, BookCopy $copy)
    {
        if ($copy->borrowings()->where('status', 'borrowed')->exists()) {
            return redirect()->route('admin.books.show', $book)
                ->with('error', 'Eksemplar tidak dapat dihapus karena sedang dipinjam.');
        }

        try {
            $copy->delete();
            return redirect()->route('admin.books.show', $book)
                ->with('success', 'Eksemplar berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.books.show', $book)
                ->with('error', 'Eksemplar tidak dapat dihapus karena terkait data lain.');
        }
    }

    /**
     * Get shelf columns for AJAX.
     */
    public function getColumns(Shelf $shelf)
    {
        return response()->json(
            $shelf->columns()->active()->get(['id', 'name'])
        );
    }
}
