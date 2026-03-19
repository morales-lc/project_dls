<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mides_documents', function (Blueprint $table) {
            $table->text('advisors')->nullable()->after('author');
            $table->date('publication_date')->nullable()->after('year');
            $table->text('tags')->nullable()->after('title');
        });

        // Backfill publication_date using existing year data for older records.
        DB::table('mides_documents')
            ->whereNull('publication_date')
            ->whereNotNull('year')
            ->orderBy('id')
            ->chunkById(100, function ($documents) {
                foreach ($documents as $document) {
                    DB::table('mides_documents')
                        ->where('id', $document->id)
                        ->update([
                            'publication_date' => sprintf('%04d-01-01', (int) $document->year),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('mides_documents', function (Blueprint $table) {
            $table->dropColumn(['advisors', 'publication_date', 'tags']);
        });
    }
};
