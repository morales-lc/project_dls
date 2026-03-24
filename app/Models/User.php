<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'contact_number',
        'address',
        'password',
        'guest_plain_password',
        'guest_expires_at',
        'guest_account_status',
        'role',
        'login_otp',
        'login_otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'guest_plain_password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'guest_expires_at' => 'datetime',
            'login_otp_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function studentFaculty()
{
    return $this->hasOne(StudentFaculty::class);
}

    public function loginLogs()
    {
        return $this->hasMany(UserLoginLog::class);
    }
}
