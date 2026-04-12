<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Shelf;
use App\Exports\BooksExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['category', 'copies']);

        if ($request->search) {
            $query->search($request->search);
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        $books = $query->latest()->paginate(15);
        $categories = Category::orderBy('name')->get();

        return view('admin.books.index', compact('books', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $shelves = Shelf::active()->with('columns')->get();

        return view('admin.books.create', compact('categories', 'shelves'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'nullable|string|max:20',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'edition' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            'publication_place' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'physical_description' => 'nullable|string|max:255',
            'classification' => 'nullable|string|max:50',
            'call_number' => 'nullable|string|max:50',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // First copy data
            'copy_code' => 'nullable|string|max:50',
            'inventory_code' => 'nullable|string|max:50',
            'shelf_id' => 'nullable|exists:shelves,id',
            'shelf_column_id' => 'nullable|exists:shelf_columns,id',
            'received_date' => 'nullable|date',
            'price' => 'nullable|numeric|min:0',
            'initial_copies' => 'nullable|integer|min:1|max:100',
        ]);

        // Handle cover image
        $bookData = collect($validated)->except([
            'copy_code', 'inventory_code', 'shelf_id', 'shelf_column_id',
            'received_date', 'price', 'initial_copies', 'cover_image'
        ])->toArray();

        if ($request->hasFile('cover_image')) {
            $bookData['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        $book = Book::create($bookData);

        // Create initial copies
        $numCopies = max(1, $validated['initial_copies'] ?? 1);
        $shelfLocation = null;
        if ($validated['shelf_id'] ?? null) {
            $shelf = Shelf::find($validated['shelf_id']);
            $shelfLocation = $shelf ? $shelf->code : null;
        }

        for ($i = 0; $i < $numCopies; $i++) {
            $book->copies()->create([
                'copy_code' => $i === 0 ? ($validated['copy_code'] ?? null) : null,
                'inventory_code' => $i === 0 ? ($validated['inventory_code'] ?? null) : null,
                'shelf_id' => $validated['shelf_id'] ?? null,
                'shelf_column_id' => $validated['shelf_column_id'] ?? null,
                'shelf_location' => $shelfLocation,
                'condition' => 'baik',
                'received_date' => $i === 0 ? ($validated['received_date'] ?? null) : null,
                'price' => $i === 0 ? ($validated['price'] ?? null) : null,
                'is_available' => true,
            ]);
        }

        return redirect()->route('admin.books.index')
            ->with('success', "Buku berhasil ditambahkan dengan {$numCopies} eksemplar.");
    }

    public function show(Book $book)
    {
        $book->load(['category', 'copies.shelf', 'copies.shelfColumn']);
        $copies = $book->copies()->latest()->paginate(20);

        return view('admin.books.show', compact('book', 'copies'));
    }

    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'isbn' => 'nullable|string|max:20',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'edition' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            'publication_place' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'physical_description' => 'nullable|string|max:255',
            'classification' => 'nullable|string|max:50',
            'call_number' => 'nullable|string|max:50',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        $book->update($validated);

        return redirect()->route('admin.books.show', $book)
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->activeBorrowings()->count() > 0) {
            return redirect()->route('admin.books.index')
                ->with('error', 'Buku tidak dapat dihapus karena masih ada peminjaman aktif.');
        }

        try {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $book->delete();

            return redirect()->route('admin.books.index')
                ->with('success', 'Buku berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.books.index')
                ->with('error', 'Buku tidak dapat dihapus karena masih terkait dengan data lain.');
        }
    }

    /**
     * Show book detail via AJAX.
     */
    public function detail(Book $book)
    {
        $book->load(['category', 'copies.shelf', 'copies.shelfColumn']);

        return response()->json([
            'id' => $book->id,
            'isbn' => $book->isbn,
            'title' => $book->title,
            'author' => $book->author,
            'edition' => $book->edition,
            'category' => $book->category,
            'publisher' => $book->publisher,
            'publication_year' => $book->publication_year,
            'publication_place' => $book->publication_place,
            'description' => $book->description,
            'physical_description' => $book->physical_description,
            'classification' => $book->classification,
            'call_number' => $book->call_number,
            'cover_url' => $book->cover_url,
            'stock' => $book->stock,
            'available_stock' => $book->available_stock,
            'copies_count' => $book->copies->count(),
            'copies' => $book->copies->map(fn($c) => [
                'id' => $c->id,
                'copy_code' => $c->copy_code,
                'inventory_code' => $c->inventory_code,
                'shelf_display' => $c->shelf_display,
                'condition' => $c->condition,
                'status' => $c->status,
                'status_color' => $c->status_color,
            ]),
        ]);
    }

    /**
     * Export books to Excel.
     */
    public function export(Request $request)
    {
        return Excel::download(
            new BooksExport($request->category),
            'laporan-buku-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Import books from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $import = new \App\Imports\BooksImport();
            Excel::import($import, $request->file('file'));

            $msg = "Import berhasil! {$import->getImportedCount()} buku baru ditambahkan.";
            if ($import->getUpdatedCount() > 0) {
                $msg .= " {$import->getUpdatedCount()} eksemplar ditambahkan ke buku existing.";
            }
            if ($import->getSkippedCount() > 0) {
                $msg .= " {$import->getSkippedCount()} baris di-skip.";
            }

            return redirect()->route('admin.books.index')->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->route('admin.books.index')
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Judul', 'Pengarang', 'ISBN', 'Penerbit', 'Tahun Terbit', 
            'Tempat Terbit', 'Kategori', 'Edisi', 'Klasifikasi', 'No Panggil',
            'Kode Eksemplar', 'No Inventaris', 'Rak', 'Kolom', 'Harga', 'Kondisi'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $headers);
            // Sample row
            fputcsv($file, [
                'Pemrograman PHP', 'John Doe', '978-xxx-xxx', 'Penerbit ABC', '2024',
                'Jakarta', 'Teknologi', 'Cetakan ke-1', '005.2', '005.2 DOE p',
                'BK-001', 'INV-001', 'Rak A', 'Kolom 1', '50000', 'baik'
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-import-buku.csv"',
        ]);
    }
}

