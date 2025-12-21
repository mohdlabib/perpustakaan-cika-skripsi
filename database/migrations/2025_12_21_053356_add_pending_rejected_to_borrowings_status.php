<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify enum to include 'pending' and 'rejected'
        DB::statement("ALTER TABLE borrowings MODIFY COLUMN status ENUM('pending', 'borrowed', 'returned', 'overdue', 'rejected') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE borrowings MODIFY COLUMN status ENUM('borrowed', 'returned', 'overdue') DEFAULT 'borrowed'");
    }
};
