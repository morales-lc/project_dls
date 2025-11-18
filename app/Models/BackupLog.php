<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    protected $fillable = [
        'type',
        'status',
        'filename',
        'file_size_mb',
        'output',
        'user_id',
    ];

    protected $casts = [
        'file_size_mb' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
