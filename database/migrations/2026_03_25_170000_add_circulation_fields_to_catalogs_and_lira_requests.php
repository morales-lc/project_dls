<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('catalogs', 'borrowed_count')) {
            Schema::table('catalogs', function (Blueprint $table) {
                $table->unsignedInteger('borrowed_count')->default(0)->after('copies_count');
            });
        }

        Schema::table('lira_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('lira_requests', 'catalog_id')) {
                $table->unsignedBigInteger('catalog_id')->nullable()->after('action');
                $table->index('catalog_id');
            }
            if (!Schema::hasColumn('lira_requests', 'loan_status')) {
                $table->string('loan_status', 20)->nullable()->after('response_sent_at');
            }
            if (!Schema::hasColumn('lira_requests', 'borrowed_at')) {
                $table->timestamp('borrowed_at')->nullable()->after('loan_status');
            }
            if (!Schema::hasColumn('lira_requests', 'borrowed_by')) {
                $table->foreignId('borrowed_by')->nullable()->constrained('users')->nullOnDelete()->after('borrowed_at');
            }
            if (!Schema::hasColumn('lira_requests', 'returned_at')) {
                $table->timestamp('returned_at')->nullable()->after('borrowed_by');
            }
            if (!Schema::hasColumn('lira_requests', 'returned_by')) {
                $table->foreignId('returned_by')->nullable()->constrained('users')->nullOnDelete()->after('returned_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lira_requests', function (Blueprint $table) {
            if (Schema::hasColumn('lira_requests', 'returned_by')) {
                $table->dropConstrainedForeignId('returned_by');
            }
            if (Schema::hasColumn('lira_requests', 'returned_at')) {
                $table->dropColumn('returned_at');
            }
            if (Schema::hasColumn('lira_requests', 'borrowed_by')) {
                $table->dropConstrainedForeignId('borrowed_by');
            }
            if (Schema::hasColumn('lira_requests', 'borrowed_at')) {
                $table->dropColumn('borrowed_at');
            }
            if (Schema::hasColumn('lira_requests', 'loan_status')) {
                $table->dropColumn('loan_status');
            }
            if (Schema::hasColumn('lira_requests', 'catalog_id')) {
                $table->dropIndex(['catalog_id']);
                $table->dropColumn('catalog_id');
            }
        });

        Schema::table('catalogs', function (Blueprint $table) {
            if (Schema::hasColumn('catalogs', 'borrowed_count')) {
                $table->dropColumn('borrowed_count');
            }
        });
    }
};
