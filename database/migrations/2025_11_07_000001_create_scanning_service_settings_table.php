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
        Schema::create('scanning_service_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Scanning Service');
            $table->json('images')->nullable();
            $table->json('steps')->nullable();
            $table->text('important_note')->nullable();
            $table->text('scanning_request_note')->nullable();
            $table->text('extract_limits')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scanning_service_settings');
    }
};
