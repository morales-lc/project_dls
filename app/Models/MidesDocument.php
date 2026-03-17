<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MidesDocument extends Model
{
    protected $table = 'mides_documents';
    protected $fillable = [
        'type',
        'category',
        'program',
        'mides_category_id',
        'author',
        'year',
        'title',
        'pdf_path',
    ];

    /**
     * The category/program this document belongs to (nullable).
     */
    public function midesCategory()
    {
        return $this->belongsTo(MidesCategory::class, 'mides_category_id');
    }
}
