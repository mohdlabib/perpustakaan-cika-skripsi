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
 * Template import buku — header di baris 1, langsung bisa diimport.
 * Kolom: Judul Buku, Pengarang, ISBN, Penerbit, Tahun Terbit, Tempat Terbit,
 *        Kategori, Klasifikasi, No. Panggil, Edisi, Deskripsi Fisik
 */
class BooksTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithEvents
{
    public function headings(): array
    {
        return [
            'Judul Buku',
            'Pengarang',
            'ISBN',
            'Penerbit',
            'Tahun Terbit',
            'Tempat Terbit',
            'Kategori',
            'Klasifikasi',
            'No. Panggil',
            'Edisi',
            'Deskripsi Fisik',
        ];
    }

    public function array(): array
    {
        return [
            ['Clean Code',        'Robert C. Martin', '978-0-13-235088-4', 'Prentice Hall',   '2008', 'New Jersey',  'Teknologi', '005.13', '005.13 MAR c', 'Cetakan ke-1', 'xiv, 431 hlm.; 24 cm'],
            ['Pemrograman PHP',   'John Doe',         '978-xxx-xxx',       'Penerbit ABC',    '2024', 'Jakarta',     'Teknologi', '005.2',  '005.2 DOE p',  '',            ''],
            ['Laskar Pelangi',    'Andrea Hirata',    '979-xxx-xxx',       'Bentang Pustaka', '2005', 'Yogyakarta',  'Fiksi',     '813',    '813 HIR l',    'Edisi ke-2',  'xii, 529 hlm.; 21 cm'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 32, 'B' => 22, 'C' => 20, 'D' => 20,
            'E' => 12, 'F' => 18, 'G' => 16, 'H' => 14,
            'I' => 18, 'J' => 16, 'K' => 26,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Header row style
                $sheet->getStyle('A1:K1')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6A1B9A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '4A148C']]],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                // Data rows
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 2) {
                    $sheet->getStyle("A2:K{$lastRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                    ]);
                    for ($i = 2; $i <= $lastRow; $i++) {
                        $color = ($i % 2 === 0) ? 'FAF5FF' : 'FFFFFF';
                        $sheet->getStyle("A{$i}:K{$i}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                        ]);
                    }
                }

                // ISBN & Tahun Terbit sebagai teks
                $sheet->getStyle('C1:C1000')->getNumberFormat()->setFormatCode('@');
                $sheet->getStyle('E1:E1000')->getNumberFormat()->setFormatCode('@');

                $sheet->freezePane('A2');
                $event->sheet->setTitle('Data Buku');
            },
        ];
    }
}
