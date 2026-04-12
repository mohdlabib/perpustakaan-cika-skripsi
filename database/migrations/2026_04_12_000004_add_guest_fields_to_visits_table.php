<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add guest visitor support to visits table.
     */
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Make student_nis nullable for guest visitors
            $table->string('student_nis', 20)->nullable()->change();
            
            // Guest visitor fields
            $table->enum('visitor_type', ['student', 'guest'])->default('student')->after('id');
            $table->string('guest_name')->nullable()->after('student_nis');
            $table->string('guest_institution')->nullable()->after('guest_name');
            $table->string('guest_purpose')->nullable()->after('guest_institution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['visitor_type', 'guest_name', 'guest_institution', 'guest_purpose']);
        });
    }
};
