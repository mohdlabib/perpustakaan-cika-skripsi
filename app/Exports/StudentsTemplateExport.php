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
 * Template import siswa.
 * Header identik dengan StudentsExport (kolom yg bisa diimport).
 * Kolom statistik (Total Peminjaman, Sedang Dipinjam, Terlambat, Status) di-export
 * tapi diabaikan saat import — tetap ada agar user tahu formatnya.
 */
class StudentsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    public function array(): array
    {
        return [
            ['12345', 'Ahmad Budi Santoso', 'XII IPA 1', 'XII', '08123456789'],
            ['12346', 'Siti Rahayu',         'XII IPS 2', 'XII', '08234567890'],
            ['11234', 'Budi Hartono',         'XI IPA 1', 'XI',  ''],
        ];
    }

    public function headings(): array
    {
        // Identik dengan StudentsExport (kolom wajib untuk import)
        return [
            'NIS',          // wajib, unik
            'Nama Lengkap', // wajib
            'Kelas',        // opsional, contoh: XII IPA 1
            'Angkatan',     // opsional, contoh: XII / 2024
            'No. Telepon',  // opsional
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 14, 'B' => 30, 'C' => 16, 'D' => 12, 'E' => 18];
    }

    public function styles(Worksheet $sheet) { return []; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'E';

                $sheet->insertNewRowBefore(1, 3);
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA SISWA');
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->setCellValue('A2', 'Kolom wajib: NIS dan Nama Lengkap. NIS yang sudah ada akan di-update, NIS baru akan ditambahkan.');
                $sheet->mergeCells("A3:{$lastCol}3");
                $sheet->setCellValue('A3', 'Password default siswa baru = NIS siswa.');

                $this->applyTitleStyle($sheet, $lastCol);
            },
        ];
    }

    private function applyTitleStyle($sheet, string $lastCol): void
    {
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
    }
}
