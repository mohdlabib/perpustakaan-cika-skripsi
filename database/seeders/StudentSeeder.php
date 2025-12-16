<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            ['nis' => '2024001', 'name' => 'Ahmad Fadillah', 'class' => 'XII IPA 1', 'phone' => '081234567890'],
            ['nis' => '2024002', 'name' => 'Siti Nurhaliza', 'class' => 'XII IPA 1', 'phone' => '081234567891'],
            ['nis' => '2024003', 'name' => 'Budi Santoso', 'class' => 'XII IPA 2', 'phone' => '081234567892'],
            ['nis' => '2024004', 'name' => 'Dewi Lestari', 'class' => 'XII IPS 1', 'phone' => '081234567893'],
            ['nis' => '2024005', 'name' => 'Rizki Pratama', 'class' => 'XII IPS 1', 'phone' => '081234567894'],
            ['nis' => '2024006', 'name' => 'Anisa Rahma', 'class' => 'XI IPA 1', 'phone' => '081234567895'],
            ['nis' => '2024007', 'name' => 'Muhammad Hafiz', 'class' => 'XI IPA 1', 'phone' => '081234567896'],
            ['nis' => '2024008', 'name' => 'Putri Ayu', 'class' => 'XI IPA 2', 'phone' => '081234567897'],
            ['nis' => '2024009', 'name' => 'Dimas Putra', 'class' => 'XI IPS 1', 'phone' => '081234567898'],
            ['nis' => '2024010', 'name' => 'Rina Wulandari', 'class' => 'XI IPS 1', 'phone' => '081234567899'],
            ['nis' => '2024011', 'name' => 'Farhan Akbar', 'class' => 'X IPA 1', 'phone' => '081234567800'],
            ['nis' => '2024012', 'name' => 'Nabila Zahra', 'class' => 'X IPA 1', 'phone' => '081234567801'],
            ['nis' => '2024013', 'name' => 'Arif Rahman', 'class' => 'X IPA 2', 'phone' => '081234567802'],
            ['nis' => '2024014', 'name' => 'Lina Sari', 'class' => 'X IPS 1', 'phone' => '081234567803'],
            ['nis' => '2024015', 'name' => 'Yusuf Maulana', 'class' => 'X IPS 1', 'phone' => '081234567804'],
        ];

        foreach ($students as $student) {
            Student::create($student);
        }
    }
}
