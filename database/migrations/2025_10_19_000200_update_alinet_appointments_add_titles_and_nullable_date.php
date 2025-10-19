<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add new column titles_or_topics
        Schema::table('alinet_appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('alinet_appointments', 'titles_or_topics')) {
                $table->text('titles_or_topics')->nullable()->after('institution_college');
            }
        });

        // Make appointment_date nullable (use raw SQL to avoid requiring doctrine/dbal)
        if (Schema::hasColumn('alinet_appointments', 'appointment_date')) {
            // Works for MySQL/MariaDB
            try {
                DB::statement('ALTER TABLE alinet_appointments MODIFY appointment_date DATE NULL');
            } catch (\Throwable $e) {
                // Fallback for PostgreSQL or other drivers: attempt a generic alter
                try {
                    DB::statement('ALTER TABLE alinet_appointments ALTER COLUMN appointment_date DROP NOT NULL');
                } catch (\Throwable $e2) {
                    // If both fail, leave as-is; Online mode will not set date but controller can avoid using it
                }
            }
        }
    }

    public function down(): void
    {
        // Revert appointment_date to NOT NULL (best-effort): set any NULLs to current date first
        if (Schema::hasColumn('alinet_appointments', 'appointment_date')) {
            try {
                // Fill nulls with today to satisfy NOT NULL constraint on rollback
                DB::table('alinet_appointments')->whereNull('appointment_date')->update([
                    'appointment_date' => now()->toDateString(),
                ]);
                DB::statement('ALTER TABLE alinet_appointments MODIFY appointment_date DATE NOT NULL');
            } catch (\Throwable $e) {
                try {
                    DB::statement('ALTER TABLE alinet_appointments ALTER COLUMN appointment_date SET NOT NULL');
                } catch (\Throwable $e2) {
                    // ignore
                }
            }
        }

        Schema::table('alinet_appointments', function (Blueprint $table) {
            if (Schema::hasColumn('alinet_appointments', 'titles_or_topics')) {
                $table->dropColumn('titles_or_topics');
            }
        });
    }
};
