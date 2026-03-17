<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('alinet_appointments', function (Blueprint $table) {
            $table->string('mode_of_research')->nullable()->after('email');
            $table->json('assistance')->nullable()->after('mode_of_research');
            $table->json('resource_types')->nullable()->after('assistance');
        });

        // Best-effort data migration: if services existed, try to map 'Scanning Service' etc.
        if (Schema::hasColumn('alinet_appointments', 'services')) {
            $rows = DB::table('alinet_appointments')->select('id', 'services')->get();
            foreach ($rows as $row) {
                $services = json_decode($row->services, true) ?: [];
                // naive mapping: treat all previous services as assistance; leave resource_types empty
                DB::table('alinet_appointments')->where('id', $row->id)->update([
                    'assistance' => json_encode($services),
                    'resource_types' => json_encode([]),
                ]);
            }

            Schema::table('alinet_appointments', function (Blueprint $table) {
                $table->dropColumn('services');
            });
        }
    }

    public function down(): void
    {
        Schema::table('alinet_appointments', function (Blueprint $table) {
            $table->json('services')->nullable()->after('appointment_date');
        });

        // Merge back assistance into services for rollback
        $rows = DB::table('alinet_appointments')->select('id', 'assistance')->get();
        foreach ($rows as $row) {
            $assistance = json_decode($row->assistance, true) ?: [];
            DB::table('alinet_appointments')->where('id', $row->id)->update([
                'services' => json_encode($assistance),
            ]);
        }

        Schema::table('alinet_appointments', function (Blueprint $table) {
            $table->dropColumn(['mode_of_research', 'assistance', 'resource_types']);
        });
    }
};
