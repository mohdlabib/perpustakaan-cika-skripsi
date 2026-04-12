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
use Illuminate\Support\Facades\Hash;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    protected $imported = 0;
    protected $updated = 0;
    protected $skipped = 0;

    public function model(array $row)
    {
        $nis = trim($row['nis']);
        
        // Find or create grade
        $grade = null;
        if (!empty($row['kelas'])) {
            $grade = Grade::firstOrCreate(['name' => trim($row['kelas'])]);
        }

        // Check if student exists
        $existing = Student::find($nis);
        
        if ($existing) {
            // Update existing student
            $existing->update([
                'name' => trim($row['nama'] ?? $row['nama_siswa'] ?? $existing->name),
                'class' => $row['kelas'] ?? $existing->class,
                'grade_id' => $grade?->id ?? $existing->grade_id,
                'phone' => $row['telepon'] ?? $row['hp'] ?? $existing->phone,
            ]);
            $this->updated++;
            return null;
        }

        $this->imported++;

        return new Student([
            'nis' => $nis,
            'name' => trim($row['nama'] ?? $row['nama_siswa'] ?? ''),
            'class' => $row['kelas'] ?? null,
            'grade_id' => $grade?->id,
            'phone' => $row['telepon'] ?? $row['hp'] ?? null,
            'password' => Hash::make($nis), // Default password = NIS
        ]);
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|string|max:20',
            'nama' => 'required_without:nama_siswa|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nis.required' => 'Kolom NIS wajib diisi.',
            'nama.required_without' => 'Kolom Nama wajib diisi.',
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
