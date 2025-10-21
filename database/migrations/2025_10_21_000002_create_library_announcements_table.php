<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('library_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('text', 500);
            $table->unsignedInteger('position')->default(0); // for ordering
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_announcements');
    }
};
