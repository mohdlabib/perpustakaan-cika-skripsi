<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Shelf;
use App\Models\ShelfColumn;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class BooksImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithCustomCsvSettings
{
    use SkipsErrors, SkipsFailures;

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter' => ',',
        ];
    }

    protected $imported = 0;
    protected $updated = 0;
    protected $skipped = 0;

    /**
     * Normalize row keys to handle various header formats.
     */
    private function normalizeRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            // Normalize: strip whitespace, lowercase, replace spaces with underscore
            $normalizedKey = str_replace(' ', '_', strtolower(trim($key)));
            $normalized[$normalizedKey] = $value;
        }
        return $normalized;
    }

    public function model(array $row)
    {
        // Normalize row keys for consistency
        $row = $this->normalizeRow($row);
        // Find or create category
        $category = null;
        if (!empty($row['kategori'])) {
            $category = Category::firstOrCreate(['name' => trim($row['kategori'])]);
        }

        // Check for existing book by ISBN or title + author combo
        $existingBook = null;
        
        if (!empty($row['isbn'])) {
            $existingBook = Book::where('isbn', trim($row['isbn']))->first();
        }
        
        if (!$existingBook) {
            $existingBook = Book::where('title', trim($row['judul']))
                ->where('author', trim($row['pengarang'] ?? $row['penulis'] ?? ''))
                ->first();
        }

        if ($existingBook) {
            // Book exists — add as a new copy (eksemplar)
            $this->createBookCopy($existingBook, $row);
            $this->updated++;
            return null;
        }

        // Create new book
        $book = Book::create([
            'title' => trim($row['judul']),
            'author' => trim($row['pengarang'] ?? $row['penulis'] ?? '-'),
            'category_id' => $category?->id,
            'isbn' => $row['isbn'] ?? null,
            'publisher' => $row['penerbit'] ?? null,
            'publication_year' => $row['tahun_terbit'] ?? $row['tahun'] ?? null,
            'publication_place' => $row['tempat_terbit'] ?? null,
            'edition' => $row['edisi'] ?? $row['cetakan'] ?? null,
            'classification' => $row['klasifikasi'] ?? null,
            'call_number' => $row['no_panggil'] ?? $row['nomor_panggil'] ?? null,
            'physical_description' => $row['deskripsi_fisik'] ?? null,
            'description' => $row['sinopsis'] ?? $row['deskripsi'] ?? null,
        ]);

        // Create initial copy
        $this->createBookCopy($book, $row);

        $this->imported++;
        return null; // We already created the book manually
    }

    /**
     * Create a BookCopy from import row data.
     */
    protected function createBookCopy(Book $book, array $row): BookCopy
    {
        // Find shelf if specified
        $shelfId = null;
        $shelfColumnId = null;
        
        if (!empty($row['rak'])) {
            $shelf = Shelf::where('name', 'like', '%' . trim($row['rak']) . '%')->first();
            if ($shelf) {
                $shelfId = $shelf->id;
                
                if (!empty($row['kolom'])) {
                    $column = ShelfColumn::where('shelf_id', $shelf->id)
                        ->where('name', 'like', '%' . trim($row['kolom']) . '%')
                        ->first();
                    $shelfColumnId = $column?->id;
                }
            }
        }

        return BookCopy::create([
            'book_id' => $book->id,
            'copy_code' => $row['kode_eksemplar'] ?? $row['kode_buku'] ?? null,
            'inventory_code' => $row['no_inventaris'] ?? $row['inventaris'] ?? null,
            'shelf_id' => $shelfId,
            'shelf_column_id' => $shelfColumnId,
            'shelf_location' => $row['lokasi_rak'] ?? null,
            'condition' => $row['kondisi'] ?? 'baik',
            'received_date' => $row['tanggal_diterima'] ?? null,
            'price' => $row['harga'] ?? null,
            'is_available' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:500',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'judul.required' => 'Kolom Judul wajib diisi.',
        ];
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getUpdatedCount(): int
    {
        return $this->updated;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }
}
