<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'description',
        'photo',
        'youtube_link',
            'website_link',
            'og_image',
        ];
}
