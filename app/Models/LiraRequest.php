<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Catalog;

class LiraRequest extends Model
{
    use HasFactory;

    protected $table = 'lira_requests';

    protected $fillable = [
        'user_id', 'consent', 'first_name', 'middle_name', 'last_name', 'email', 'program_strand_grade_level', 'designation', 'department', 'action', 'assistance_types', 'resource_types', 'titles_of', 'for_borrow_scan', 'for_list', 'for_videos',
        // decision/response fields
        'status', 'decision_reason', 'processed_by', 'processed_at',
        'response_subject', 'response_message', 'responded_by', 'response_sent_at',
        // circulation fields
        'catalog_id', 'loan_status', 'borrowed_at', 'borrowed_by', 'returned_at', 'returned_by'
    ];

    protected $casts = [
        'consent' => 'boolean',
        'assistance_types' => 'array',
        'resource_types' => 'array',
        'for_videos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'processed_at' => 'datetime',
        'response_sent_at' => 'datetime',
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    // simple status enum: pending, accepted, rejected
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isBorrowed()
    {
        return $this->loan_status === 'borrowed';
    }

    public function catalog()
    {
        return $this->belongsTo(Catalog::class, 'catalog_id');
    }
}
