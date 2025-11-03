<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarcImportLog extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
        'records_added',
        'records_updated',
        'records_deleted',
        'records_unchanged',
        'records_errors',
        'total_parsed',
        'deletion_enabled',
        'log_file_path',
        'summary',
    ];

    protected $casts = [
        'deletion_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
