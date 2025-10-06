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
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_faculty_id')->nullable(false);
            $table->unsignedBigInteger('bookmarkable_id');
            $table->string('bookmarkable_type');
            $table->timestamps();

            $table->index(['student_faculty_id']);
            $table->index(['bookmarkable_id', 'bookmarkable_type']);

            $table->foreign('student_faculty_id')->references('id')->on('student_faculty')->onDelete('cascade');
            $table->unique(['student_faculty_id', 'bookmarkable_id', 'bookmarkable_type'], 'bookmarks_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookmarks');
    }
};
