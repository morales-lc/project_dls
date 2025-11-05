<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_space_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Learning Spaces');
            $table->text('description')->nullable();
            $table->json('images')->nullable(); // Array of image paths
            $table->json('content_sections')->nullable(); // Array of content sections (types, how to reserve, etc.)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_space_settings');
    }
};
