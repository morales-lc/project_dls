<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->string('title')->nullable()->after('user_id');
            $table->unsignedBigInteger('parent_id')->nullable()->after('title');
            $table->enum('type', ['thread', 'reply'])->default('thread')->after('parent_id');
            $table->enum('status', ['open', 'resolved', 'closed'])->default('open')->after('is_anonymous');

            $table->index(['type', 'created_at'], 'feedback_type_created_at_idx');
            $table->index(['parent_id', 'created_at'], 'feedback_parent_created_at_idx');
            $table->index('status', 'feedback_status_idx');

            $table->foreign('parent_id')
                ->references('id')
                ->on('feedback')
                ->nullOnDelete();
        });

        // Backfill existing rows as forum threads.
        DB::table('feedback')
            ->whereNull('type')
            ->update([
                'type' => 'thread',
                'status' => 'open',
            ]);

        // Give old records fallback titles so they appear as proper topics.
        DB::statement("UPDATE feedback SET title = CONCAT('Feedback #', id) WHERE title IS NULL OR title = ''");
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex('feedback_type_created_at_idx');
            $table->dropIndex('feedback_parent_created_at_idx');
            $table->dropIndex('feedback_status_idx');

            $table->dropColumn(['title', 'parent_id', 'type', 'status']);
        });
    }
};
