<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformationLiteracyPost extends Model
{
    protected $table = 'information_literacy_posts';
    protected $fillable = [
        'title',
        'description',
        'date_time',
        'facilitators',
        'type',
        'image',
    ];
}
