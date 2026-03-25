<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    protected $table = 'catalogs';

    protected $fillable = [
        'unique_key',
        'title',
        'author',
        'call_number',
        'sublocation',
        'publisher',
        'year',
        'edition',
        'format',
        'content_type',
        'media_type',
        'carrier_type',
        'copies_count',
        'borrowed_count',
        'isbn',
        'issn',
        'lccn',
        'subjects',
        'additional_details',
        'cover_image'
    ];
}
