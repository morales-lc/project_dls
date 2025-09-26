<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('library_staff', function (Blueprint $table) {
            $table->string('middlename')->nullable()->after('first_name');
        });
    }
    public function down() {
        Schema::table('library_staff', function (Blueprint $table) {
            $table->dropColumn('middlename');
        });
    }
};
