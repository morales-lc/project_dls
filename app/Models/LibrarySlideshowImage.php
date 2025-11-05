<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibrarySlideshowImage extends Model
{
    protected $fillable = [
        'image_path',
        'caption',
        'position',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Scope to get only active images ordered by position
     */
    public function scopeActive($query)
    {
        return $query->where('active', true)->orderBy('position');
    }

    /**
     * Scope to order by position
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}
