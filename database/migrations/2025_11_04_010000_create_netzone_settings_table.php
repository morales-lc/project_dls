<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('netzone_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Netzone');
            $table->text('description')->nullable();
            $table->json('images')->nullable(); // Array of image paths
            $table->json('reminders')->nullable(); // Array of reminder objects with text and type
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('netzone_settings');
    }
};
