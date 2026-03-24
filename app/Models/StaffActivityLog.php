<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffActivityLog extends Model
{
    protected $table = 'staff_activity_logs';

    protected $fillable = [
        'user_id',
        'role',
        'method',
        'path',
        'route_name',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'status_code',
        'ip_address',
        'user_agent',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'subject_id' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
