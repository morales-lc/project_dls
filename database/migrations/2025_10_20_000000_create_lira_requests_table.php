<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lira_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('consent')->default(false);
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('program_strand_grade_level')->nullable();
            $table->string('designation')->nullable();
            $table->string('department')->nullable();
            $table->string('action')->nullable(); // borrow or scanning
            $table->json('assistance_types')->nullable(); // Document Delivery, Library Scanning, Book Borrowing
            $table->json('resource_types')->nullable(); // eBooks, Books, eBook Chapter, eJournals, Videos, List of References
            $table->text('titles_of')->nullable();
            $table->text('example_purposive')->nullable();
            $table->text('for_list')->nullable();
            $table->text('for_videos')->nullable();
            // catalog metadata for prefill
            $table->string('catalog_title')->nullable();
            $table->string('catalog_author')->nullable();
            $table->string('catalog_call_number')->nullable();
            $table->string('catalog_isbn')->nullable();
            $table->string('catalog_lccn')->nullable();
            $table->string('catalog_issn')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lira_requests');
    }
};
