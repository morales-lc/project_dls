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
        Schema::create('contact_infos', function (Blueprint $table) {
            $table->id();
            // Phone numbers per library
            $table->string('phone_college')->nullable();
            $table->string('phone_graduate')->nullable();
            $table->string('phone_senior_high')->nullable();
            $table->string('phone_ibed')->nullable();

            // Socials / contact links
            $table->string('facebook_url')->nullable();
            $table->string('email')->nullable();
            $table->string('website_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_infos');
    }
};
