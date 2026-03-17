<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('sidlak_journal_editors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sidlak_journal_id')->constrained('sidlak_journals')->onDelete('cascade');
            $table->string('name');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('sidlak_journal_peer_reviewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sidlak_journal_id')->constrained('sidlak_journals')->onDelete('cascade');
            $table->string('name');
            $table->string('title');
            $table->string('institution');
            $table->string('city');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('sidlak_journal_peer_reviewers');
        Schema::dropIfExists('sidlak_journal_editors');
    }
};
