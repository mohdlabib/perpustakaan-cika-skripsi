<?php

namespace App\Exports;

use App\Models\Book;
use App\Models\BookCopy;
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

class BooksExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $category;
    protected $rowNumber = 0;

    public function __construct($category = null)
    {
        $this->category = $category;
    }

    public function collection()
    {
        $query = Book::with(['category', 'copies', 'borrowings']);
        
        if ($this->category) {
            $query->where('category_id', $this->category);
        }
        
        return $query->orderBy('title')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Judul Buku',
            'Pengarang',
            'ISBN',
            'Penerbit',
            'Tahun Terbit',
            'Kategori',
            'Klasifikasi',
            'No. Panggil',
            'Edisi',
            'Total Eksemplar',
            'Tersedia',
            'Dipinjam',
            'Rusak/Hilang',
        ];
    }

    public function map($book): array
    {
        $this->rowNumber++;
        $totalCopies = $book->copies->count();
        $borrowed = $book->borrowings->where('status', 'borrowed')->count();
        $damagedLost = $book->copies->whereIn('condition', ['rusak', 'hilang'])->count();
        $available = $book->copies->where('condition', 'baik')
            ->where('is_available', true)->count() - $borrowed;
        
        return [
            $this->rowNumber,
            $book->title,
            $book->author,
            $book->isbn ?? '-',
            $book->publisher ?? '-',
            $book->publication_year ?? '-',
            $book->category->name ?? '-',
            $book->classification ?? '-',
            $book->call_number ?? '-',
            $book->edition ?? '-',
            $totalCopies,
            max(0, $available),
            $borrowed,
            $damagedLost,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 40,  // Judul
            'C' => 25,  // Pengarang
            'D' => 18,  // ISBN
            'E' => 20,  // Penerbit
            'F' => 12,  // Tahun
            'G' => 18,  // Kategori
            'H' => 12,  // Klasifikasi
            'I' => 14,  // No Panggil
            'J' => 15,  // Edisi
            'K' => 14,  // Total
            'L' => 12,  // Tersedia
            'M' => 12,  // Dipinjam
            'N' => 14,  // Rusak/Hilang
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styles applied in registerEvents after row insertion
        return [];
    }

    public function title(): string
    {
        return 'Laporan Buku';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'N';
                
                // Add title header (4 rows)
                $sheet->insertNewRowBefore(1, 4);
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', 'LAPORAN DATA BUKU PERPUSTAKAAN');
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
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '1B5E20'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(28);
                
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
                            'wrapText' => true,
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
                    
                    // Center align number/code columns
                    $sheet->getStyle("A{$dataStartRow}:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("F{$dataStartRow}:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("H{$dataStartRow}:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("K{$dataStartRow}:N{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    // Highlight "Tersedia = 0" cells in red
                    for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                        $available = $sheet->getCell("L{$row}")->getValue();
                        if ($available === 0 || $available === '0') {
                            $sheet->getStyle("L{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => 'DC2626'], 'bold' => true],
                            ]);
                        }
                        
                        // Highlight rusak/hilang > 0
                        $damaged = $sheet->getCell("N{$row}")->getValue();
                        if ($damaged > 0) {
                            $sheet->getStyle("N{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => 'EA580C'], 'bold' => true],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'FFF7ED'],
                                ],
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
                
                $totalBooks = Book::count();
                $totalCopies = BookCopy::where('condition', '!=', 'hilang')->count();
                $totalBorrowed = Borrowing::where('status', 'borrowed')->count();
                $totalDamaged = BookCopy::whereIn('condition', ['rusak', 'hilang'])->count();
                
                $summaryData = [
                    ['Total Judul Buku', $totalBooks],
                    ['Total Eksemplar', $totalCopies],
                    ['Total Dipinjam', $totalBorrowed],
                    ['Total Tersedia', max(0, $totalCopies - $totalBorrowed)],
                    ['Total Rusak/Hilang', $totalDamaged],
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
                $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.3)->setRight(0.3);
                
                // Freeze header row
                $sheet->freezePane('A6');
            },
        ];
    }
}
