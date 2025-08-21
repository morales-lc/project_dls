<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('mides_documents', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Graduate, Faculty/Theses/Dissertations, Undergraduate, Senior High
            $table->string('category')->nullable(); // e.g. MAED-Childhood Education, Nursing, etc. (nullable for Faculty/Theses)
            $table->string('program')->nullable(); // For undergraduate programs (nullable for Faculty/Theses)
            $table->string('author');
            $table->year('year');
            $table->string('title');
            $table->string('pdf_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mides_documents');
    }
};
