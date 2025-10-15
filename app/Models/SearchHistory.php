<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    protected $table = 'search_histories';

    protected $fillable = [
        'student_faculty_id',
        'query',
        'results_count',
    ];

    public function studentFaculty()
    {
        return $this->belongsTo(StudentFaculty::class);
    }
}
