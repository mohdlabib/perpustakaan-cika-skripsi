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
        $this->startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now('Asia/Jakarta')->startOfMonth();
        $this->endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now('Asia/Jakarta')->endOfDay();
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
            'Tipe',
            'NIS',
            'Nama Pengunjung',
            'Kelas / Instansi',
            'Angkatan',
            'Tujuan',
        ];
    }

    public function map($visit): array
    {
        $this->rowNumber++;
        
        $isGuest = $visit->visitor_type === 'guest';
        $visitedAt = $visit->visited_at->timezone('Asia/Jakarta');
        
        return [
            $this->rowNumber,
            $visitedAt->format('d/m/Y'),
            $visitedAt->format('H:i') . ' WIB',
            $isGuest ? 'Tamu' : 'Siswa',
            $isGuest ? '-' : ($visit->student_nis ?? '-'),
            $isGuest ? ($visit->guest_name ?? '-') : ($visit->student->name ?? '-'),
            $isGuest ? ($visit->guest_institution ?? '-') : ($visit->student->class ?? '-'),
            $isGuest ? '-' : ($visit->student->grade->name ?? '-'),
            $isGuest ? ($visit->guest_purpose ?? '-') : 'Kegiatan Perpustakaan',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 14,  // Tanggal
            'C' => 12,  // Jam
            'D' => 10,  // Tipe
            'E' => 15,  // NIS
            'F' => 28,  // Nama
            'G' => 22,  // Kelas/Instansi
            'H' => 12,  // Angkatan
            'I' => 22,  // Tujuan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styles applied in registerEvents after row insertion
        return [];
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
                $lastColumn = 'I';
                
                // Add title header (4 rows)
                $sheet->insertNewRowBefore(1, 4);
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', 'LAPORAN PENGUNJUNG PERPUSTAKAAN');
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', 'PERPUSTAKAAN JENDELA ILMU');
                $sheet->mergeCells("A3:{$lastColumn}3");
                $sheet->setCellValue('A3', 'Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y'));
                
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
                    $sheet->getStyle("A{$dataStartRow}:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("H{$dataStartRow}:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    // Highlight guest rows
                    for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                        $typeCell = $sheet->getCell("D{$row}")->getValue();
                        if ($typeCell === 'Tamu') {
                            $sheet->getStyle("D{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => '7C3AED'], 'bold' => true],
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
                
                $totalVisits = Visit::whereBetween('visited_at', [$this->startDate, $this->endDate])->count();
                $studentVisits = Visit::whereBetween('visited_at', [$this->startDate, $this->endDate])
                    ->where('visitor_type', 'student')->count();
                $guestVisits = Visit::whereBetween('visited_at', [$this->startDate, $this->endDate])
                    ->where('visitor_type', 'guest')->count();
                $uniqueVisitors = Visit::whereBetween('visited_at', [$this->startDate, $this->endDate])
                    ->where('visitor_type', 'student')
                    ->distinct('student_nis')
                    ->count('student_nis');
                
                $summaryData = [
                    ['Total Kunjungan', $totalVisits],
                    ['Kunjungan Siswa', $studentVisits],
                    ['Kunjungan Tamu', $guestVisits],
                    ['Siswa Unik', $uniqueVisitors],
                    ['Tanggal Export', now()->timezone('Asia/Jakarta')->format('d F Y H:i') . ' WIB'],
                ];
                
                foreach ($summaryData as $i => $item) {
                    $r = $summaryRow + 1 + $i;
                    $sheet->setCellValue("A{$r}", $item[0]);
                    $sheet->setCellValue("C{$r}", ': ' . $item[1]);
                    $sheet->getStyle("A{$r}")->getFont()->setBold(true);
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
