<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alert_books', function (Blueprint $table) {
            $table->string('author')->nullable()->after('call_number');
        });
    }

    public function down(): void
    {
        Schema::table('alert_books', function (Blueprint $table) {
            $table->dropColumn('author');
        });
    }
};
