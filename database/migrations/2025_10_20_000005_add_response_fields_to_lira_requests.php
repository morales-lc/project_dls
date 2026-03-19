<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('lira_requests', 'response_subject')) {
            Schema::table('lira_requests', function (Blueprint $table) {
                $table->string('response_subject')->nullable()->after('decision_reason');
            });
        }

        if (!Schema::hasColumn('lira_requests', 'response_message')) {
            Schema::table('lira_requests', function (Blueprint $table) {
                $table->text('response_message')->nullable()->after('response_subject');
            });
        }

        if (!Schema::hasColumn('lira_requests', 'responded_by')) {
            Schema::table('lira_requests', function (Blueprint $table) {
                $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete()->after('response_message');
            });
        }

        if (!Schema::hasColumn('lira_requests', 'response_sent_at')) {
            Schema::table('lira_requests', function (Blueprint $table) {
                $table->timestamp('response_sent_at')->nullable()->after('responded_by');
            });
        }
    }

    public function down(): void
    {
        Schema::table('lira_requests', function (Blueprint $table) {
            if (Schema::hasColumn('lira_requests', 'response_sent_at')) {
                $table->dropColumn('response_sent_at');
            }
            if (Schema::hasColumn('lira_requests', 'responded_by')) {
                $table->dropConstrainedForeignId('responded_by');
            }
            if (Schema::hasColumn('lira_requests', 'response_message')) {
                $table->dropColumn('response_message');
            }
            if (Schema::hasColumn('lira_requests', 'response_subject')) {
                $table->dropColumn('response_subject');
            }
        });
    }
};
