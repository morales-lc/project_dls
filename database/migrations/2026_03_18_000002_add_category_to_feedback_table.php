<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->string('category', 40)->default('general')->after('type');
            $table->index('category', 'feedback_category_idx');
        });

        DB::table('feedback')
            ->whereNull('category')
            ->update(['category' => 'general']);
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropIndex('feedback_category_idx');
            $table->dropColumn('category');
        });
    }
};
