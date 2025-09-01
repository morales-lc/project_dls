<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFaculty extends Model
{
    protected $table = 'student_faculty';
    protected $fillable = [
        'user_id',
        'school_id',
        'first_name',
        'last_name',
        'username',
        'password',
        'course',
        'yrlvl',
        'department',
        'birthdate',
        'role',
        'profile_picture',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
