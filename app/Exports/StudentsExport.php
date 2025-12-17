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
            'Jenis Kelamin',
            'Kelas',
            'Angkatan',
            'No. Telepon',
            'Alamat',
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
            $student->gender ?? '-',
            $student->class ?? '-',
            $student->grade->name ?? '-',
            $student->phone ?? '-',
            $student->address ?? '-',
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
            'D' => 15,  // Gender
            'E' => 12,  // Kelas
            'F' => 15,  // Angkatan
            'G' => 18,  // Telepon
            'H' => 35,  // Alamat
            'I' => 16,  // Total
            'J' => 14,  // Dipinjam
            'K' => 12,  // Terlambat
            'L' => 18,  // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2E4A35'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
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
                $lastColumn = 'L';
                
                // Add title header
                $sheet->insertNewRowBefore(1, 4);
                $sheet->mergeCells('A1:L1');
                $sheet->setCellValue('A1', 'LAPORAN DATA SISWA PERPUSTAKAAN');
                $sheet->mergeCells('A2:L2');
                $sheet->setCellValue('A2', 'PERPUSTAKAAN SMAN 8 PEKANBARU');
                $sheet->mergeCells('A3:L3');
                $sheet->setCellValue('A3', 'Tanggal Export: ' . now()->format('d F Y H:i'));
                
                // Style title
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '2E4A35']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Border for data
                $dataRange = 'A5:' . $lastColumn . $lastRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Center align number columns
                $sheet->getStyle('A5:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B5:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D5:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I5:K' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Conditional formatting for overdue
                for ($row = 6; $row <= $lastRow; $row++) {
                    $overdueCell = $sheet->getCell('K' . $row)->getValue();
                    if ($overdueCell > 0) {
                        $sheet->getStyle('K' . $row)->applyFromArray([
                            'font' => ['color' => ['rgb' => 'DC2626'], 'bold' => true],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FEE2E2'],
                            ],
                        ]);
                    }
                }
                
                // Add summary at bottom
                $summaryRow = $lastRow + 2;
                $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN:');
                $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
                
                $totalStudents = Student::count();
                $activeBorrowers = Borrowing::where('status', 'borrowed')->distinct('student_nis')->count('student_nis');
                $totalBorrowings = Borrowing::count();
                $overdueCount = Borrowing::where('status', 'borrowed')->where('due_date', '<', now())->count();
                
                $sheet->setCellValue('A' . ($summaryRow + 1), 'Total Siswa: ' . $totalStudents);
                $sheet->setCellValue('A' . ($summaryRow + 2), 'Siswa Aktif Meminjam: ' . $activeBorrowers);
                $sheet->setCellValue('A' . ($summaryRow + 3), 'Total Semua Peminjaman: ' . $totalBorrowings);
                $sheet->setCellValue('A' . ($summaryRow + 4), 'Peminjaman Terlambat: ' . $overdueCount);
                
                if ($overdueCount > 0) {
                    $sheet->getStyle('A' . ($summaryRow + 4))->applyFromArray([
                        'font' => ['color' => ['rgb' => 'DC2626'], 'bold' => true],
                    ]);
                }
                
                // Freeze header row
                $sheet->freezePane('A6');
            },
        ];
    }
}
