<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->foreignId('book_copy_id')->nullable()->after('book_id')
                  ->constrained('book_copies')->onDelete('set null');
        });

        // Link existing borrowings to their first copy
        $borrowings = DB::table('borrowings')->whereNull('book_copy_id')->get();
        foreach ($borrowings as $borrowing) {
            $copy = DB::table('book_copies')
                ->where('book_id', $borrowing->book_id)
                ->first();
            if ($copy) {
                DB::table('borrowings')
                    ->where('id', $borrowing->id)
                    ->update(['book_copy_id' => $copy->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropForeign(['book_copy_id']);
            $table->dropColumn('book_copy_id');
        });
    }
};
