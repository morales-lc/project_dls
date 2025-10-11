<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MidesCategory extends Model
{
    protected $table = 'mides_categories';
    protected $fillable = ['type', 'name'];

    /**
     * Documents that belong to this category/program.
     */
    public function documents()
    {
        return $this->hasMany(MidesDocument::class, 'mides_category_id');
    }
}
