<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_faculty', function (Blueprint $table) {
            // Add program_id and make it nullable for existing rows
            $table->foreignId('program_id')->nullable()->after('yrlvl')->constrained('programs')->nullOnDelete();
            // Drop department column if exists
            if (Schema::hasColumn('student_faculty', 'department')) {
                $table->dropColumn('department');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_faculty', function (Blueprint $table) {
            // Re-create department column
            if (!Schema::hasColumn('student_faculty', 'department')) {
                $table->string('department')->nullable()->after('yrlvl');
            }
            // Drop program_id foreign key & column
            if (Schema::hasColumn('student_faculty', 'program_id')) {
                $table->dropConstrainedForeignId('program_id');
            }
        });
    }
};