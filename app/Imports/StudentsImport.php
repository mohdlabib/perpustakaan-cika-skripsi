<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Grade;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithCustomCsvSettings
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
     * Looks for known header keywords like 'NIS', 'Nama', 'Kelas'.
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
     * Check if a row text contains known header keywords for students.
     */
    protected function isHeaderRow(string $rowText): bool
    {
        $knownHeaders = ['nis', 'nama', 'kelas', 'angkatan', 'telepon', 'class'];
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
     * Includes export header formats like 'Nama Lengkap', 'No. Telepon', 'Angkatan'.
     */
    private const HEADER_ALIASES = [
        'nis' => ['nis', 'nis_siswa', 'no_induk', 'nomor_induk', 'nomor_induk_siswa'],
        'nama' => ['nama', 'nama_siswa', 'nama_lengkap', 'name', 'full_name'],
        'kelas' => ['kelas', 'class', 'kelas_siswa', 'rombel'],
        'angkatan' => ['angkatan', 'grade', 'tahun_angkatan', 'tahun_masuk'],
        'telepon' => ['telepon', 'no_telepon', 'nomor_telepon', 'hp', 'no_hp', 'phone', 'handphone', 'wa', 'whatsapp'],
        // Skip-only fields (export columns that don't exist in DB)
        'jenis_kelamin' => ['jenis_kelamin', 'gender', 'jk', 'l_p'],
        'alamat' => ['alamat', 'address'],
    ];

    /**
     * Normalize row keys to handle various header formats using alias mapping.
     */
    private function normalizeRow(array $row): array
    {
        // Clean keys: strip BOM, whitespace, lowercase, replace special chars
        $cleanRow = [];
        foreach ($row as $key => $value) {
            $cleanKey = preg_replace('/[\x{FEFF}\x{200B}]/u', '', (string) $key);
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
        // Normalize row keys for consistency
        $row = $this->normalizeRow($row);

        // Skip summary rows (e.g., "Total Siswa: 5", "RINGKASAN:")
        $nis = trim((string) ($row['nis'] ?? ''));
        $nama = trim((string) ($row['nama'] ?? ''));

        // Required: NIS and Nama must be present and not be summary text
        if (empty($nis) || empty($nama)) {
            $this->skipped++;
            return null;
        }

        // Skip rows that look like summary (contain ':', or NIS contains non-numeric/non-alphanumeric mixed)
        if (str_contains($nis, ':') || str_contains($nama, ':')) {
            $this->skipped++;
            return null;
        }

        // Skip placeholder values
        if ($nis === '-' || $nama === '-') {
            $this->skipped++;
            return null;
        }

        // Find or create grade
        $grade = null;
        if (!empty($row['angkatan']) && trim((string) $row['angkatan']) !== '-') {
            $gradeName = trim((string) $row['angkatan']);
            $grade = Grade::firstOrCreate(['name' => $gradeName]);
        }

        // Get other fields with proper defaults
        $kelas = !empty($row['kelas']) && trim((string) $row['kelas']) !== '-' 
            ? trim((string) $row['kelas']) 
            : null;
        
        $telepon = !empty($row['telepon']) && trim((string) $row['telepon']) !== '-' 
            ? trim((string) $row['telepon']) 
            : null;

        // Check if student exists
        $existing = Student::find($nis);

        if ($existing) {
            // Update existing student
            $existing->update([
                'name' => $nama,
                'class' => $kelas ?? $existing->class,
                'grade_id' => $grade?->id ?? $existing->grade_id,
                'phone' => $telepon ?? $existing->phone,
            ]);
            $this->updated++;
            return null;
        }

        $this->imported++;

        return new Student([
            'nis' => $nis,
            'name' => $nama,
            'class' => $kelas,
            'grade_id' => $grade?->id,
            'phone' => $telepon,
            'password' => Hash::make($nis), // Default password = NIS
        ]);
    }

    public function rules(): array
    {
        // Relaxed validation — we handle missing fields in model() method
        return [
            '*.nis' => 'nullable|string|max:20',
            '*.nama' => 'nullable|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.nis.max' => 'NIS terlalu panjang (maks 20 karakter).',
            '*.nama.max' => 'Nama terlalu panjang (maks 255 karakter).',
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
