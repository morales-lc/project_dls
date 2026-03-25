<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_faculty_id')->nullable(false);
            $table->unsignedBigInteger('cartable_id');
            $table->string('cartable_type');
            $table->timestamps();

            $table->index(['student_faculty_id']);
            $table->index(['cartable_id', 'cartable_type']);

            $table->foreign('student_faculty_id')->references('id')->on('student_faculty')->onDelete('cascade');
            $table->unique(['student_faculty_id', 'cartable_id', 'cartable_type'], 'cart_items_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
