<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lira_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('lira_requests', 'decision_reason')) {
                $table->text('decision_reason')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('lira_requests', function (Blueprint $table) {
            if (Schema::hasColumn('lira_requests', 'decision_reason')) {
                $table->dropColumn('decision_reason');
            }
        });
    }
};
