<?php

namespace App\Exports;

use App\Models\Visit;
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
use Carbon\Carbon;

class VisitorsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $grade;
    protected $rowNumber = 0;

    public function __construct($startDate = null, $endDate = null, $grade = null)
    {
        $this->startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfMonth();
        $this->endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();
        $this->grade = $grade;
    }

    public function collection()
    {
        $query = Visit::with(['student.grade'])
            ->whereBetween('visited_at', [$this->startDate, $this->endDate]);
        
        if ($this->grade) {
            $query->whereHas('student', function($q) {
                $q->where('grade_id', $this->grade);
            });
        }
        
        return $query->orderBy('visited_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Jam',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Angkatan',
        ];
    }

    public function map($visit): array
    {
        $this->rowNumber++;
        
        return [
            $this->rowNumber,
            $visit->visited_at->format('d/m/Y'),
            $visit->visited_at->format('H:i') . ' WIB',
            $visit->student_nis,
            $visit->student->name ?? '-',
            $visit->student->class ?? '-',
            $visit->student->grade->name ?? '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 14,  // Tanggal
            'C' => 12,  // Jam
            'D' => 15,  // NIS
            'E' => 30,  // Nama
            'F' => 12,  // Kelas
            'G' => 15,  // Angkatan
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
        return 'Laporan Pengunjung';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = 'G';
                
                // Add title header
                $sheet->insertNewRowBefore(1, 4);
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'LAPORAN PENGUNJUNG PERPUSTAKAAN');
                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'PERPUSTAKAAN SMAN 8 PEKANBARU');
                $sheet->mergeCells('A3:G3');
                $sheet->setCellValue('A3', 'Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y'));
                
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
                
                // Center align columns
                $sheet->getStyle('A5:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B5:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F5:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Add summary at bottom
                $summaryRow = $lastRow + 2;
                $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN:');
                $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
                
                $totalVisits = Visit::whereBetween('visited_at', [$this->startDate, $this->endDate])->count();
                $uniqueVisitors = Visit::whereBetween('visited_at', [$this->startDate, $this->endDate])
                    ->distinct('student_nis')
                    ->count('student_nis');
                
                $sheet->setCellValue('A' . ($summaryRow + 1), 'Total Kunjungan: ' . $totalVisits);
                $sheet->setCellValue('A' . ($summaryRow + 2), 'Pengunjung Unik: ' . $uniqueVisitors);
                $sheet->setCellValue('A' . ($summaryRow + 3), 'Tanggal Export: ' . now()->format('d F Y H:i'));
                
                // Freeze header row
                $sheet->freezePane('A6');
            },
        ];
    }
}
