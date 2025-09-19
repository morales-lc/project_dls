<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable(); // e.g. 'program' or 'special category'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_departments');
    }
};
