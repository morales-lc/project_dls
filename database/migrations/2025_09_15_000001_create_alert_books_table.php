<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('pdf_path');
            $table->string('cover_image')->nullable();
            $table->unsignedBigInteger('department_id');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('alert_departments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_books');
    }
};
