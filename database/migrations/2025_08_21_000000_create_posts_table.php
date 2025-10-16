<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Announcement, Event, etc.
            $table->string('title');
            $table->text('description')->nullable(); // New description field
            $table->string('photo')->nullable(); // Path to uploaded photo
            $table->string('youtube_link')->nullable(); // YouTube video URL
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
