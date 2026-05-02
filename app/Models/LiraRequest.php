<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'catalog_id', 'loan_status', 'borrowed_at', 'borrowed_by', 'returned_at', 'returned_by', 'return_due_date'
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
        'return_due_date' => 'date',
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

    public function isSuccessfulFulfillment(): bool
    {
        return $this->status === 'accepted'
            && (!is_null($this->response_sent_at) || in_array($this->loan_status, ['borrowed', 'returned'], true));
    }

    public function scopeBorrowHistory(Builder $query): Builder
    {
        return $query->whereIn('action', ['borrow', 'scanning']);
    }

    public function scopeSuccessfulFulfillment(Builder $query): Builder
    {
        return $query
            ->where('status', 'accepted')
            ->where(function (Builder $fulfilledQuery) {
                $fulfilledQuery
                    ->whereNotNull('response_sent_at')
                    ->orWhereIn('loan_status', ['borrowed', 'returned']);
            });
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->whereIn('status', ['rejected', 'canceled']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class, 'catalog_id');
    }
}
