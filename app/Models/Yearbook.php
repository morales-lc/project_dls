<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Yearbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'year',
        'pdf_file',
    ];

    protected $casts = [
        'year' => 'integer',
    ];
}
