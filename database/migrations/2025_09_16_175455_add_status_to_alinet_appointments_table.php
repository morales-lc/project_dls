<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up()
    {
        if (!Schema::hasColumn('alinet_appointments', 'status')) {
            Schema::table('alinet_appointments', function (Blueprint $table) {
                $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('alinet_appointments', 'status')) {
            Schema::table('alinet_appointments', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
