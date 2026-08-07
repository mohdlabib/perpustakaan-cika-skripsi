<?php

namespace App\Imports;

use App\Models\Visit;
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

class VisitsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithCustomCsvSettings
{
    use SkipsErrors, SkipsFailures;

    protected $imported = 0;
    protected $skipped = 0;
    protected $detectedHeadingRow = 1;

    public function __construct(?string $filePath = null)
    {
        if ($filePath && file_exists($filePath)) {
            $this->detectedHeadingRow = $this->detectHeadingRow($filePath);
        }
    }

    /**
     * Auto-detect heading row by scanning first 10 rows.
     * Export files have 4 title rows before the actual header.
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
     * Check if a row looks like the header row for visits.
     */
    protected function isHeaderRow(string $rowText): bool
    {
        $knownHeaders = ['tanggal', 'nama', 'tipe', 'nis', 'pengunjung', 'kunjungan', 'instansi', 'tujuan', 'jam'];
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
     * Header alias mapping.
     * Export headers: No, Tanggal, Jam, Tipe, NIS, Nama Pengunjung, Kelas / Instansi, Angkatan, Tujuan
     */
    private const HEADER_ALIASES = [
        'nis'      => ['nis', 'nis_siswa', 'no_induk'],
        'nama'     => ['nama', 'nama_pengunjung', 'nama_tamu', 'visitor_name', 'name'],
        'tipe'     => ['tipe', 'tipe_pengunjung', 'visitor_type', 'type'],
        'tanggal'  => ['tanggal', 'tanggal_kunjungan', 'visited_at', 'date', 'visit_date'],
        'jam'      => ['jam', 'waktu', 'time'],
        'instansi' => ['instansi', 'asal', 'kelas_instansi', 'kelas', 'institution', 'kelas_instansi_'],
        'angkatan' => ['angkatan', 'grade'],
        'tujuan'   => ['tujuan', 'tujuan_kunjungan', 'purpose'],
        // Summary columns — recognized but ignored
        'total_kunjungan' => ['total_kunjungan'],
        'kunjungan_siswa' => ['kunjungan_siswa'],
        'kunjungan_tamu'  => ['kunjungan_tamu'],
        'siswa_unik'      => ['siswa_unik'],
        'tanggal_export'  => ['tanggal_export'],
    ];

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
     * Detect summary/footer rows from export.
     */
    private function isSummaryRow(array $row): bool
    {
        $nama = strtolower(trim((string) ($row['nama'] ?? '')));
        $nis  = strtolower(trim((string) ($row['nis'] ?? '')));

        $summaryKeywords = ['ringkasan', 'total kunjungan', 'kunjungan siswa', 'kunjungan tamu', 'siswa unik', 'tanggal export'];
        foreach ($summaryKeywords as $kw) {
            if (str_contains($nama, $kw) || str_contains($nis, $kw)) {
                return true;
            }
        }

        if (str_contains((string) ($row['nama'] ?? ''), ': ')) {
            return true;
        }

        return false;
    }

    public function model(array $row)
    {
        $row = $this->normalizeRow($row);

        // Skip summary rows
        if ($this->isSummaryRow($row)) {
            $this->skipped++;
            return null;
        }

        $visitorName = trim((string) ($row['nama'] ?? ''));
        $tipe        = strtolower(trim((string) ($row['tipe'] ?? '')));
        $nis         = trim((string) ($row['nis'] ?? ''));
        $visitDate   = $this->parseDate($row['tanggal'] ?? null);

        // Determine visitor type from "Tipe" column
        $isGuest = in_array($tipe, ['tamu', 'guest']);
        if (empty($tipe)) {
            // fallback: no NIS = guest, has NIS = student
            $isGuest = empty($nis);
        }

        if (!$isGuest && !empty($nis)) {
            // Student visit
            $student = Student::find($nis);

            if (!$student) {
                // Try partial match (leading zeros stripped)
                $student = Student::where('nis', 'like', '%' . $nis)->first();
            }

            if ($student) {
                $dateStr = $visitDate ? $visitDate->toDateString() : now()->toDateString();
                $exists = Visit::where('student_nis', $student->nis)
                    ->whereDate('visited_at', $dateStr)
                    ->exists();

                if ($exists) {
                    $this->skipped++;
                    return null;
                }

                // Combine date + time (Jam column) for accurate visited_at
                $visitedAt = $this->combineDateAndTime($visitDate, $row['jam'] ?? null);

                $this->imported++;
                return new Visit([
                    'visitor_type' => 'student',
                    'student_nis'  => $student->nis,
                    'guest_purpose'=> !empty($row['tujuan']) && $row['tujuan'] !== '-' ? trim((string)$row['tujuan']) : null,
                    'visited_at'   => $visitedAt,
                ]);
            }
            // NIS given but student not found in DB — treat as guest or skip based on requirement
            // Requirement says fallback to guest:
            $isGuest = true;
        }

        // Guest visit
        if (empty($visitorName)) {
            $this->skipped++;
            return null;
        }

        $this->imported++;

        $institution = $row['instansi'] ?? null;
        $purpose     = $row['tujuan'] ?? null;

        if ($purpose === 'Kegiatan Perpustakaan') {
            $purpose = null;
        }

        // Combine date + time for accurate visited_at
        $visitedAt = $this->combineDateAndTime($visitDate, $row['jam'] ?? null);

        return new Visit([
            'visitor_type'      => 'guest',
            'guest_name'        => $visitorName,
            'guest_institution' => ($institution && $institution !== '-') ? $institution : null,
            'guest_purpose'     => ($purpose && $purpose !== '-') ? $purpose : null,
            'visited_at'        => $visitedAt,
        ]);
    }

    protected function combineDateAndTime(?Carbon $date, $time): Carbon
    {
        $date = $date ?? now();
        if (empty($time)) {
            return $date;
        }

        try {
            // If time is just a string like "10:30"
            if (is_string($time) && preg_match('/^(\d{1,2}):(\d{2})/', $time, $matches)) {
                return $date->setTime((int)$matches[1], (int)$matches[2]);
            }
        } catch (\Exception $e) {}

        return $date;
    }

    public function rules(): array
    {
        return [];
    }

    public function customValidationMessages(): array
    {
        return [];
    }

    protected function parseDate($value): ?Carbon
    {
        if (empty($value) || $value === '-') return null;

        try {
            if (is_numeric($value)) {
                return Carbon::createFromTimestamp(($value - 25569) * 86400);
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

    public function getImportedCount(): int { return $this->imported; }
    public function getSkippedCount(): int { return $this->skipped; }
}
