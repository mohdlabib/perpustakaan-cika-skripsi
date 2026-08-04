<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Template import buku.
 * Header identik dengan BooksExport (kolom yang diproses saat import).
 * Kolom statistik (Total Eksemplar, Tersedia, Dipinjam, Rusak/Hilang) di-export
 * tapi diabaikan saat import — TIDAK disertakan di template agar tidak membingungkan.
 */
class BooksTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    public function array(): array
    {
        return [
            [
                'Clean Code', 'Robert C. Martin', '978-0-13-235088-4',
                'Prentice Hall', '2008', 'Teknologi', '005.13', '005.13 MAR c',
                'Cetakan ke-1',
            ],
            [
                'Pemrograman PHP', 'John Doe', '978-xxx-xxx',
                'Penerbit ABC', '2024', 'Teknologi', '005.2', '005.2 DOE p',
                '',
            ],
        ];
    }

    public function headings(): array
    {
        // Identik dengan BooksExport::headings() (hanya kolom yang bisa diimport)
        return [
            'Judul Buku',   // wajib
            'Pengarang',    // wajib
            'ISBN',         // opsional, untuk deduplikasi
            'Penerbit',     // opsional
            'Tahun Terbit', // opsional
            'Kategori',     // nama kategori (jika ada di master data)
            'Klasifikasi',  // kode DDC / klasifikasi
            'No. Panggil',  // call number
            'Edisi',        // cetakan / edisi
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 32, 'B' => 24, 'C' => 20,
            'D' => 20, 'E' => 14, 'F' => 18,
            'G' => 14, 'H' => 20, 'I' => 16,
        ];
    }

    public function styles(Worksheet $sheet) { return []; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'I';

                $sheet->insertNewRowBefore(1, 3);
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA BUKU');
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->setCellValue('A2', 'Kolom wajib: Judul Buku dan Pengarang. ISBN digunakan untuk deduplikasi (buku dengan ISBN sama akan di-update).');
                $sheet->mergeCells("A3:{$lastCol}3");
                $sheet->setCellValue('A3', 'Untuk menambah eksemplar, gunakan fitur Import Eksemplar di halaman detail buku.');

                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1B5E20']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2:A3')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '555555']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $headerRow = 4;
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1B5E20']]],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(20);
                $lastRow = $sheet->getHighestRow();
                if ($lastRow > $headerRow) {
                    $sheet->getStyle("A" . ($headerRow + 1) . ":{$lastCol}{$lastRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF8E1']],
                    ]);
                }
                $sheet->freezePane('A5');
            },
        ];
    }
}
