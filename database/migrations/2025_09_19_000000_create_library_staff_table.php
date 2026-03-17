<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('library_staff', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 10); // Mr., Ms., Mrs., Dr., etc.
            $table->string('first_name');
            $table->string('last_name');
            $table->string('role');
            $table->string('email');
            $table->string('photo')->nullable();
            $table->text('description')->nullable();
            $table->enum('department', ['college', 'graduate', 'senior_high', 'ibed']);
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('library_staff');
    }
};
