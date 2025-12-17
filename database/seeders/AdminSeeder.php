<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@perpustakaan.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'petugas@perpustakaan.test'],
            [
                'name' => 'Petugas Perpustakaan',
                'password' => Hash::make('password'),
            ]
        );
    }
}
