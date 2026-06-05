<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\Borrowing;
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

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $grade;
    protected $rowNumber = 0;

    public function __construct($grade = null)
    {
        $this->grade = $grade;
    }

    public function collection()
    {
        $query = Student::with(['grade', 'borrowings']);
        
        if ($this->grade) {
            $query->where('grade_id', $this->grade);
        }
        
        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Lengkap',
            'Kelas',
            'Angkatan',
            'No. Telepon',
            'Total Peminjaman',
            'Sedang Dipinjam',
            'Terlambat',
            'Status',
        ];
    }

    public function map($student): array
    {
        $this->rowNumber++;
        $totalBorrowings = $student->borrowings->count();
        $activeBorrowings = $student->borrowings->where('status', 'borrowed')->count();
        $overdueBorrowings = $student->borrowings->where('status', 'borrowed')
            ->filter(fn($b) => $b->due_date < now())->count();
        
        return [
            $this->rowNumber,
            $student->nis,
            $student->name,
            $student->class ?? '-',
            $student->grade->name ?? '-',
            $student->phone ?? '-',
            $totalBorrowings,
            $activeBorrowings,
            $overdueBorrowings,
            $activeBorrowings > 0 ? 'Aktif Meminjam' : 'Tidak Meminjam',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // NIS
            'C' => 30,  // Nama
            'D' => 14,  // Kelas
            'E' => 12,  // Angkatan
            'F' => 18,  // Telepon
            'G' => 16,  // Total Peminjaman
            'H' => 15,  // Sedang Dipinjam
            'I' => 12,  // Terlambat
            'J' => 18,  // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styles applied in registerEvents after row insertion
        return [];
    }

    public function title(): string
    {
        return 'Laporan Siswa';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = 'J';
                
                // Add title header (4 rows)
                $sheet->insertNewRowBefore(1, 4);
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', 'LAPORAN DATA SISWA PERPUSTAKAAN');
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', 'PERPUSTAKAAN JENDELA ILMU');
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->setCellValue('A3', 'Tanggal Export: ' . now()->format('d F Y H:i') . ' WIB');
                
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
                
                // Row 4 is empty separator
                // Row 5 is the header row (after insert)
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
                
                // Style data rows with borders and alternating colors
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
                    
                    // Alternating row colors (zebra stripe)
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
                    
                    // Center align specific columns
                    $sheet->getStyle("A{$dataStartRow}:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B{$dataStartRow}:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("D{$dataStartRow}:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("G{$dataStartRow}:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    // Conditional formatting for overdue
                    for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                        $overdueCell = $sheet->getCell("I{$row}")->getValue();
                        if ($overdueCell > 0) {
                            $sheet->getStyle("I{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => 'DC2626'], 'bold' => true],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'FEE2E2'],
                                ],
                            ]);
                        }
                    }
                    
                    // Status column styling
                    for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                        $statusCell = $sheet->getCell("J{$row}")->getValue();
                        if ($statusCell === 'Aktif Meminjam') {
                            $sheet->getStyle("J{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => '1565C0'], 'bold' => true],
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
                
                $totalStudents = Student::count();
                $activeBorrowers = Borrowing::where('status', 'borrowed')->distinct('student_nis')->count('student_nis');
                $totalBorrowings = Borrowing::count();
                $overdueCount = Borrowing::where('status', 'borrowed')->where('due_date', '<', now())->count();
                
                $summaryData = [
                    ['Total Siswa Terdaftar', $totalStudents],
                    ['Siswa Aktif Meminjam', $activeBorrowers],
                    ['Total Semua Peminjaman', $totalBorrowings],
                    ['Peminjaman Terlambat', $overdueCount],
                ];
                
                foreach ($summaryData as $i => $item) {
                    $r = $summaryRow + 1 + $i;
                    $sheet->setCellValue("A{$r}", $item[0]);
                    $sheet->setCellValue("C{$r}", ': ' . $item[1]);
                    $sheet->getStyle("A{$r}")->getFont()->setBold(true);
                }
                
                if ($overdueCount > 0) {
                    $r = $summaryRow + 4;
                    $sheet->getStyle("A{$r}:C{$r}")->applyFromArray([
                        'font' => ['color' => ['rgb' => 'DC2626'], 'bold' => true],
                    ]);
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
