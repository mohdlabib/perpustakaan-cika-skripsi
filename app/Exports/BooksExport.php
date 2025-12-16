<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BooksExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * Get the collection of books.
     */
    public function collection()
    {
        return Book::with('category')->get();
    }

    /**
     * Define the headings.
     */
    public function headings(): array
    {
        return [
            'No',
            'ISBN',
            'Judul Buku',
            'Penulis',
            'Kategori',
            'Penerbit',
            'Tahun Terbit',
            'Lokasi Rak',
            'Stok',
            'Stok Tersedia',
        ];
    }

    /**
     * Map data for each row.
     */
    public function map($book): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $book->isbn ?? '-',
            $book->title,
            $book->author,
            $book->category->name ?? '-',
            $book->publisher ?? '-',
            $book->publication_year ?? '-',
            $book->shelf_location ?? '-',
            $book->stock,
            $book->available_stock,
        ];
    }

    /**
     * Style the worksheet.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
