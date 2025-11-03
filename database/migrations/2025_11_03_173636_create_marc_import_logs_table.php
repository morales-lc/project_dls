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
        Schema::create('marc_import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->integer('records_added')->default(0);
            $table->integer('records_updated')->default(0);
            $table->integer('records_deleted')->default(0);
            $table->integer('records_unchanged')->default(0);
            $table->integer('records_errors')->default(0);
            $table->integer('total_parsed')->default(0);
            $table->boolean('deletion_enabled')->default(false);
            $table->string('log_file_path')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marc_import_logs');
    }
};
