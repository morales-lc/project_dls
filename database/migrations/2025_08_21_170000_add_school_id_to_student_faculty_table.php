<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('student_faculty', function (Blueprint $table) {
            $table->string('school_id')->nullable()->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('student_faculty', function (Blueprint $table) {
            $table->dropColumn('school_id');
        });
    }
};
