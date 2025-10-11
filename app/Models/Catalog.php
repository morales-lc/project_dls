<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    protected $table = 'catalogs';

    protected $fillable = [
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
        'isbn',
        'issn',
        'lccn',
        'subjects',
        'additional_details',
    ];
}
