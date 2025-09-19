<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('sidlak_journals', function (Blueprint $table) {
            $table->string('print_issn')->nullable()->after('cover_photo');
        });
    }

    public function down() {
        Schema::table('sidlak_journals', function (Blueprint $table) {
            $table->dropColumn('print_issn');
        });
    }
};
