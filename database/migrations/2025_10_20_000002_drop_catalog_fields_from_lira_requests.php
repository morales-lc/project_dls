<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lira_requests', function (Blueprint $table) {
            if (Schema::hasColumn('lira_requests', 'catalog_title')) {
                $table->dropColumn([
                    'catalog_title', 'catalog_author', 'catalog_call_number', 'catalog_isbn', 'catalog_lccn', 'catalog_issn'
                ]);
            }
        });
    }

    public function down()
    {
        Schema::table('lira_requests', function (Blueprint $table) {
            $table->string('catalog_title')->nullable()->after('for_videos');
            $table->string('catalog_author')->nullable()->after('catalog_title');
            $table->string('catalog_call_number')->nullable()->after('catalog_author');
            $table->string('catalog_isbn')->nullable()->after('catalog_call_number');
            $table->string('catalog_lccn')->nullable()->after('catalog_isbn');
            $table->string('catalog_issn')->nullable()->after('catalog_lccn');
        });
    }
};
