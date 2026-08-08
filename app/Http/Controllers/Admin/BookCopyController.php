<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Shelf;
use App\Models\ShelfColumn;
use App\Exports\BookCopiesExport;
use App\Imports\BookCopiesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
            'copy_code'      => 'nullable|string|max:50',
            'inventory_code' => 'nullable|string|max:50',
            'shelf_id'       => 'nullable|exists:shelves,id',
            'shelf_column_id'=> 'nullable|exists:shelf_columns,id',
            'condition'      => 'required|in:baik,rusak,hilang',
            'received_date'  => 'nullable|date',
            'price'          => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string|max:500',
        ]);

        $shelfLocation = null;
        if ($validated['shelf_id'] ?? null) {
            $shelf = Shelf::find($validated['shelf_id']);
            $shelfLocation = $shelf ? $shelf->code : null;
        }

        $book->copies()->create([
            ...$validated,
            'shelf_location' => $shelfLocation,
            'is_available'   => $validated['condition'] === 'baik',
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
            'copy_code'      => 'nullable|string|max:50',
            'inventory_code' => 'nullable|string|max:50',
            'shelf_id'       => 'nullable|exists:shelves,id',
            'shelf_column_id'=> 'nullable|exists:shelf_columns,id',
            'condition'      => 'required|in:baik,rusak,hilang',
            'received_date'  => 'nullable|date',
            'price'          => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string|max:500',
        ]);

        $shelfLocation = null;
        if ($validated['shelf_id'] ?? null) {
            $shelf = Shelf::find($validated['shelf_id']);
            $shelfLocation = $shelf ? $shelf->code : null;
        }

        $copy->update([
            ...$validated,
            'shelf_location' => $shelfLocation,
            'is_available'   => $validated['condition'] === 'baik',
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
     * Import eksemplar dari Excel untuk buku ini.
     */
    public function importCopies(Request $request, Book $book)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $file   = $request->file('file');
            // Store to disk first so getRealPath() is stable for heading detection
            $storedPath = $file->store('imports/book-copies', 'local');
            $fullPath   = \Illuminate\Support\Facades\Storage::disk('local')->path($storedPath);

            $import = new BookCopiesImport($book->id, $fullPath);
            Excel::import($import, $storedPath, 'local');

            $msg = "Import eksemplar berhasil! {$import->getImportedCount()} eksemplar ditambahkan.";
            if ($import->getSkippedCount() > 0) {
                $msg .= " {$import->getSkippedCount()} baris di-skip (duplikat atau data tidak valid).";
            }

            \Illuminate\Support\Facades\Storage::disk('local')->delete($storedPath);

            return redirect()->route('admin.books.show', $book)->with('success', $msg);
        } catch (\Exception $e) {
            if (isset($storedPath)) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($storedPath);
            }
            return redirect()->route('admin.books.show', $book)
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Export daftar eksemplar buku ini sebagai Excel (bisa digunakan sebagai template import).
     */
    public function exportCopies(Book $book)
    {
        return Excel::download(
            new BookCopiesExport($book),
            'eksemplar-' . \Illuminate\Support\Str::slug($book->title) . '-' . now()->format('Y-m-d') . '.xlsx'
        );
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
