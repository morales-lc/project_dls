<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('resource_views', 'search_term')) {
            Schema::table('resource_views', function (Blueprint $table) {
                $table->string('search_term', 191)->nullable()->after('action');
                $table->index('search_term');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('resource_views', 'search_term')) {
            Schema::table('resource_views', function (Blueprint $table) {
                $table->dropIndex(['search_term']);
                $table->dropColumn('search_term');
            });
        }
    }
};
