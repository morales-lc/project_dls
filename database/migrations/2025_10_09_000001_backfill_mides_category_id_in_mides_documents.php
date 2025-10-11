<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // For each document, try to find a matching category in mides_categories
        $documents = DB::table('mides_documents')->get();
        foreach ($documents as $doc) {
            $categoryId = null;

            // Prefer exact match on category name (for Graduate and Undergrad stored in category)
            if (!empty($doc->category)) {
                $cat = DB::table('mides_categories')
                    ->where('name', $doc->category)
                    ->first();
                if ($cat) {
                    $categoryId = $cat->id;
                }
            }

            // If no match and program column is used (Senior High), try finding by name
            if (!$categoryId && !empty($doc->program)) {
                $cat = DB::table('mides_categories')
                    ->where('name', $doc->program)
                    ->first();
                if ($cat) {
                    $categoryId = $cat->id;
                }
            }

            // As a last resort, try matching by type (use a blank-name row or first matching type)
            if (!$categoryId && !empty($doc->type)) {
                $cat = DB::table('mides_categories')
                    ->where('type', $doc->type)
                    ->first();
                if ($cat) {
                    $categoryId = $cat->id;
                }
            }

            if ($categoryId) {
                DB::table('mides_documents')
                    ->where('id', $doc->id)
                    ->update(['mides_category_id' => $categoryId]);
            }
        }
    }

    public function down()
    {
        DB::table('mides_documents')->update(['mides_category_id' => null]);
    }
};
