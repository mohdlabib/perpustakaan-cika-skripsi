<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students_registry', function (Blueprint $table) {
            $table->string('password')->nullable()->after('photo');
            $table->foreignId('grade_id')->nullable()->after('class')->constrained('grades')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students_registry', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropColumn(['password', 'grade_id']);
        });
    }
};
