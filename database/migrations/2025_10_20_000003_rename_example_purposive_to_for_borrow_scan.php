<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lira_requests', function (Blueprint $table) {
            if (Schema::hasColumn('lira_requests', 'example_purposive')) {
                $table->renameColumn('example_purposive', 'for_borrow_scan');
            }
        });
    }

    public function down()
    {
        Schema::table('lira_requests', function (Blueprint $table) {
            if (Schema::hasColumn('lira_requests', 'for_borrow_scan')) {
                $table->renameColumn('for_borrow_scan', 'example_purposive');
            }
        });
    }
};
