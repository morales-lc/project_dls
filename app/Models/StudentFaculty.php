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
        // 'username' is no longer an auth field; keep for display only, but do not mass-assign
        // 'password' removed; passwords live only on users table
        'course',
        'yrlvl',
        'program_id',
        'birthdate',
        'role',
        'profile_picture',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
