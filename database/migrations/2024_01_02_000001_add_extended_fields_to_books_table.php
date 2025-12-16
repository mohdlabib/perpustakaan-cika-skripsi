<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('item_code')->nullable()->after('isbn'); // Kode Eksemplar
            $table->string('edition')->nullable()->after('author'); // Cetakan
            $table->string('publication_place')->nullable()->after('publication_year'); // Tempat Terbit
            $table->string('physical_description')->nullable()->after('description'); // Deskripsi Fisik
            $table->string('classification')->nullable()->after('physical_description'); // Klasifikasi
            $table->string('call_number')->nullable()->after('classification'); // Nomor Panggil
            $table->string('inventory_code')->nullable()->after('call_number'); // Nomor Inventaris
            $table->date('received_date')->nullable()->after('inventory_code'); // Tanggal Diterima
            $table->decimal('price', 12, 2)->nullable()->after('received_date'); // Harga Buku
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'item_code',
                'edition', 
                'publication_place',
                'physical_description',
                'classification',
                'call_number',
                'inventory_code',
                'received_date',
                'price'
            ]);
        });
    }
};
