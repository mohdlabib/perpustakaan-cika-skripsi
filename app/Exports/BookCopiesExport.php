<?php

namespace App\Exports;

use App\Models\Book;
use App\Models\BookCopy;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Export daftar eksemplar (copies) dari satu buku.
 * Format output bisa langsung dijadikan template untuk import kembali.
 */
class BookCopiesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected Book $book;
    protected int $rowNumber = 0;

    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    public function collection()
    {
        return $this->book->copies()->with(['shelf', 'shelfColumn'])->orderBy('id')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Eksemplar',
            'No. Inventaris',
            'Rak',
            'Kolom',
            'Harga',
            'Kondisi',
            'Status',
            'Tanggal Diterima',
            'Catatan',
        ];
    }

    public function map($copy): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $copy->copy_code ?? '',
            $copy->inventory_code ?? '',
            $copy->shelf->name ?? '',
            $copy->shelfColumn->name ?? '',
            $copy->price ?? '',
            $copy->condition,
            $copy->status,
            $copy->received_date ? $copy->received_date->format('d/m/Y') : '',
            $copy->notes ?? '',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  // No
            'B' => 18, // Kode Eksemplar
            'C' => 18, // No. Inventaris
            'D' => 18, // Rak
            'E' => 12, // Kolom
            'F' => 14, // Harga
            'G' => 12, // Kondisi
            'H' => 14, // Status
            'I' => 16, // Tanggal Diterima
            'J' => 28, // Catatan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function title(): string
    {
        return 'Eksemplar Buku';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'J';

                // Add title header (4 rows) — consistent with other exports
                $sheet->insertNewRowBefore(1, 4);
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', 'DAFTAR EKSEMPLAR BUKU');
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', $this->book->title . ' — ' . ($this->book->author ?? ''));
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->setCellValue('A3', 'Tanggal Export: ' . now()->format('d F Y H:i') . ' WIB');

                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1B5E20']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '2E7D32']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $headerRow = 5;

                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2E7D32'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '1B5E20'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(22);

                $dataStartRow = $headerRow + 1;
                if ($dataStartRow <= $lastRow) {
                    $sheet->getStyle("A{$dataStartRow}:{$lastColumn}{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['rgb' => 'D1D5DB'],
                            ],
                        ],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Alternating row colors
                    for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                        if (($row - $dataStartRow) % 2 === 1) {
                            $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                                'fill' => [
                                    'fillType'   => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'F1F8E9'],
                                ],
                            ]);
                        }
                    }

                    // Highlight rusak/hilang rows
                    for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                        $kondisi = strtolower((string) $sheet->getCell("G{$row}")->getValue());
                        if ($kondisi === 'rusak') {
                            $sheet->getStyle("G{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => 'D97706'], 'bold' => true],
                            ]);
                        } elseif ($kondisi === 'hilang') {
                            $sheet->getStyle("G{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => 'DC2626'], 'bold' => true],
                            ]);
                        }
                    }

                    $sheet->getStyle("F{$dataStartRow}:F{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("F{$dataStartRow}:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("A{$dataStartRow}:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("G{$dataStartRow}:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Print setup
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                // Freeze header row
                $sheet->freezePane('A6');
            },
        ];
    }
}
