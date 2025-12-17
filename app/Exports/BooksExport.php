<?php

namespace App\Exports;

use App\Models\Book;
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
        $query = Book::with(['category', 'borrowings']);
        
        if ($this->category) {
            $query->where('category_id', $this->category);
        }
        
        return $query->orderBy('title')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Item',
            'Judul Buku',
            'Pengarang',
            'Penerbit',
            'Tahun Terbit',
            'ISBN',
            'Kategori',
            'Lokasi Rak',
            'Stok Total',
            'Sedang Dipinjam',
            'Stok Tersedia',
            'Klasifikasi',
            'No. Panggil',
            'Harga',
        ];
    }

    public function map($book): array
    {
        $this->rowNumber++;
        $borrowed = $book->borrowings->where('status', 'borrowed')->count();
        
        return [
            $this->rowNumber,
            $book->item_code ?? '-',
            $book->title,
            $book->author,
            $book->publisher ?? '-',
            $book->publication_year ?? '-',
            $book->isbn ?? '-',
            $book->category->name ?? '-',
            $book->shelf_location ?? '-',
            $book->stock,
            $borrowed,
            $book->stock - $borrowed,
            $book->classification ?? '-',
            $book->call_number ?? '-',
            $book->price ? 'Rp ' . number_format($book->price, 0, ',', '.') : '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 12,  // Kode Item
            'C' => 40,  // Judul
            'D' => 25,  // Pengarang
            'E' => 20,  // Penerbit
            'F' => 12,  // Tahun
            'G' => 18,  // ISBN
            'H' => 18,  // Kategori
            'I' => 12,  // Lokasi
            'J' => 10,  // Stok Total
            'K' => 14,  // Dipinjam
            'L' => 12,  // Tersedia
            'M' => 12,  // Klasifikasi
            'N' => 12,  // No Panggil
            'O' => 15,  // Harga
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
        return 'Laporan Buku';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = 'O';
                
                // Add title header
                $sheet->insertNewRowBefore(1, 4);
                $sheet->mergeCells('A1:O1');
                $sheet->setCellValue('A1', 'LAPORAN DATA BUKU PERPUSTAKAAN');
                $sheet->mergeCells('A2:O2');
                $sheet->setCellValue('A2', 'PERPUSTAKAAN SMAN 8 PEKANBARU');
                $sheet->mergeCells('A3:O3');
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
                $sheet->getStyle('F5:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J5:L' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Add summary at bottom
                $summaryRow = $lastRow + 2;
                $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN:');
                $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
                
                $totalBooks = Book::count();
                $totalStock = Book::sum('stock');
                $totalBorrowed = Borrowing::where('status', 'borrowed')->count();
                
                $sheet->setCellValue('A' . ($summaryRow + 1), 'Total Judul Buku: ' . $totalBooks);
                $sheet->setCellValue('A' . ($summaryRow + 2), 'Total Stok: ' . $totalStock);
                $sheet->setCellValue('A' . ($summaryRow + 3), 'Total Dipinjam: ' . $totalBorrowed);
                $sheet->setCellValue('A' . ($summaryRow + 4), 'Total Tersedia: ' . ($totalStock - $totalBorrowed));
                
                // Freeze header row
                $sheet->freezePane('A6');
            },
        ];
    }
}
