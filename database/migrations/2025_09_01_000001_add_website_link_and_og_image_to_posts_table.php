<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('website_link')->nullable()->after('youtube_link');
            $table->string('og_image')->nullable()->after('website_link');
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['website_link', 'og_image']);
        });
    }
};
