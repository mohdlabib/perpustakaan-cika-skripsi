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

/**
 * Template import kunjungan — header di baris 1, langsung bisa diimport.
 * Kolom: Tanggal, Jam, Tipe, NIS, Nama Pengunjung, Kelas / Instansi, Tujuan
 */
class VisitorsTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithEvents
{
    public function headings(): array
    {
        return ['Tanggal', 'Jam', 'Tipe', 'NIS', 'Nama Pengunjung', 'Kelas / Instansi', 'Tujuan'];
    }

    public function array(): array
    {
        return [
            ['04/08/2026', '08:30', 'Siswa', '12345', 'Ahmad Budi',    'XII IPA 1',          'Membaca buku'],
            ['04/08/2026', '09:15', 'Tamu',  '',      'Siti Nurbaya',  'Universitas Riau',   'Referensi penelitian'],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 14, 'B' => 8, 'C' => 10, 'D' => 14, 'E' => 28, 'F' => 22, 'G' => 26];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Header row style
                $sheet->getStyle('A1:G1')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1B5E20']]],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                // Data rows
                $lastRow = $sheet->getHighestRow();
                if ($lastRow > 1) {
                    $sheet->getStyle("A2:G{$lastRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                    ]);
                    for ($i = 2; $i <= $lastRow; $i++) {
                        $color = ($i % 2 === 0) ? 'F5FFF5' : 'FFFFFF';
                        $sheet->getStyle("A{$i}:G{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                        ]);
                    }
                }

                // Kolom NIS sebagai teks
                $sheet->getStyle('D1:D1000')->getNumberFormat()->setFormatCode('@');
                // Kolom Tanggal sebagai teks (agar tidak auto-convert)
                $sheet->getStyle('A1:A1000')->getNumberFormat()->setFormatCode('@');
                // Kolom Jam sebagai teks
                $sheet->getStyle('B1:B1000')->getNumberFormat()->setFormatCode('@');

                // Dropdown untuk kolom Tipe (C)
                $validation = $sheet->getDataValidation('C2:C1000');
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
                    ->setAllowBlank(false)
                    ->setShowDropDown(false)
                    ->setFormula1('"Siswa,Tamu"');

                $sheet->freezePane('A2');
                $event->sheet->setTitle('Kunjungan');
            },
        ];
    }
}
