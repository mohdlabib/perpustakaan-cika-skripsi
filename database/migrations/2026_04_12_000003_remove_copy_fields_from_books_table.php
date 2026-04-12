<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Remove columns that now live in book_copies
            if (Schema::hasColumn('books', 'item_code')) {
                $table->dropColumn('item_code');
            }
            if (Schema::hasColumn('books', 'inventory_code')) {
                $table->dropColumn('inventory_code');
            }
            if (Schema::hasColumn('books', 'shelf_location')) {
                $table->dropColumn('shelf_location');
            }
            if (Schema::hasColumn('books', 'received_date')) {
                $table->dropColumn('received_date');
            }
            if (Schema::hasColumn('books', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('books', 'stock')) {
                $table->dropColumn('stock');
            }
        });

        // Drop foreign keys separately to avoid issues
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'shelf_id')) {
                $table->dropForeign(['shelf_id']);
                $table->dropColumn('shelf_id');
            }
        });

        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'shelf_column_id')) {
                $table->dropForeign(['shelf_column_id']);
                $table->dropColumn('shelf_column_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('item_code')->nullable();
            $table->string('inventory_code')->nullable();
            $table->foreignId('shelf_id')->nullable()->constrained();
            $table->foreignId('shelf_column_id')->nullable()->constrained();
            $table->string('shelf_location')->nullable();
            $table->date('received_date')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->integer('stock')->default(0);
        });
    }
};
