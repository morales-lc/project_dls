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
        'barcode',
        'publisher',
        'year',
        'edition',
        'format',
        'lccn',
        'isbn',
        'issn',
        'series',
        'additional_info',
    ];
}
