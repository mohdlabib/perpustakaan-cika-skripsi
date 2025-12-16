<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fiksi',
                'slug' => 'fiksi',
                'description' => 'Novel, cerpen, cerita pendek, dan karya sastra fiksi lainnya',
                'icon' => '📚',
            ],
            [
                'name' => 'Non-Fiksi',
                'slug' => 'non-fiksi',
                'description' => 'Buku pengetahuan umum, biografi, dan sejarah',
                'icon' => '📖',
            ],
            [
                'name' => 'Sains & Teknologi',
                'slug' => 'sains-teknologi',
                'description' => 'Buku pelajaran IPA, matematika, fisika, kimia, dan teknologi',
                'icon' => '🔬',
            ],
            [
                'name' => 'Bahasa',
                'slug' => 'bahasa',
                'description' => 'Kamus, buku grammar, dan pembelajaran bahasa',
                'icon' => '🗣️',
            ],
            [
                'name' => 'Agama',
                'slug' => 'agama',
                'description' => 'Buku keagamaan dan spiritual',
                'icon' => '🕌',
            ],
            [
                'name' => 'Ensiklopedia',
                'slug' => 'ensiklopedia',
                'description' => 'Kumpulan pengetahuan dan referensi',
                'icon' => '📕',
            ],
            [
                'name' => 'Komik & Manga',
                'slug' => 'komik-manga',
                'description' => 'Buku komik dan manga untuk hiburan',
                'icon' => '🎨',
            ],
            [
                'name' => 'Sejarah',
                'slug' => 'sejarah',
                'description' => 'Buku tentang sejarah Indonesia dan dunia',
                'icon' => '🏛️',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
