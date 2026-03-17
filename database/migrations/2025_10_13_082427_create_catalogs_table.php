<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('unique_key')->unique();
            $table->text('title')->nullable();
            $table->text('author')->nullable();
            $table->string('call_number')->nullable();
            $table->string('sublocation')->nullable();
            $table->text('publisher')->nullable();
            $table->string('year')->nullable();
            $table->string('edition')->nullable();
            $table->text('format')->nullable();
            $table->string('content_type')->nullable();
            $table->string('media_type')->nullable();
            $table->string('carrier_type')->nullable();
            $table->text('isbn')->nullable();
            $table->text('issn')->nullable();
            $table->string('lccn')->nullable();
            $table->text('subjects')->nullable();
            $table->longText('additional_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogs');
    }
};
