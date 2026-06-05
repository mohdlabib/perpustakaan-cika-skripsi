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
use PhpOffice\PhpSpreadsheet\IOFactory;

class BooksImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithCustomCsvSettings
{
    use SkipsErrors, SkipsFailures;

    protected $imported = 0;
    protected $updated = 0;
    protected $skipped = 0;
    protected $detectedHeadingRow = 1;

    /**
     * Constructor: auto-detect the heading row from file.
     * Exported files have 4 title rows before the actual header.
     */
    public function __construct(?string $filePath = null)
    {
        if ($filePath && file_exists($filePath)) {
            $this->detectedHeadingRow = $this->detectHeadingRow($filePath);
        }
    }

    /**
     * Auto-detect the heading row by scanning first 10 rows.
     * Looks for known header keywords like 'Judul', 'Pengarang', 'ISBN'.
     */
    protected function detectHeadingRow(string $filePath): int
    {
        try {
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if ($extension === 'csv') {
                return $this->detectHeadingRowCsv($filePath);
            }

            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Scan first 10 rows to find the heading row
            for ($row = 1; $row <= min(10, $sheet->getHighestRow()); $row++) {
                $rowValues = [];
                for ($col = 1; $col <= min(20, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn())); $col++) {
                    $cellValue = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                    if ($cellValue !== null) {
                        $rowValues[] = strtolower(trim((string) $cellValue));
                    }
                }

                // Check if this row contains known header keywords
                $rowText = implode(' ', $rowValues);
                if ($this->isHeaderRow($rowText)) {
                    return $row;
                }
            }

            return 1; // Default to row 1
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Detect heading row for CSV files.
     */
    protected function detectHeadingRowCsv(string $filePath): int
    {
        try {
            $handle = fopen($filePath, 'r');
            if (!$handle) return 1;

            $row = 0;
            while (($data = fgetcsv($handle)) !== false && $row < 10) {
                $row++;
                $rowText = strtolower(implode(' ', array_map('trim', $data)));
                if ($this->isHeaderRow($rowText)) {
                    fclose($handle);
                    return $row;
                }
            }

            fclose($handle);
            return 1;
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Check if a row text contains known header keywords.
     */
    protected function isHeaderRow(string $rowText): bool
    {
        $knownHeaders = ['judul', 'pengarang', 'penulis', 'isbn', 'penerbit', 'kategori', 'title', 'author'];
        $matchCount = 0;

        foreach ($knownHeaders as $header) {
            if (str_contains($rowText, $header)) {
                $matchCount++;
            }
        }

        // At least 2 known headers found = this is a heading row
        return $matchCount >= 2;
    }

    /**
     * Override heading row to use the auto-detected position.
     */
    public function headingRow(): int
    {
        return $this->detectedHeadingRow;
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter' => ',',
        ];
    }

    /**
     * Header alias mapping: maps various possible header names to canonical keys.
     */
    private const HEADER_ALIASES = [
        'judul' => ['judul', 'judul_buku', 'title', 'nama_buku'],
        'pengarang' => ['pengarang', 'penulis', 'author', 'nama_pengarang', 'nama_penulis'],
        'isbn' => ['isbn', 'no_isbn', 'nomor_isbn'],
        'penerbit' => ['penerbit', 'publisher'],
        'tahun_terbit' => ['tahun_terbit', 'tahun', 'year', 'tahun_publikasi'],
        'tempat_terbit' => ['tempat_terbit', 'kota_terbit', 'tempat'],
        'kategori' => ['kategori', 'category', 'jenis', 'jenis_buku'],
        'edisi' => ['edisi', 'cetakan', 'edition'],
        'klasifikasi' => ['klasifikasi', 'classification', 'kode_klasifikasi'],
        'no_panggil' => ['no_panggil', 'nomor_panggil', 'call_number', 'no_panggil'],
        'kode_eksemplar' => ['kode_eksemplar', 'kode_buku', 'item_code', 'kode'],
        'no_inventaris' => ['no_inventaris', 'inventaris', 'inventory_code', 'nomor_inventaris'],
        'rak' => ['rak', 'shelf', 'nama_rak'],
        'kolom' => ['kolom', 'column', 'kolom_rak'],
        'harga' => ['harga', 'price'],
        'kondisi' => ['kondisi', 'condition', 'status_kondisi'],
        'deskripsi_fisik' => ['deskripsi_fisik', 'physical_description'],
        'sinopsis' => ['sinopsis', 'deskripsi', 'description', 'abstrak'],
        'lokasi_rak' => ['lokasi_rak', 'lokasi', 'shelf_location'],
        'tanggal_diterima' => ['tanggal_diterima', 'tgl_diterima', 'received_date'],
        // Export-specific headers (columns from BooksExport)
        'total_eksemplar' => ['total_eksemplar'],
        'tersedia' => ['tersedia'],
        'dipinjam' => ['dipinjam'],
        'rusak_hilang' => ['rusak_hilang'],
    ];

    /**
     * Normalize row keys to handle various header formats using alias mapping.
     * Strips BOM, special characters, and maps aliases to canonical keys.
     */
    private function normalizeRow(array $row): array
    {
        // First, normalize all keys: strip BOM, whitespace, lowercase, replace special chars
        $cleanRow = [];
        foreach ($row as $key => $value) {
            // Strip BOM characters and normalize
            $cleanKey = preg_replace('/[\x{FEFF}\x{200B}]/u', '', $key);
            // Replace special characters including /, \, (, ), :, ;, etc. with underscore
            $cleanKey = str_replace([' ', '-', '.', '/', '\\', '(', ')', ':', ';', ','], '_', strtolower(trim($cleanKey)));
            // Remove consecutive underscores
            $cleanKey = preg_replace('/_+/', '_', $cleanKey);
            $cleanKey = trim($cleanKey, '_');
            $cleanRow[$cleanKey] = $value;
        }

        // Now map aliases to canonical keys
        $mapped = [];
        foreach (self::HEADER_ALIASES as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                if (isset($cleanRow[$alias]) && $cleanRow[$alias] !== null && $cleanRow[$alias] !== '') {
                    $mapped[$canonical] = $cleanRow[$alias];
                    break;
                }
            }
        }

        // Also keep any unmapped keys
        foreach ($cleanRow as $key => $value) {
            if (!isset($mapped[$key])) {
                $mapped[$key] = $value;
            }
        }

        return $mapped;
    }

    public function model(array $row)
    {
        // Normalize row keys for consistency
        $row = $this->normalizeRow($row);

        // Check if we have a title (required field)
        $title = trim($row['judul'] ?? '');
        if (empty($title)) {
            $this->skipped++;
            return null;
        }

        // Find or create category
        $category = null;
        if (!empty($row['kategori'])) {
            $category = Category::firstOrCreate(['name' => trim($row['kategori'])]);
        }

        // Check for existing book by ISBN or title + author combo
        $existingBook = null;
        
        if (!empty($row['isbn'])) {
            $isbn = trim($row['isbn']);
            // Skip placeholder ISBNs
            if ($isbn !== '-' && $isbn !== '0') {
                $existingBook = Book::where('isbn', $isbn)->first();
            }
        }
        
        if (!$existingBook) {
            $author = trim($row['pengarang'] ?? '');
            if (!empty($author) && $author !== '-') {
                $existingBook = Book::where('title', $title)
                    ->where('author', $author)
                    ->first();
            }
        }

        if ($existingBook) {
            // Book exists — add as a new copy (eksemplar)
            $this->createBookCopy($existingBook, $row);
            $this->updated++;
            return null;
        }

        // Parse publication year (handle possible non-numeric values)
        $pubYear = $row['tahun_terbit'] ?? null;
        if ($pubYear !== null && $pubYear !== '-') {
            $pubYear = is_numeric($pubYear) ? (int) $pubYear : null;
        } else {
            $pubYear = null;
        }

        // Create new book
        $book = Book::create([
            'title' => $title,
            'author' => trim($row['pengarang'] ?? '-'),
            'category_id' => $category?->id,
            'isbn' => ($row['isbn'] ?? null) !== '-' ? ($row['isbn'] ?? null) : null,
            'publisher' => ($row['penerbit'] ?? null) !== '-' ? ($row['penerbit'] ?? null) : null,
            'publication_year' => $pubYear,
            'publication_place' => ($row['tempat_terbit'] ?? null) !== '-' ? ($row['tempat_terbit'] ?? null) : null,
            'edition' => ($row['edisi'] ?? null) !== '-' ? ($row['edisi'] ?? null) : null,
            'classification' => ($row['klasifikasi'] ?? null) !== '-' ? ($row['klasifikasi'] ?? null) : null,
            'call_number' => ($row['no_panggil'] ?? null) !== '-' ? ($row['no_panggil'] ?? null) : null,
            'physical_description' => ($row['deskripsi_fisik'] ?? null) !== '-' ? ($row['deskripsi_fisik'] ?? null) : null,
            'description' => ($row['sinopsis'] ?? null) !== '-' ? ($row['sinopsis'] ?? null) : null,
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

        // Parse condition
        $condition = 'baik';
        if (!empty($row['kondisi'])) {
            $kondisi = strtolower(trim($row['kondisi']));
            if (in_array($kondisi, ['baik', 'rusak', 'hilang'])) {
                $condition = $kondisi;
            }
        }

        return BookCopy::create([
            'book_id' => $book->id,
            'copy_code' => $row['kode_eksemplar'] ?? null,
            'inventory_code' => $row['no_inventaris'] ?? null,
            'shelf_id' => $shelfId,
            'shelf_column_id' => $shelfColumnId,
            'shelf_location' => $row['lokasi_rak'] ?? null,
            'condition' => $condition,
            'received_date' => $row['tanggal_diterima'] ?? null,
            'price' => $row['harga'] ?? null,
            'is_available' => $condition === 'baik',
        ]);
    }

    public function rules(): array
    {
        // Use relaxed validation — we handle missing title in model() method
        return [
            '*.judul' => 'nullable|string|max:500',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.judul.max' => 'Judul buku terlalu panjang (maks 500 karakter).',
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
