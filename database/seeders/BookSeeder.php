<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            // Fiksi
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'category' => 'fiksi',
                'publisher' => 'Bentang Pustaka',
                'publication_year' => 2005,
                'stock' => 5,
                'description' => 'Novel tentang perjuangan anak-anak Belitung untuk meraih mimpi mereka.',
                'shelf_location' => 'A-01',
            ],
            [
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'category' => 'fiksi',
                'publisher' => 'Hasta Mitra',
                'publication_year' => 1980,
                'stock' => 3,
                'description' => 'Novel pertama dari Tetralogi Pulau Buru.',
                'shelf_location' => 'A-02',
            ],
            [
                'title' => 'Dilan 1990',
                'author' => 'Pidi Baiq',
                'category' => 'fiksi',
                'publisher' => 'Pastel Books',
                'publication_year' => 2014,
                'stock' => 4,
                'description' => 'Kisah cinta remaja SMA di Bandung tahun 1990.',
                'shelf_location' => 'A-03',
            ],
            [
                'title' => 'Perahu Kertas',
                'author' => 'Dewi Lestari',
                'category' => 'fiksi',
                'publisher' => 'Bentang Pustaka',
                'publication_year' => 2009,
                'stock' => 3,
                'description' => 'Novel tentang pencarian jati diri dan cinta.',
                'shelf_location' => 'A-04',
            ],
            
            // Sains & Teknologi
            [
                'title' => 'Fisika SMA Kelas XII',
                'author' => 'Marthen Kanginan',
                'category' => 'sains-teknologi',
                'publisher' => 'Erlangga',
                'publication_year' => 2022,
                'stock' => 10,
                'description' => 'Buku pelajaran fisika untuk kelas 12 SMA.',
                'shelf_location' => 'B-01',
            ],
            [
                'title' => 'Matematika Peminatan Kelas XI',
                'author' => 'Sukino',
                'category' => 'sains-teknologi',
                'publisher' => 'Erlangga',
                'publication_year' => 2021,
                'stock' => 8,
                'description' => 'Buku pelajaran matematika peminatan.',
                'shelf_location' => 'B-02',
            ],
            [
                'title' => 'Biologi Campbell',
                'author' => 'Neil A. Campbell',
                'category' => 'sains-teknologi',
                'publisher' => 'Erlangga',
                'publication_year' => 2020,
                'stock' => 5,
                'description' => 'Referensi biologi komprehensif.',
                'shelf_location' => 'B-03',
            ],
            
            // Bahasa
            [
                'title' => 'Kamus Besar Bahasa Indonesia',
                'author' => 'Tim Penyusun KBBI',
                'category' => 'bahasa',
                'publisher' => 'Balai Pustaka',
                'publication_year' => 2023,
                'stock' => 6,
                'description' => 'Kamus resmi bahasa Indonesia.',
                'shelf_location' => 'C-01',
            ],
            [
                'title' => 'English Grammar in Use',
                'author' => 'Raymond Murphy',
                'category' => 'bahasa',
                'publisher' => 'Cambridge',
                'publication_year' => 2019,
                'stock' => 4,
                'description' => 'Panduan grammar bahasa Inggris.',
                'shelf_location' => 'C-02',
            ],
            
            // Sejarah
            [
                'title' => 'Sejarah Indonesia Modern',
                'author' => 'M.C. Ricklefs',
                'category' => 'sejarah',
                'publisher' => 'Serambi',
                'publication_year' => 2018,
                'stock' => 3,
                'description' => 'Sejarah Indonesia dari masa kolonial hingga reformasi.',
                'shelf_location' => 'D-01',
            ],
            [
                'title' => 'Peradaban Nusantara',
                'author' => 'Sartono Kartodirdjo',
                'category' => 'sejarah',
                'publisher' => 'Gramedia',
                'publication_year' => 2020,
                'stock' => 4,
                'description' => 'Kajian tentang peradaban Indonesia kuno.',
                'shelf_location' => 'D-02',
            ],
            
            // Agama
            [
                'title' => 'Fiqih Islam',
                'author' => 'H. Sulaiman Rasjid',
                'category' => 'agama',
                'publisher' => 'Sinar Baru',
                'publication_year' => 2021,
                'stock' => 7,
                'description' => 'Buku panduan fiqih untuk muslim.',
                'shelf_location' => 'E-01',
            ],
            
            // Ensiklopedia
            [
                'title' => 'Ensiklopedia Indonesia',
                'author' => 'Tim Penyusun',
                'category' => 'ensiklopedia',
                'publisher' => 'PT Ichtiar Baru',
                'publication_year' => 2019,
                'stock' => 2,
                'description' => 'Ensiklopedia lengkap tentang Indonesia.',
                'shelf_location' => 'F-01',
            ],
            
            // Non-Fiksi
            [
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'category' => 'non-fiksi',
                'publisher' => 'Gramedia',
                'publication_year' => 2020,
                'stock' => 5,
                'description' => 'Cara membangun kebiasaan baik dan menghilangkan kebiasaan buruk.',
                'shelf_location' => 'G-01',
            ],
            [
                'title' => 'Rich Dad Poor Dad',
                'author' => 'Robert T. Kiyosaki',
                'category' => 'non-fiksi',
                'publisher' => 'Gramedia',
                'publication_year' => 2018,
                'stock' => 4,
                'description' => 'Pelajaran tentang keuangan yang tidak diajarkan di sekolah.',
                'shelf_location' => 'G-02',
            ],
            
            // Komik & Manga
            [
                'title' => 'One Piece Vol. 1',
                'author' => 'Eiichiro Oda',
                'category' => 'komik-manga',
                'publisher' => 'Elex Media Komputindo',
                'publication_year' => 2003,
                'stock' => 3,
                'description' => 'Petualangan Luffy menjadi raja bajak laut.',
                'shelf_location' => 'H-01',
            ],
            [
                'title' => 'Naruto Vol. 1',
                'author' => 'Masashi Kishimoto',
                'category' => 'komik-manga',
                'publisher' => 'Elex Media Komputindo',
                'publication_year' => 2004,
                'stock' => 3,
                'description' => 'Perjalanan Naruto menjadi ninja terkuat.',
                'shelf_location' => 'H-02',
            ],
            [
                'title' => 'Doraemon Vol. 1',
                'author' => 'Fujiko F. Fujio',
                'category' => 'komik-manga',
                'publisher' => 'Elex Media Komputindo',
                'publication_year' => 1999,
                'stock' => 4,
                'description' => 'Petualangan Doraemon dan Nobita.',
                'shelf_location' => 'H-03',
            ],
        ];

        foreach ($books as $book) {
            $categorySlug = $book['category'];
            unset($book['category']);
            
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $book['category_id'] = $category->id;
                Book::create($book);
            }
        }
    }
}
