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
 * Template import peminjaman.
 * Header identik dengan BorrowingsExport (kolom yang diproses saat import).
 */
class BorrowingsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    public function array(): array
    {
        return [
            // Contoh: peminjaman siswa aktif
            ['Siswa', 'Ahmad Budi', '12345', 'Clean Code', 'Robert C. Martin', 'BK-001', '04/08/2026', '11/08/2026', '', 'Dipinjam'],
            // Contoh: peminjaman sudah dikembalikan
            ['Siswa', 'Siti Rahayu', '12346', 'Pemrograman PHP', 'John Doe', 'BK-002', '01/08/2026', '08/08/2026', '07/08/2026', 'Dikembalikan'],
            // Contoh: peminjaman guru
            ['Guru', 'Pak Budi', '', 'Matematika Dasar', 'Tim Penulis', 'BK-003', '04/08/2026', '18/08/2026', '', 'Dipinjam'],
        ];
    }

    public function headings(): array
    {
        // Identik dengan BorrowingsExport::headings() (minus "No" yang auto-number)
        return [
            'Tipe Peminjam',        // Siswa | Guru
            'Nama Peminjam',        // nama lengkap
            'NIS / Info',           // NIS untuk siswa, info untuk guru
            'Judul Buku',           // judul atau sebagian judul
            'Penulis',              // opsional (membantu disambiguasi judul)
            'Kode Eksemplar',       // kode copy, opsional
            'Tanggal Pinjam',       // format: dd/mm/yyyy
            'Batas Kembali',        // format: dd/mm/yyyy
            'Tanggal Kembali',      // format: dd/mm/yyyy, kosongkan jika belum kembali
            'Status',               // Dipinjam | Dikembalikan | Menunggu Persetujuan | Ditolak
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16, 'B' => 24, 'C' => 14, 'D' => 30,
            'E' => 22, 'F' => 16, 'G' => 16, 'H' => 16,
            'I' => 16, 'J' => 24,
        ];
    }

    public function styles(Worksheet $sheet) { return []; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'J';

                $sheet->insertNewRowBefore(1, 3);
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA PEMINJAMAN');
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->setCellValue('A2', 'Kolom wajib: Judul Buku dan NIS (untuk Siswa). Format tanggal: dd/mm/yyyy. Status: Dipinjam / Dikembalikan / Menunggu Persetujuan');
                $sheet->mergeCells("A3:{$lastCol}3");
                $sheet->setCellValue('A3', 'Data duplikat (NIS + Judul + Tanggal Pinjam sama) akan dilewati otomatis.');

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
