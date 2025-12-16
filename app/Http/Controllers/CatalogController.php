<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display the book catalog.
     */
    public function index(Request $request)
    {
        $query = Book::with('category');

        // Search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Availability filter
        if ($request->boolean('available')) {
            $query->available();
        }

        // Sorting
        $sortBy = $request->get('sort', 'title');
        $sortDir = $request->get('dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $books = $query->paginate(12);
        $categories = Category::withCount('books')->get();

        return view('catalog.index', compact('books', 'categories'));
    }

    /**
     * Display a single book.
     */
    public function show(Book $book)
    {
        $book->load('category', 'borrowings.student');
        
        // Get related books from same category
        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->limit(4)
            ->get();

        return view('catalog.show', compact('book', 'relatedBooks'));
    }

    /**
     * Live search endpoint for AJAX.
     */
    public function search(Request $request)
    {
        $term = $request->get('q', '');
        
        $books = Book::with('category')
            ->search($term)
            ->limit(10)
            ->get();

        return response()->json([
            'results' => $books->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'category' => $book->category->name,
                    'available' => $book->is_available,
                    'stock' => $book->available_stock,
                    'cover' => $book->cover_image,
                ];
            }),
        ]);
    }
}
