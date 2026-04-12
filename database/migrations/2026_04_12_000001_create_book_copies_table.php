<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->string('copy_code')->nullable(); // Kode Eksemplar
            $table->string('inventory_code')->nullable(); // Nomor Inventaris
            $table->foreignId('shelf_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('shelf_column_id')->nullable()->constrained()->onDelete('set null');
            $table->string('shelf_location')->nullable();
            $table->enum('condition', ['baik', 'rusak', 'hilang'])->default('baik');
            $table->date('received_date')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['book_id', 'condition']);
            $table->index('copy_code');
            $table->index('inventory_code');
        });

        // Migrate existing data from books to book_copies
        $books = DB::table('books')->get();
        foreach ($books as $book) {
            $stock = max(1, $book->stock ?? 1);
            
            for ($i = 0; $i < $stock; $i++) {
                DB::table('book_copies')->insert([
                    'book_id' => $book->id,
                    'copy_code' => $i === 0 ? ($book->item_code ?? null) : null,
                    'inventory_code' => $i === 0 ? ($book->inventory_code ?? null) : null,
                    'shelf_id' => $book->shelf_id ?? null,
                    'shelf_column_id' => $book->shelf_column_id ?? null,
                    'shelf_location' => $book->shelf_location ?? null,
                    'condition' => 'baik',
                    'received_date' => $book->received_date ?? null,
                    'price' => $i === 0 ? ($book->price ?? null) : null,
                    'is_available' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
