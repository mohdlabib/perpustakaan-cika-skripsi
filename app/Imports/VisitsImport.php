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
use Carbon\Carbon;

class VisitsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    protected $imported = 0;
    protected $skipped = 0;

    public function model(array $row)
    {
        $visitorName = trim($row['nama'] ?? $row['nama_pengunjung'] ?? '');
        $visitorType = strtolower(trim($row['tipe'] ?? $row['tipe_pengunjung'] ?? 'umum'));
        $nis = trim($row['nis'] ?? '');
        $visitDate = $this->parseDate($row['tanggal'] ?? $row['tanggal_kunjungan'] ?? null);

        // If NIS is provided, try to find the student
        if (!empty($nis)) {
            $student = Student::find($nis);
            if ($student) {
                $this->imported++;
                return new Visit([
                    'visitor_type' => 'student',
                    'student_nis' => $nis,
                    'visited_at' => $visitDate ?? now(),
                ]);
            }
        }

        // Guest visitor
        if (empty($visitorName)) {
            $this->skipped++;
            return null;
        }

        $this->imported++;

        return new Visit([
            'visitor_type' => 'guest',
            'guest_name' => $visitorName,
            'guest_institution' => $row['instansi'] ?? $row['asal'] ?? null,
            'guest_purpose' => $row['tujuan'] ?? $row['tujuan_kunjungan'] ?? null,
            'visited_at' => $visitDate ?? now(),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required_without:nis|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama.required_without' => 'Kolom Nama wajib diisi jika NIS tidak diisi.',
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
