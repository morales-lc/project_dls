<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('sidlak_journals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('month');
            $table->year('year');
            $table->string('cover_photo')->nullable();
            $table->timestamps();
        });

        Schema::create('sidlak_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sidlak_journal_id')->constrained('sidlak_journals')->onDelete('cascade');
            $table->string('title');
            $table->string('authors');
            $table->string('pdf_file');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('sidlak_articles');
        Schema::dropIfExists('sidlak_journals');
    }
};
