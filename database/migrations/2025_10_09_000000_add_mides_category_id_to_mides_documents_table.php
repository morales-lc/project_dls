<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('mides_documents', function (Blueprint $table) {
            // Add nullable FK column for backward-compatible migration
            $table->unsignedBigInteger('mides_category_id')->nullable()->after('type');
            $table->foreign('mides_category_id')
                  ->references('id')
                  ->on('mides_categories')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('mides_documents', function (Blueprint $table) {
            $table->dropForeign(['mides_category_id']);
            $table->dropColumn('mides_category_id');
        });
    }
};
