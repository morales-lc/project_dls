<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('student_faculty', function (Blueprint $table) {
            if (!Schema::hasColumn('student_faculty', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('student_faculty', function (Blueprint $table) {
            if (Schema::hasColumn('student_faculty', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
