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
    Schema::create('student_faculty', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('first_name')->nullable();
        $table->string('last_name')->nullable();
        $table->string('username')->nullable();
        $table->string('password')->nullable();
        $table->string('course')->nullable();
        $table->string('yrlvl')->nullable();
        $table->string('department')->nullable();
        $table->date('birthdate')->nullable();
        $table->enum('role', ['student', 'faculty'])->nullable();
        $table->string('profile_picture')->nullable(); // will store hashname
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_faculties');
    }
};
