<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceView extends Model
{
    protected $table = 'resource_views';

    protected $fillable = [
        'student_faculty_id',
        'document_type',
        'document_id',
        'program_id',
        'course',
        'role',
        'action',
        'search_term',
    ];

    public function studentFaculty()
    {
        return $this->belongsTo(StudentFaculty::class, 'student_faculty_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
