<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Template import siswa — header di baris 1, langsung bisa diimport.
 * Kolom: NIS, Nama Lengkap, Kelas, Angkatan, No. Telepon
 */
class StudentsTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithEvents
{
    public function headings(): array
    {
        return ['NIS', 'Nama Lengkap', 'Kelas', 'Angkatan', 'No. Telepon'];
    }

    public function array(): array
    {
        return [
            ['12345', 'Ahmad Budi Santoso', 'XII IPA 1', 'XII', '08123456789'],
            ['12346', 'Siti Rahayu',         'XII IPS 2', 'XII', '08234567890'],
            ['11234', 'Budi Hartono',         'XI IPA 1',  'XI',  ''],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 14, 'B' => 30, 'C' => 16, 'D' => 12, 'E' => 18];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Style header row (row 1)
                $sheet->getStyle('A1:E1')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0D47A1']]],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                // Style data rows
                $lastRow = $sheet->getHighestRow();
                if ($lastRow > 1) {
                    $sheet->getStyle("A2:E{$lastRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                    ]);
                    // Alternating row colors
                    for ($i = 2; $i <= $lastRow; $i++) {
                        $color = ($i % 2 === 0) ? 'F5F9FF' : 'FFFFFF';
                        $sheet->getStyle("A{$i}:E{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                        ]);
                    }
                }

                // Format kolom NIS sebagai teks agar leading zero tidak hilang
                $sheet->getStyle('A1:A1000')->getNumberFormat()->setFormatCode('@');

                // Freeze header
                $sheet->freezePane('A2');

                // Set nama sheet
                $event->sheet->setTitle('Data Siswa');
            },
        ];
    }
}
