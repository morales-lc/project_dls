<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BackupSchedule extends Model
{
    protected $fillable = [
        'enabled',
        'frequency',
        'backup_type',
        'scheduled_time',
        'retention_count',
        'last_run_at',
        'next_run_at',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'retention_count' => 'integer',
    ];

    /**
     * Get backup logs for this schedule
     */
    public function backupLogs()
    {
        return $this->hasMany(BackupLog::class, 'schedule_id');
    }

    /**
     * Calculate next run time based on frequency
     */
    public function calculateNextRun(): Carbon
    {
        $now = Carbon::now();
        $time = Carbon::parse($this->scheduled_time);
        
        return match ($this->frequency) {
            'daily' => $now->copy()->setTimeFrom($time)->addDay(),
            'weekly' => $now->copy()->setTimeFrom($time)->addWeek(),
            'monthly' => $now->copy()->setTimeFrom($time)->addMonth(),
            default => $now->copy()->addDay(),
        };
    }

    /**
     * Check if schedule should run now
     */
    public function shouldRun(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        if (!$this->next_run_at) {
            return true; // First run
        }

        return Carbon::now()->greaterThanOrEqualTo($this->next_run_at);
    }

    /**
     * Update last run and calculate next run
     */
    public function markAsRun(): void
    {
        $this->update([
            'last_run_at' => Carbon::now(),
            'next_run_at' => $this->calculateNextRun(),
        ]);
    }
}
