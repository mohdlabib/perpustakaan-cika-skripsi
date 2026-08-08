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

            for ($row = 1; $row <= min(10, $sheet->getHighestRow()); $row++) {
                $rowValues = [];
                for ($col = 1; $col <= min(20, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn())); $col++) {
                    $cellValue = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                    if ($cellValue !== null) {
                        $rowValues[] = strtolower(trim((string) $cellValue));
                    }
                }

                $rowText = implode(' ', $rowValues);
                if ($this->isHeaderRow($rowText)) {
                    return $row;
                }
            }

            return 1;
        } catch (\Exception $e) {
            return 1;
        }
    }

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

    protected function isHeaderRow(string $rowText): bool
    {
        $knownHeaders = ['judul', 'pengarang', 'penulis', 'isbn', 'penerbit', 'kategori', 'title', 'author'];
        $matchCount = 0;

        foreach ($knownHeaders as $header) {
            if (str_contains($rowText, $header)) {
                $matchCount++;
            }
        }

        return $matchCount >= 2;
    }

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
     * Includes export-format headers like 'Judul Buku', 'No. Panggil', etc.
     */
    private const HEADER_ALIASES = [
        'judul'             => ['judul', 'judul_buku', 'title', 'nama_buku'],
        'pengarang'         => ['pengarang', 'penulis', 'author', 'nama_pengarang', 'nama_penulis'],
        'isbn'              => ['isbn', 'no_isbn', 'nomor_isbn'],
        'penerbit'          => ['penerbit', 'publisher'],
        'tahun_terbit'      => ['tahun_terbit', 'tahun', 'year', 'tahun_publikasi'],
        'tempat_terbit'     => ['tempat_terbit', 'kota_terbit', 'tempat'],
        'kategori'          => ['kategori', 'category', 'jenis', 'jenis_buku'],
        'edisi'             => ['edisi', 'cetakan', 'edition'],
        'klasifikasi'       => ['klasifikasi', 'classification', 'kode_klasifikasi'],
        // "No. Panggil" normalized → "no_panggil"
        'no_panggil'        => ['no_panggil', 'nomor_panggil', 'call_number', 'no_panggil'],
        'kode_eksemplar'    => ['kode_eksemplar', 'kode_buku', 'item_code', 'kode'],
        'no_inventaris'     => ['no_inventaris', 'inventaris', 'inventory_code', 'nomor_inventaris'],
        'rak'               => ['rak', 'shelf', 'nama_rak'],
        'kolom'             => ['kolom', 'column', 'kolom_rak'],
        'harga'             => ['harga', 'price'],
        'kondisi'           => ['kondisi', 'condition', 'status_kondisi'],
        'deskripsi_fisik'   => ['deskripsi_fisik', 'physical_description'],
        'sinopsis'          => ['sinopsis', 'deskripsi', 'description', 'abstrak'],
        'lokasi_rak'        => ['lokasi_rak', 'lokasi', 'shelf_location'],
        'tanggal_diterima'  => ['tanggal_diterima', 'tgl_diterima', 'received_date'],
        // Export-only summary columns — recognized but ignored for BookCopy creation
        'total_eksemplar'   => ['total_eksemplar'],
        'tersedia'          => ['tersedia'],
        'dipinjam'          => ['dipinjam'],
        'rusak_hilang'      => ['rusak_hilang', 'rusak_hilang_'],
    ];

    /**
     * Normalize row keys to handle various header formats using alias mapping.
     * Strips BOM, special characters, and maps aliases to canonical keys.
     */
    private function normalizeRow(array $row): array
    {
        $cleanRow = [];
        foreach ($row as $key => $value) {
            $cleanKey = preg_replace('/[\x{FEFF}\x{200B}]/u', '', (string) $key);
            $cleanKey = str_replace([' ', '-', '.', '/', '\\', '(', ')', ':', ';', ','], '_', strtolower(trim($cleanKey)));
            $cleanKey = preg_replace('/_+/', '_', $cleanKey);
            $cleanKey = trim($cleanKey, '_');
            $cleanRow[$cleanKey] = $value;
        }

        $mapped = [];
        foreach (self::HEADER_ALIASES as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                if (array_key_exists($alias, $cleanRow) && $cleanRow[$alias] !== null && $cleanRow[$alias] !== '') {
                    $mapped[$canonical] = $cleanRow[$alias];
                    break;
                }
            }
        }

        foreach ($cleanRow as $key => $value) {
            if (!isset($mapped[$key])) {
                $mapped[$key] = $value;
            }
        }

        return $mapped;
    }

    /**
     * Check if a row looks like a summary/footer row (not actual data).
     */
    private function isSummaryRow(array $row): bool
    {
        $judul = trim((string) ($row['judul'] ?? ''));

        // Skip rows with "RINGKASAN:", "Total", etc. in the title field
        $summaryKeywords = ['ringkasan', 'total judul', 'total eksemplar', 'total dipinjam', 'total tersedia', 'total rusak'];
        $judulLower = strtolower($judul);
        foreach ($summaryKeywords as $kw) {
            if (str_contains($judulLower, $kw)) {
                return true;
            }
        }

        // If title contains ': ' it's likely a summary row like "Total Judul Buku: 5"
        if (str_contains($judul, ': ')) {
            return true;
        }

        return false;
    }

    public function model(array $row)
    {
        $row = $this->normalizeRow($row);

        $title = trim((string) ($row['judul'] ?? ''));
        if (empty($title)) {
            $this->skipped++;
            return null;
        }

        // Skip summary/footer rows
        if ($this->isSummaryRow($row)) {
            $this->skipped++;
            return null;
        }

        // Find or create category; fallback to "Umum" so category_id is never null
        $categoryName = (!empty($row['kategori']) && trim((string) $row['kategori']) !== '-')
            ? trim((string) $row['kategori'])
            : 'Umum';
        $category = Category::firstOrCreate(['name' => $categoryName]);

        // Check for existing book by ISBN or title + author combo
        $existingBook = null;

        if (!empty($row['isbn'])) {
            $isbn = trim((string) $row['isbn']);
            if ($isbn !== '-' && $isbn !== '0') {
                $existingBook = Book::where('isbn', $isbn)->first();
            }
        }

        if (!$existingBook) {
            $author = trim((string) ($row['pengarang'] ?? ''));
            if (!empty($author) && $author !== '-') {
                $existingBook = Book::where('title', $title)
                    ->where('author', $author)
                    ->first();
            }
        }

        if ($existingBook) {
            // Book exists — only add a new copy if we have copy-specific data
            // Don't create a copy just from export summary columns
            if ($this->hasCopyData($row)) {
                $this->createBookCopy($existingBook, $row);
            }
            $this->updated++;
            return null;
        }

        // Parse publication year
        $pubYear = $row['tahun_terbit'] ?? null;
        if ($pubYear !== null && $pubYear !== '-') {
            $pubYear = is_numeric($pubYear) ? (int) $pubYear : null;
        } else {
            $pubYear = null;
        }

        $book = Book::create([
            'title'                => $title,
            'author'               => trim((string) ($row['pengarang'] ?? '-')),
            'category_id'          => $category?->id,
            'isbn'                 => $this->nullOrValue($row['isbn'] ?? null),
            'publisher'            => $this->nullOrValue($row['penerbit'] ?? null),
            'publication_year'     => $pubYear,
            'publication_place'    => $this->nullOrValue($row['tempat_terbit'] ?? null),
            'edition'              => $this->nullOrValue($row['edisi'] ?? null),
            'classification'       => $this->nullOrValue($row['klasifikasi'] ?? null),
            'call_number'          => $this->nullOrValue($row['no_panggil'] ?? null),
            'physical_description' => $this->nullOrValue($row['deskripsi_fisik'] ?? null),
            'description'          => $this->nullOrValue($row['sinopsis'] ?? null),
        ]);

        // Create initial copy only if we have copy-specific data
        // (avoid creating empty copies from export summary-only rows)
        if ($this->hasCopyData($row)) {
            $this->createBookCopy($book, $row);
        } else {
            // Always create at least 1 empty copy so the book has stock
            $book->copies()->create([
                'book_id'      => $book->id,
                'condition'    => 'baik',
                'is_available' => true,
            ]);
        }

        $this->imported++;
        return null;
    }

    /**
     * Check if row has copy-specific data (not just book summary info).
     * Export-only summary columns (total_eksemplar, tersedia, dipinjam, rusak_hilang)
     * don't count as copy data.
     */
    private function hasCopyData(array $row): bool
    {
        return !empty($row['kode_eksemplar'])
            || !empty($row['no_inventaris'])
            || !empty($row['rak'])
            || (!empty($row['harga']) && is_numeric(str_replace([',', '.'], '', (string) $row['harga'])));
    }

    /**
     * Return null for dash/empty values, otherwise return trimmed string.
     */
    private function nullOrValue($value): ?string
    {
        if ($value === null || $value === '' || trim((string) $value) === '-') {
            return null;
        }
        return trim((string) $value);
    }

    protected function createBookCopy(Book $book, array $row): BookCopy
    {
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

        $condition = 'baik';
        if (!empty($row['kondisi'])) {
            $kondisi = strtolower(trim((string) $row['kondisi']));
            if (in_array($kondisi, ['baik', 'rusak', 'hilang'])) {
                $condition = $kondisi;
            }
        }

        // Safely parse price — must be numeric or null
        $price = null;
        if (!empty($row['harga'])) {
            $rawPrice = str_replace([',', ' '], ['', ''], (string) $row['harga']);
            if (is_numeric($rawPrice)) {
                $price = (float) $rawPrice;
            }
        }

        // Safely parse received_date
        $receivedDate = null;
        if (!empty($row['tanggal_diterima']) && $row['tanggal_diterima'] !== '-') {
            try {
                $raw = $row['tanggal_diterima'];
                if (is_numeric($raw)) {
                    // Excel serial date
                    $receivedDate = \Carbon\Carbon::createFromTimestamp(($raw - 25569) * 86400)->toDateString();
                } else {
                    $receivedDate = \Carbon\Carbon::parse($raw)->toDateString();
                }
            } catch (\Exception $e) {
                $receivedDate = null;
            }
        }

        return BookCopy::create([
            'book_id'       => $book->id,
            'copy_code'     => $this->nullOrValue($row['kode_eksemplar'] ?? null),
            'inventory_code'=> $this->nullOrValue($row['no_inventaris'] ?? null),
            'shelf_id'      => $shelfId,
            'shelf_column_id'=> $shelfColumnId,
            'shelf_location'=> $this->nullOrValue($row['lokasi_rak'] ?? null),
            'condition'     => $condition,
            'received_date' => $receivedDate,
            'price'         => $price,
            'is_available'  => $condition === 'baik',
        ]);
    }

    public function rules(): array
    {
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

    public function getImportedCount(): int { return $this->imported; }
    public function getUpdatedCount(): int { return $this->updated; }
    public function getSkippedCount(): int { return $this->skipped; }
}
