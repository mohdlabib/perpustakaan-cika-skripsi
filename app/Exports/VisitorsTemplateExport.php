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
 * Template import kunjungan.
 * Header IDENTIK dengan VisitorsExport agar file export bisa langsung diimport kembali.
 * Kolom: Tanggal, Jam, Tipe, NIS, Nama Pengunjung, Kelas / Instansi, Tujuan
 * (Kolom "No" dan "Angkatan" ada di export tapi diabaikan saat import — tetap disertakan agar konsisten)
 */
class VisitorsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    public function array(): array
    {
        return [
            // Contoh 1: Kunjungan siswa
            ['04/08/2026', '08:30', 'Siswa', '12345', 'Ahmad Budi', 'XII IPA 1', 'Membaca buku'],
            // Contoh 2: Kunjungan tamu (kosongkan NIS)
            ['04/08/2026', '09:15', 'Tamu', '', 'Siti Nurbaya', 'Universitas Riau', 'Referensi penelitian'],
        ];
    }

    public function headings(): array
    {
        // HARUS IDENTIK dengan VisitorsExport::headings() (kecuali "No" dan "Angkatan" yang opsional)
        return [
            'Tanggal',          // format: dd/mm/yyyy
            'Jam',              // format: HH:mm (24 jam)
            'Tipe',             // nilai: Siswa | Tamu
            'NIS',              // kosongkan jika Tamu
            'Nama Pengunjung',  // wajib
            'Kelas / Instansi', // kelas untuk siswa, instansi untuk tamu
            'Tujuan',           // opsional
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14,  // Tanggal
            'B' => 10,  // Jam
            'C' => 10,  // Tipe
            'D' => 14,  // NIS
            'E' => 28,  // Nama Pengunjung
            'F' => 22,  // Kelas / Instansi
            'G' => 30,  // Tujuan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'G';

                // Title rows
                $sheet->insertNewRowBefore(1, 3);
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA KUNJUNGAN PERPUSTAKAAN');
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->setCellValue('A2', 'Isi data sesuai kolom. Baris contoh (baris 5-6) boleh dihapus. Format tanggal: dd/mm/yyyy, Jam: HH:mm');
                $sheet->mergeCells("A3:{$lastCol}3");
                $sheet->setCellValue('A3', 'Kolom wajib: Tanggal, Tipe, NIS (jika Siswa) atau Nama Pengunjung (jika Tamu)');

                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1B5E20']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2:A3')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '555555']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Header row style (row 4 after insert)
                $headerRow = 4;
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1B5E20']]],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(20);

                // Data rows style
                $lastRow = $sheet->getHighestRow();
                if ($lastRow > $headerRow) {
                    $sheet->getStyle("A" . ($headerRow + 1) . ":{$lastCol}{$lastRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF8E1']],
                    ]);
                }

                $sheet->freezePane('A5');
            },
        ];
    }
}
