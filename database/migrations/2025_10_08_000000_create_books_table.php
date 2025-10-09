<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('authors')->nullable();
            $table->text('description')->nullable();
            $table->string('call_number')->nullable();
            $table->string('sublocation')->nullable();
            $table->string('published')->nullable();
            $table->string('copyright')->nullable();
            $table->string('format')->nullable();
            $table->string('content_type')->nullable();
            $table->string('media_type')->nullable();
            $table->string('carrier_type')->nullable();
            $table->string('issn')->nullable();
            $table->string('isbn')->nullable();
            $table->string('lccn')->nullable();
            $table->string('barcode')->nullable();
            $table->string('status')->default('Available');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
};
