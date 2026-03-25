<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserLoginLog extends Model
{
    protected $table = 'user_login_logs';

    protected $fillable = [
        'user_id',
        'student_faculty_id',
        'program_id',
        'course',
        'role',
        'ip_address',
        'user_agent',
        'logged_in_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_in_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentFaculty()
    {
        return $this->belongsTo(StudentFaculty::class, 'student_faculty_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public static function recordForUser(User $user, ?Request $request = null): void
    {
        if (!in_array($user->role, ['student', 'faculty'], true)) {
            return;
        }

        $sf = $user->studentFaculty;

        self::create([
            'user_id' => $user->id,
            'student_faculty_id' => $sf?->id,
            'program_id' => $sf?->program_id,
            'course' => $sf?->course,
            'role' => $user->role,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'logged_in_at' => now(),
        ]);
    }
}
