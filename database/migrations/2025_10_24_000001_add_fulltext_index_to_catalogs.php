<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('catalogs')) {
            return;
        }
        try {
            Schema::table('catalogs', function (Blueprint $table) {
                // Laravel's fullText uses proper SQL for MySQL 5.7+/8 and MariaDB 10.2+ when supported
                try {
                    $table->fullText(['title', 'subjects', 'additional_details', 'author', 'publisher'], 'fulltext_catalog_search');
                } catch (\Throwable $e) {
                    // Fallback to raw SQL if grammar doesn't support fullText
                    DB::statement("ALTER TABLE `catalogs` ADD FULLTEXT `fulltext_catalog_search` (`title`, `subjects`, `additional_details`, `author`, `publisher`)");
                }
            });
        } catch (\Throwable $e) {
            // ignore if not supported
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('catalogs')) {
            return;
        }
        try {
            Schema::table('catalogs', function (Blueprint $table) {
                try {
                    $table->dropFullText('fulltext_catalog_search');
                } catch (\Throwable $e) {
                    try {
                        DB::statement("ALTER TABLE `catalogs` DROP INDEX `fulltext_catalog_search`");
                    } catch (\Throwable $e2) {
                        // ignore
                    }
                }
            });
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
