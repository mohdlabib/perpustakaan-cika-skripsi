<?php

namespace App\Exports;

use App\Models\Borrowing;
use Maatwebsite\Excel\Concerns\FromQuery;
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

class BorrowingsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $status;
    protected $startDate;
    protected $endDate;
    protected $rowNumber = 0;

    public function __construct($status = null, $startDate = null, $endDate = null)
    {
        $this->status = $status;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        $query = Borrowing::with(['student', 'book']);

        if ($this->status) {
            if ($this->status === 'overdue') {
                $query->where('status', 'borrowed')->where('due_date', '<', now());
            } else {
                $query->where('status', $this->status);
            }
        }

        if ($this->startDate) {
            $query->whereDate('borrow_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('borrow_date', '<=', $this->endDate);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Tipe Peminjam',
            'Nama Peminjam',
            'NIS / Info',
            'Judul Buku',
            'Penulis',
            'Tanggal Pinjam',
            'Batas Kembali',
            'Tanggal Kembali',
            'Status',
        ];
    }

    public function map($borrowing): array
    {
        $this->rowNumber++;

        // Determine borrower type and info
        $borrowerType = $borrowing->borrower_type ?? 'student';
        if ($borrowerType === 'teacher') {
            $borrowerName = $borrowing->borrower_name ?? '-';
            $borrowerInfo = $borrowing->borrower_info ?? '-';
        } else {
            $borrowerName = $borrowing->student->name ?? '-';
            $borrowerInfo = $borrowing->student_nis ?? '-';
        }

        $statusMap = [
            'pending' => 'Menunggu Persetujuan',
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'rejected' => 'Ditolak',
            'overdue' => 'Terlambat',
        ];

        $status = $statusMap[$borrowing->status] ?? $borrowing->status;
        if ($borrowing->status === 'borrowed' && $borrowing->due_date && $borrowing->due_date->isPast()) {
            $status = 'Terlambat (' . abs($borrowing->days_remaining) . ' hari)';
        }

        return [
            $this->rowNumber,
            $borrowerType === 'teacher' ? 'Guru' : 'Siswa',
            $borrowerName,
            $borrowerInfo,
            $borrowing->book->title ?? '-',
            $borrowing->book->author ?? '-',
            $borrowing->borrow_date ? $borrowing->borrow_date->format('d/m/Y') : '-',
            $borrowing->due_date ? $borrowing->due_date->format('d/m/Y') : '-',
            $borrowing->return_date ? $borrowing->return_date->format('d/m/Y') : '-',
            $status,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 14,  // Tipe
            'C' => 25,  // Nama
            'D' => 15,  // NIS
            'E' => 35,  // Judul
            'F' => 22,  // Penulis
            'G' => 14,  // Tgl Pinjam
            'H' => 14,  // Batas Kembali
            'I' => 16,  // Tgl Kembali
            'J' => 22,  // Status
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Styles applied in registerEvents after row insertion
        return [];
    }

    public function title(): string
    {
        return 'Data Peminjaman';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'J';
                
                // Add title header (4 rows) — consistent with other exports
                $sheet->insertNewRowBefore(1, 4);
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', 'LAPORAN DATA PEMINJAMAN BUKU');
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', 'PERPUSTAKAAN JENDELA ILMU');
                $sheet->mergeCells("A3:{$lastColumn}3");
                
                // Build period text
                $periodText = 'Tanggal Export: ' . now()->format('d F Y H:i') . ' WIB';
                if ($this->startDate || $this->endDate) {
                    $periodText = 'Periode: ';
                    $periodText .= $this->startDate ? \Carbon\Carbon::parse($this->startDate)->format('d M Y') : 'Awal';
                    $periodText .= ' - ';
                    $periodText .= $this->endDate ? \Carbon\Carbon::parse($this->endDate)->format('d M Y') : 'Sekarang';
                }
                $sheet->setCellValue('A3', $periodText);
                
                // Recalculate lastRow after insert
                $lastRow = $sheet->getHighestRow();
                
                // Style title rows
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1B5E20']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '2E7D32']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Row 5 is header row
                $headerRow = 5;
                
                // Style header row
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2E7D32'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '1B5E20'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(22);
                
                // Style data rows
                $dataStartRow = $headerRow + 1;
                if ($dataStartRow <= $lastRow) {
                    $sheet->getStyle("A{$dataStartRow}:{$lastColumn}{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'D1D5DB'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    
                    // Alternating row colors
                    for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                        if (($row - $dataStartRow) % 2 === 1) {
                            $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'F1F8E9'],
                                ],
                            ]);
                        }
                    }
                    
                    // Center align columns
                    $sheet->getStyle("A{$dataStartRow}:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("D{$dataStartRow}:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("G{$dataStartRow}:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    // Conditional formatting for status
                    for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                        $statusCell = (string) $sheet->getCell("J{$row}")->getValue();
                        
                        if (str_contains($statusCell, 'Terlambat')) {
                            $sheet->getStyle("J{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => 'DC2626'], 'bold' => true],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'FEE2E2'],
                                ],
                            ]);
                        } elseif ($statusCell === 'Dikembalikan') {
                            $sheet->getStyle("J{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => '16A34A']],
                            ]);
                        } elseif ($statusCell === 'Dipinjam') {
                            $sheet->getStyle("J{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => '2563EB'], 'bold' => true],
                            ]);
                        } elseif ($statusCell === 'Menunggu Persetujuan') {
                            $sheet->getStyle("J{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => 'D97706']],
                            ]);
                        }
                    }
                }
                
                // Add summary at bottom
                $summaryRow = $lastRow + 2;
                $sheet->setCellValue("A{$summaryRow}", 'RINGKASAN:');
                $sheet->getStyle("A{$summaryRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1B5E20']],
                ]);
                
                $totalBorrowings = Borrowing::count();
                $activeBorrowed = Borrowing::where('status', 'borrowed')->count();
                $returned = Borrowing::where('status', 'returned')->count();
                $overdue = Borrowing::where('status', 'borrowed')->where('due_date', '<', now())->count();
                
                $summaryData = [
                    ['Total Peminjaman', $totalBorrowings],
                    ['Sedang Dipinjam', $activeBorrowed],
                    ['Sudah Dikembalikan', $returned],
                    ['Terlambat', $overdue],
                ];
                
                foreach ($summaryData as $i => $item) {
                    $r = $summaryRow + 1 + $i;
                    $sheet->setCellValue("A{$r}", $item[0]);
                    $sheet->setCellValue("C{$r}", ': ' . $item[1]);
                    $sheet->getStyle("A{$r}")->getFont()->setBold(true);
                }
                
                if ($overdue > 0) {
                    $r = $summaryRow + 4;
                    $sheet->getStyle("A{$r}:C{$r}")->applyFromArray([
                        'font' => ['color' => ['rgb' => 'DC2626'], 'bold' => true],
                    ]);
                }
                
                // Print setup
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.3)->setRight(0.3);
                
                // Freeze header row
                $sheet->freezePane('A6');
            },
        ];
    }
}
