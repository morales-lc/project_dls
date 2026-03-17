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
        Schema::create('resource_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_faculty_id')->nullable()->index();
            $table->string('document_type', 50); // mides, sidlak, etc.
            $table->unsignedBigInteger('document_id')->nullable()->index();
            $table->unsignedBigInteger('program_id')->nullable()->index();
            $table->string('course')->nullable()->index();
            $table->string('role')->nullable(); // student or faculty
            $table->string('action')->default('view'); // view or download
            $table->timestamps();

            $table->foreign('student_faculty_id')->references('id')->on('student_faculty')->onDelete('set null');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_views');
    }
};
