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
use Carbon\Carbon;

class BorrowingsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    protected $imported = 0;
    protected $skipped = 0;

    public function model(array $row)
    {
        $nis = trim($row['nis'] ?? $row['nis_siswa'] ?? '');
        $bookCode = trim($row['kode_buku'] ?? $row['kode_eksemplar'] ?? '');
        
        // Find student
        $student = Student::find($nis);
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
        $dueDate = $this->parseDate($row['tanggal_kembali'] ?? $row['batas_kembali'] ?? null);
        $returnDate = $this->parseDate($row['tanggal_dikembalikan'] ?? null);
        
        // Determine status
        $status = strtolower(trim($row['status'] ?? 'borrowed'));
        if (!in_array($status, ['borrowed', 'returned', 'pending', 'rejected'])) {
            $status = $returnDate ? 'returned' : 'borrowed';
        }

        $this->imported++;

        return new Borrowing([
            'student_nis' => $nis,
            'book_id' => $book->id,
            'book_copy_id' => $bookCopy?->id,
            'borrow_date' => $borrowDate ?? now(),
            'due_date' => $dueDate ?? now()->addDays(7),
            'return_date' => $returnDate,
            'status' => $status,
        ]);
    }

    public function rules(): array
    {
        return [
            'nis' => 'required_without:nis_siswa|string',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nis.required_without' => 'Kolom NIS wajib diisi.',
        ];
    }

    protected function parseDate($value): ?Carbon
    {
        if (empty($value)) return null;
        
        try {
            // Handle Excel numeric date
            if (is_numeric($value)) {
                return Carbon::createFromTimestamp(
                    ($value - 25569) * 86400
                );
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
