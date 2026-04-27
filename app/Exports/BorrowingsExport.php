<?php

namespace App\Exports;

use App\Models\Borrowing;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BorrowingsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
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

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'J';

        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F6F3B'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // All data rows
            "A2:{$lastCol}{$lastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Header borders
            "A1:{$lastCol}1" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '166534'],
                    ],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Peminjaman';
    }
}
