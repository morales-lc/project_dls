<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lira_requests', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('catalog_issn');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null')->after('status');
            $table->timestamp('processed_at')->nullable()->after('processed_by');
        });
    }

    public function down()
    {
        Schema::table('lira_requests', function (Blueprint $table) {
            $table->dropColumn(['status', 'processed_by', 'processed_at']);
        });
    }
};
