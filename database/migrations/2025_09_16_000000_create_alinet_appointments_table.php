<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alinet_appointments', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 10)->nullable();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email');
            $table->string('strand_course')->nullable();
            $table->string('institution_college')->nullable();
            $table->date('appointment_date');
            $table->json('services');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alinet_appointments');
    }
};
