<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Backfill users.username from student_faculty.username where missing
        try {
            // Use a raw update with join when supported (MySQL)
            DB::statement('UPDATE users u JOIN student_faculty sf ON sf.user_id = u.id SET u.username = sf.username WHERE u.username IS NULL AND sf.username IS NOT NULL');
        } catch (\Throwable $e) {
            // Fallback: do it in chunks to be safe across drivers
            DB::table('student_faculty')
                ->whereNotNull('username')
                ->join('users', 'student_faculty.user_id', '=', 'users.id')
                ->whereNull('users.username')
                ->select('student_faculty.username', 'users.id as user_id')
                ->orderBy('users.id')
                ->chunkById(500, function ($rows) {
                    foreach ($rows as $row) {
                        DB::table('users')->where('id', $row->user_id)->update(['username' => $row->username]);
                    }
                }, 'user_id');
        }

        // Drop password column from student_faculty if it exists
        if (Schema::hasColumn('student_faculty', 'password')) {
            Schema::table('student_faculty', function (Blueprint $table) {
                $table->dropColumn('password');
            });
        }
    }

    public function down(): void
    {
        // Re-create the password column on student_faculty as nullable string
        if (!Schema::hasColumn('student_faculty', 'password')) {
            Schema::table('student_faculty', function (Blueprint $table) {
                $table->string('password')->nullable()->after('username');
            });
        }
        // No safe automatic rollback for username backfill
    }
};
