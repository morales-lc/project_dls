<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    protected $fillable = [
        'type',
        'frequency',
        'status',
        'filename',
        'file_size_mb',
        'output',
        'user_id',
        'schedule_id',
    ];

    protected $casts = [
        'file_size_mb' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(BackupSchedule::class);
    }
}
