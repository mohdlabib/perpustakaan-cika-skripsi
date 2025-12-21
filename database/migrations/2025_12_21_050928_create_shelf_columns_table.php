<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shelf_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelf_id')->constrained()->onDelete('cascade');
            $table->string('name', 50); // e.g., A, B, C or 1, 2, 3
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['shelf_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shelf_columns');
    }
};
