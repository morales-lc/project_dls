<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CartItem extends Model
{
    protected $table = 'cart_items';

    protected $fillable = [
        'student_faculty_id',
        'cartable_id',
        'cartable_type',
    ];

    public function studentFaculty(): BelongsTo
    {
        return $this->belongsTo(StudentFaculty::class);
    }

    public function cartable(): MorphTo
    {
        return $this->morphTo();
    }
}
