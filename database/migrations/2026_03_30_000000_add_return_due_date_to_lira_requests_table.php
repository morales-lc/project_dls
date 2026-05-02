<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lira_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('lira_requests', 'return_due_date')) {
                $table->date('return_due_date')->nullable()->after('returned_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lira_requests', function (Blueprint $table) {
            if (Schema::hasColumn('lira_requests', 'return_due_date')) {
                $table->dropColumn('return_due_date');
            }
        });
    }
};