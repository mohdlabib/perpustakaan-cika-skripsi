<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category')->latest();
        
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        $books = $query->paginate(15);
        $categories = Category::orderBy('name')->get();
        
        return view('admin.books.index', compact('books', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'isbn' => 'nullable|string|max:50',
            'item_code' => 'nullable|string|max:50',
            'edition' => 'nullable|string|max:100',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'publication_place' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'physical_description' => 'nullable|string|max:255',
            'classification' => 'nullable|string|max:100',
            'call_number' => 'nullable|string|max:100',
            'inventory_code' => 'nullable|string|max:100',
            'shelf_location' => 'nullable|string|max:50',
            'received_date' => 'nullable|date',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('book-covers', 'public');
        }
        
        Book::create($validated);
        
        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'isbn' => 'nullable|string|max:50',
            'item_code' => 'nullable|string|max:50',
            'edition' => 'nullable|string|max:100',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'publication_place' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'physical_description' => 'nullable|string|max:255',
            'classification' => 'nullable|string|max:100',
            'call_number' => 'nullable|string|max:100',
            'inventory_code' => 'nullable|string|max:100',
            'shelf_location' => 'nullable|string|max:50',
            'received_date' => 'nullable|date',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        if ($request->hasFile('cover_image')) {
            // Delete old image
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('book-covers', 'public');
        }
        
        $book->update($validated);
        
        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        
        $book->delete();
        
        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $category = $request->input('category');
        $filename = 'Laporan-Buku-Perpustakaan-SMAN8-' . date('Y-m-d') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\BooksExport($category),
            $filename
        );
    }
}
