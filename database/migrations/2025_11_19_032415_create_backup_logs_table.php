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
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // full, database, files
            $table->string('status'); // success, failed
            $table->string('filename')->nullable();
            $table->decimal('file_size_mb', 10, 2)->nullable();
            $table->text('output')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};
