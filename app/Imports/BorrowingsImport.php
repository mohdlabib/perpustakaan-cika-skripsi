<?php

namespace App\Imports;

use App\Models\Borrowing;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BorrowingsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithCustomCsvSettings
{
    use SkipsErrors, SkipsFailures;

    protected $imported = 0;
    protected $skipped = 0;
    protected $detectedHeadingRow = 1;

    /**
     * Constructor: auto-detect the heading row from file.
     */
    public function __construct(?string $filePath = null)
    {
        if ($filePath && file_exists($filePath)) {
            $this->detectedHeadingRow = $this->detectHeadingRow($filePath);
        }
    }

    /**
     * Auto-detect the heading row by scanning first 10 rows.
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
                for ($col = 1; $col <= min(15, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn())); $col++) {
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
     * Check if a row text contains known header keywords for borrowings.
     */
    protected function isHeaderRow(string $rowText): bool
    {
        $knownHeaders = ['nis', 'peminjam', 'tanggal', 'pinjam', 'kembali', 'status', 'judul', 'buku'];
        $matchCount = 0;

        foreach ($knownHeaders as $header) {
            if (str_contains($rowText, $header)) {
                $matchCount++;
            }
        }

        return $matchCount >= 3;
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
     * Header alias mapping for borrowing import.
     * Expanded to handle export format headers (e.g., 'NIS / Info', 'Nama Peminjam').
     */
    private const HEADER_ALIASES = [
        'nis' => ['nis', 'nis_siswa', 'no_induk', 'nomor_induk', 'nis_info', 'nis_siswa_info'],
        'kode_eksemplar' => ['kode_eksemplar', 'kode_buku', 'item_code', 'kode', 'copy_code'],
        'judul_buku' => ['judul_buku', 'judul', 'title', 'nama_buku'],
        'nama_peminjam' => ['nama_peminjam', 'nama', 'borrower_name', 'peminjam'],
        'tipe_peminjam' => ['tipe_peminjam', 'tipe', 'borrower_type', 'jenis_peminjam'],
        'penulis' => ['penulis', 'pengarang', 'author'],
        'tanggal_pinjam' => ['tanggal_pinjam', 'tgl_pinjam', 'borrow_date', 'tanggal_peminjaman'],
        'batas_kembali' => ['batas_kembali', 'tanggal_kembali', 'due_date', 'tgl_kembali', 'deadline'],
        'tanggal_dikembalikan' => ['tanggal_dikembalikan', 'tgl_dikembalikan', 'return_date', 'dikembalikan', 'tanggal_kembali_aktual'],
        'status' => ['status', 'status_peminjaman'],
    ];

    /**
     * Normalize row keys using alias mapping.
     * Fixed: now strips '/', '\', '(', ')' and other special characters.
     */
    private function normalizeRow(array $row): array
    {
        // Clean keys: strip BOM, whitespace, lowercase, replace special chars
        $cleanRow = [];
        foreach ($row as $key => $value) {
            $cleanKey = preg_replace('/[\x{FEFF}\x{200B}]/u', '', $key);
            // Replace special characters including /, \, (, ), :, ;, etc. with underscore
            $cleanKey = str_replace([' ', '-', '.', '/', '\\', '(', ')', ':', ';', ','], '_', strtolower(trim($cleanKey)));
            $cleanKey = preg_replace('/_+/', '_', $cleanKey);
            $cleanKey = trim($cleanKey, '_');
            $cleanRow[$cleanKey] = $value;
        }

        // Map aliases to canonical keys
        $mapped = [];
        foreach (self::HEADER_ALIASES as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                if (isset($cleanRow[$alias]) && $cleanRow[$alias] !== null && $cleanRow[$alias] !== '') {
                    $mapped[$canonical] = $cleanRow[$alias];
                    break;
                }
            }
        }

        // Keep unmapped keys too
        foreach ($cleanRow as $key => $value) {
            if (!isset($mapped[$key])) {
                $mapped[$key] = $value;
            }
        }

        return $mapped;
    }

    public function model(array $row)
    {
        $row = $this->normalizeRow($row);

        // Get NIS - ensure it's a string and trimmed
        $nis = trim((string) ($row['nis'] ?? ''));
        
        // If NIS is empty but we have a nama_peminjam, try to find student by name
        if (empty($nis) && !empty($row['nama_peminjam'])) {
            $studentByName = Student::where('name', 'like', '%' . trim($row['nama_peminjam']) . '%')->first();
            if ($studentByName) {
                $nis = $studentByName->nis;
            }
        }
        
        if (empty($nis)) {
            $this->skipped++;
            return null;
        }

        $bookCode = trim((string) ($row['kode_eksemplar'] ?? ''));
        
        // Find student by NIS
        $student = Student::find($nis);
        if (!$student) {
            // Try partial match (NIS might have leading zeros stripped by Excel)
            $student = Student::where('nis', 'like', '%' . $nis)->first();
        }
        
        if (!$student) {
            $this->skipped++;
            return null;
        }

        // Find book by copy_code or by title
        $book = null;
        $bookCopy = null;
        
        if (!empty($bookCode)) {
            $bookCopy = BookCopy::where('copy_code', $bookCode)->first();
            if ($bookCopy) {
                $book = $bookCopy->book;
            }
        }
        
        if (!$book && !empty($row['judul_buku'])) {
            $book = Book::where('title', 'like', '%' . trim($row['judul_buku']) . '%')->first();
        }

        if (!$book) {
            $this->skipped++;
            return null;
        }

        // Parse dates
        $borrowDate = $this->parseDate($row['tanggal_pinjam'] ?? null);
        $dueDate = $this->parseDate($row['batas_kembali'] ?? null);
        $returnDate = $this->parseDate($row['tanggal_dikembalikan'] ?? null);
        
        // Determine status
        $status = strtolower(trim((string) ($row['status'] ?? 'borrowed')));
        
        // Map Indonesian status labels back to English
        $statusMap = [
            'dipinjam' => 'borrowed',
            'dikembalikan' => 'returned',
            'menunggu persetujuan' => 'pending',
            'ditolak' => 'rejected',
            'terlambat' => 'borrowed', // Overdue is still 'borrowed' status
        ];
        
        // Check if status matches Indonesian label (possibly with extra info like "Terlambat (5 hari)")
        foreach ($statusMap as $label => $englishStatus) {
            if (str_contains($status, $label)) {
                $status = $englishStatus;
                break;
            }
        }
        
        if (!in_array($status, ['borrowed', 'returned', 'pending', 'rejected'])) {
            $status = $returnDate ? 'returned' : 'borrowed';
        }

        // If status is 'borrowed', try to assign a specific copy
        if ($status === 'borrowed' && !$bookCopy) {
            $bookCopy = $book->copies()
                ->where('condition', 'baik')
                ->where('is_available', true)
                ->whereDoesntHave('borrowings', fn($q) => $q->where('status', 'borrowed'))
                ->first();
        }

        $this->imported++;

        $borrowing = Borrowing::create([
            'student_nis' => $student->nis,
            'book_id' => $book->id,
            'book_copy_id' => $bookCopy?->id,
            'borrow_date' => $borrowDate ?? now(),
            'due_date' => $dueDate ?? now()->addDays(7),
            'return_date' => $returnDate,
            'status' => $status,
        ]);

        // Mark copy as unavailable if actively borrowed
        if ($status === 'borrowed' && $bookCopy) {
            $bookCopy->update(['is_available' => false]);
        }

        return null; // We already created the record manually
    }

    public function rules(): array
    {
        // Relaxed validation - we handle missing NIS in model()
        return [
            '*.nis' => 'nullable|string',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.nis.string' => 'Kolom NIS harus berupa teks.',
        ];
    }

    protected function parseDate($value): ?Carbon
    {
        if (empty($value) || $value === '-') return null;
        
        try {
            // Handle Excel numeric date (serial date number)
            if (is_numeric($value)) {
                return Carbon::createFromTimestamp(
                    ($value - 25569) * 86400
                );
            }
            
            // Handle d/m/Y format (from export)
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y', $value);
            }
            
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }
}
