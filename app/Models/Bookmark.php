<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Bookmark extends Model
{
    protected $table = 'bookmarks';

    protected $fillable = [
        'student_faculty_id',
        'bookmarkable_id',
        'bookmarkable_type',
    ];

    public function studentFaculty(): BelongsTo
    {
        return $this->belongsTo(StudentFaculty::class);
    }

    public function bookmarkable(): MorphTo
    {
        return $this->morphTo();
    }
}
