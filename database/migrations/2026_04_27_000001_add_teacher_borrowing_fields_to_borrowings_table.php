<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->string('borrower_type', 10)->default('student')->after('id');
            $table->string('borrower_name')->nullable()->after('borrower_type');
            $table->string('borrower_info')->nullable()->after('borrower_name');
        });

        // Make student_nis nullable for teacher borrowings
        Schema::table('borrowings', function (Blueprint $table) {
            $table->string('student_nis', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['borrower_type', 'borrower_name', 'borrower_info']);
        });

        Schema::table('borrowings', function (Blueprint $table) {
            $table->string('student_nis', 20)->nullable(false)->change();
        });
    }
};
