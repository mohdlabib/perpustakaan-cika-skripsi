<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->string('code'); // e.g., "A-01", "B-02"
            $table->string('name'); // e.g., "Rak Fiksi", "Rak Non-Fiksi"
            $table->string('location')->nullable(); // e.g., "Lantai 1", "Ruang Baca"
            $table->text('description')->nullable();
            $table->integer('capacity')->nullable(); // Kapasitas buku
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shelves');
    }
};
