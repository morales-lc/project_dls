<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MidesCategory extends Model
{
    protected $table = 'mides_categories';
    protected $fillable = ['type', 'name'];
}
