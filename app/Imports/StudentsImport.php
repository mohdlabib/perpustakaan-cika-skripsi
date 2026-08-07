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

    public function __construct(?string $filePath = null)
    {
        if ($filePath && file_exists($filePath)) {
            $this->detectedHeadingRow = $this->detectHeadingRow($filePath);
        }
    }

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

    protected function isHeaderRow(string $rowText): bool
    {
        $knownHeaders = ['nis', 'nama', 'kelas', 'angkatan', 'telepon', 'class'];
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

    private const HEADER_ALIASES = [
        'nis'      => ['nis', 'nis_siswa', 'no_induk', 'nomor_induk', 'nomor_induk_siswa'],
        'nama'     => ['nama', 'nama_siswa', 'nama_lengkap', 'name', 'full_name'],
        'kelas'    => ['kelas', 'class', 'kelas_siswa', 'rombel'],
        'angkatan' => ['angkatan', 'grade', 'tahun_angkatan', 'tahun_masuk'],
        'telepon'  => ['telepon', 'no_telepon', 'nomor_telepon', 'hp', 'no_hp', 'phone', 'handphone', 'wa', 'whatsapp', 'no_telepon'],
        // Export-only columns — recognized but ignored
        'total_peminjaman'  => ['total_peminjaman'],
        'sedang_dipinjam'   => ['sedang_dipinjam'],
        'terlambat'         => ['terlambat'],
        'status'            => ['status'],
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
     * Check if a row is a summary/footer row from the export.
     * Export appends "RINGKASAN:" and summary data after the last data row.
     */
    private function isSummaryRow(string $nis, string $nama): bool
    {
        // Skip rows where NIS or nama contains colon (summary format like "RINGKASAN:" or "Total:")
        if (str_contains($nis, ':') || str_contains($nama, ':')) {
            return true;
        }

        // Skip rows starting with known summary keywords
        $summaryKeywords = ['ringkasan', 'total siswa', 'siswa aktif', 'total semua', 'peminjaman terlambat', 'tanggal export'];
        $nisLower = strtolower($nis);
        $namaLower = strtolower($nama);
        foreach ($summaryKeywords as $kw) {
            if (str_contains($nisLower, $kw) || str_contains($namaLower, $kw)) {
                return true;
            }
        }

        // NOTE: Removed the space-rejection logic — many valid NIS values contain spaces
        // Only reject based on summary keywords above

        return false;
    }

    public function model(array $row)
    {
        $row = $this->normalizeRow($row);

        $nis  = trim((string) ($row['nis'] ?? ''));
        $nama = trim((string) ($row['nama'] ?? ''));

        if (empty($nis) || empty($nama)) {
            $this->skipped++;
            return null;
        }

        if ($this->isSummaryRow($nis, $nama)) {
            $this->skipped++;
            return null;
        }

        if ($nis === '-' || $nama === '-') {
            $this->skipped++;
            return null;
        }

        // Find or create grade
        $grade = null;
        if (!empty($row['angkatan']) && trim((string) $row['angkatan']) !== '-') {
            $gradeName = trim((string) $row['angkatan']);
            // Skip if angkatan looks like a summary label
            if (!str_contains(strtolower($gradeName), ':') && !str_contains(strtolower($gradeName), 'total')) {
                $grade = Grade::firstOrCreate(['name' => $gradeName]);
            }
        }

        $kelas = (!empty($row['kelas']) && trim((string) $row['kelas']) !== '-')
            ? trim((string) $row['kelas'])
            : null;

        $telepon = (!empty($row['telepon']) && trim((string) $row['telepon']) !== '-')
            ? trim((string) $row['telepon'])
            : null;

        $existing = Student::find($nis);

        if ($existing) {
            $existing->update([
                'name'     => $nama,
                'class'    => $kelas ?? $existing->class,
                'grade_id' => $grade?->id ?? $existing->grade_id,
                'phone'    => $telepon ?? $existing->phone,
            ]);
            $this->updated++;
            return null;
        }

        $this->imported++;

        return new Student([
            'nis'      => $nis,
            'name'     => $nama,
            'class'    => $kelas,
            'grade_id' => $grade?->id,
            'phone'    => $telepon,
            'password' => Hash::make($nis),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nis'  => 'nullable|max:20',
            '*.nama' => 'nullable|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.nis.max'  => 'NIS terlalu panjang (maks 20 karakter).',
            '*.nama.max' => 'Nama terlalu panjang (maks 255 karakter).',
        ];
    }

    public function getImportedCount(): int { return $this->imported; }
    public function getUpdatedCount(): int { return $this->updated; }
    public function getSkippedCount(): int { return $this->skipped; }
}
