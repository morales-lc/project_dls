<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';

    protected $fillable = [
        'title', 'authors', 'description', 'call_number', 'sublocation', 'published', 'copyright', 'format',
        'content_type', 'media_type', 'carrier_type', 'issn', 'isbn', 'lccn', 'barcode', 'status', 'additional_info'
    ];
}
