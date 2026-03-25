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
        Schema::table('staff_activity_logs', function (Blueprint $table) {
            $table->string('action', 40)->nullable()->after('route_name');
            $table->string('subject_type')->nullable()->after('action');
            $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');
            $table->string('description')->nullable()->after('subject_id');

            $table->index(['action', 'created_at']);
            $table->index(['subject_type', 'created_at']);
            $table->index('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_activity_logs', function (Blueprint $table) {
            $table->dropIndex(['action', 'created_at']);
            $table->dropIndex(['subject_type', 'created_at']);
            $table->dropIndex(['subject_id']);

            $table->dropColumn(['action', 'subject_type', 'subject_id', 'description']);
        });
    }
};
