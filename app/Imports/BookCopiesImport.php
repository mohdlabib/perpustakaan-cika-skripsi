<?php

namespace App\Imports;

use App\Models\BookCopy;
use App\Models\Shelf;
use App\Models\ShelfColumn;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Import eksemplar (copies) untuk satu buku tertentu.
 * Template: Kode Eksemplar, No. Inventaris, Rak, Kolom, Harga, Kondisi, Tanggal Diterima
 */
class BookCopiesImport implements ToModel, WithHeadingRow, SkipsOnError, SkipsOnFailure, WithCustomCsvSettings
{
    use SkipsErrors, SkipsFailures;

    protected int $bookId;
    protected int $imported = 0;
    protected int $skipped = 0;
    protected int $detectedHeadingRow = 1;

    public function __construct(int $bookId, ?string $filePath = null)
    {
        $this->bookId = $bookId;

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
        $knownHeaders = ['kode', 'eksemplar', 'inventaris', 'kondisi', 'harga', 'rak'];
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
        'kode_eksemplar'   => ['kode_eksemplar', 'kode_buku', 'kode', 'copy_code', 'item_code'],
        'no_inventaris'    => ['no_inventaris', 'inventaris', 'inventory_code', 'nomor_inventaris', 'no_inventaris'],
        'rak'              => ['rak', 'shelf', 'nama_rak', 'lokasi_rak'],
        'kolom'            => ['kolom', 'column', 'kolom_rak'],
        'harga'            => ['harga', 'price'],
        'kondisi'          => ['kondisi', 'condition', 'status_kondisi'],
        'tanggal_diterima' => ['tanggal_diterima', 'tgl_diterima', 'received_date', 'tanggal'],
        'catatan'          => ['catatan', 'notes', 'keterangan'],
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

    public function model(array $row)
    {
        $row = $this->normalizeRow($row);

        // Skip completely empty rows
        $allValues = array_filter(array_values($row), fn($v) => !empty(trim((string) $v)));
        if (empty($allValues)) {
            $this->skipped++;
            return null;
        }

        // Skip summary/footer rows
        $kode = trim((string) ($row['kode_eksemplar'] ?? ''));
        if (str_contains(strtolower($kode), 'total') || str_contains(strtolower($kode), 'ringkasan')) {
            $this->skipped++;
            return null;
        }

        // *** DUPLICATE CHECK: skip if copy_code already exists for this book ***
        if (!empty($kode)) {
            $exists = BookCopy::where('book_id', $this->bookId)
                ->where('copy_code', $kode)
                ->exists();

            if ($exists) {
                $this->skipped++;
                return null;
            }
        }

        // Find shelf/column
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
            $k = strtolower(trim((string) $row['kondisi']));
            if (in_array($k, ['baik', 'rusak', 'hilang'])) {
                $condition = $k;
            }
        }

        // Parse price — strip "Rp", spaces, dots (thousand sep), commas
        $price = null;
        if (!empty($row['harga'])) {
            $rawPrice = preg_replace('/[^0-9]/', '', (string) $row['harga']);
            if ($rawPrice !== '') {
                $price = (float) $rawPrice;
            }
        }

        // Parse date
        $receivedDate = null;
        if (!empty($row['tanggal_diterima']) && $row['tanggal_diterima'] !== '-') {
            try {
                $raw = $row['tanggal_diterima'];
                if (is_numeric($raw)) {
                    $receivedDate = Carbon::createFromTimestamp(($raw - 25569) * 86400)->toDateString();
                } elseif (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $raw)) {
                    $receivedDate = Carbon::createFromFormat('d/m/Y', $raw)->toDateString();
                } else {
                    $receivedDate = Carbon::parse($raw)->toDateString();
                }
            } catch (\Exception $e) {
                $receivedDate = null;
            }
        }

        $this->imported++;

        // shelf_location fallback: jika rak tidak ada di DB, simpan sebagai teks
        $shelfLocationFallback = null;
        if (!$shelfId && !empty($row['rak'])) {
            $shelfLocationFallback = trim($row['rak']);
            if (!empty($row['kolom'])) {
                $shelfLocationFallback .= ' / ' . trim($row['kolom']);
            }
        }

        return new BookCopy([
            'book_id'        => $this->bookId,
            'copy_code'      => !empty($kode) ? $kode : null,
            'inventory_code' => !empty($row['no_inventaris']) && $row['no_inventaris'] !== '-' ? trim($row['no_inventaris']) : null,
            'shelf_id'       => $shelfId,
            'shelf_column_id'=> $shelfColumnId,
            'shelf_location' => $shelfLocationFallback,
            'condition'      => $condition,
            'received_date'  => $receivedDate,
            'price'          => $price,
            'notes'          => !empty($row['catatan']) && $row['catatan'] !== '-' ? trim($row['catatan']) : null,
            'is_available'   => $condition === 'baik',
        ]);
    }

    public function getImportedCount(): int { return $this->imported; }
    public function getSkippedCount(): int { return $this->skipped; }
}
