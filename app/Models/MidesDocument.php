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
        'author',
        'year',
        'title',
        'pdf_path',
    ];
}
