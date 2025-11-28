<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BackupSchedule;
use App\Models\BackupLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class RunScheduledBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run scheduled backups and apply retention policies';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for scheduled backups...');

        $schedules = BackupSchedule::where('enabled', true)->get();

        if ($schedules->isEmpty()) {
            $this->info('No enabled schedules found.');
            return 0;
        }

        foreach ($schedules as $schedule) {
            if ($schedule->shouldRun()) {
                $this->info("Running {$schedule->frequency} backup (Type: {$schedule->backup_type})...");
                $this->runBackup($schedule);
            } else {
                $nextRun = $schedule->next_run_at ? $schedule->next_run_at->format('Y-m-d H:i:s') : 'Not scheduled';
                $this->info("Schedule #{$schedule->id} ({$schedule->frequency}) not due yet. Next run: {$nextRun}");
            }
        }

        return 0;
    }

    /**
     * Run a backup for the given schedule
     */
    protected function runBackup(BackupSchedule $schedule): void
    {
        // Get existing files before backup
        $existingFiles = collect(Storage::disk('local')->files('Laravel'))
            ->filter(fn($file) => preg_match('/\.zip$/i', $file))
            ->all();

        // Build backup command
        $command = match ($schedule->backup_type) {
            'database' => 'backup:run --only-db',
            'files' => 'backup:run --only-files',
            default => 'backup:run',
        };

        try {
            // Run the backup
            Artisan::call($command);
            $output = Artisan::output();

            // Check if successful
            $success = strpos($output, 'Backup completed!') !== false || 
                      strpos($output, 'Successfully copied zip') !== false;

            if ($success) {
                // Find new backup file
                $newFiles = collect(Storage::disk('local')->files('Laravel'))
                    ->filter(fn($file) => preg_match('/\.zip$/i', $file))
                    ->diff($existingFiles)
                    ->first();

                $filename = $newFiles ? basename($newFiles) : null;
                $fileSize = $newFiles ? Storage::disk('local')->size($newFiles) / 1024 / 1024 : null;

                // Log the backup
                BackupLog::create([
                    'type' => $schedule->backup_type,
                    'frequency' => $schedule->frequency,
                    'status' => 'success',
                    'filename' => $filename,
                    'file_size_mb' => $fileSize,
                    'output' => $output,
                    'user_id' => null, // System generated
                    'schedule_id' => $schedule->id,
                ]);

                $this->info("✓ Backup completed successfully: {$filename}");

                // Apply retention policy
                $this->applyRetentionPolicy($schedule);

                // Mark schedule as run
                $schedule->markAsRun();
            } else {
                // Log failure
                BackupLog::create([
                    'type' => $schedule->backup_type,
                    'frequency' => $schedule->frequency,
                    'status' => 'failed',
                    'filename' => null,
                    'file_size_mb' => null,
                    'output' => $output,
                    'user_id' => null,
                    'schedule_id' => $schedule->id,
                ]);

                $this->error("✗ Backup failed for schedule #{$schedule->id}");
            }
        } catch (\Exception $e) {
            $this->error("✗ Exception during backup: " . $e->getMessage());

            BackupLog::create([
                'type' => $schedule->backup_type,
                'frequency' => $schedule->frequency,
                'status' => 'failed',
                'filename' => null,
                'file_size_mb' => null,
                'output' => $e->getMessage(),
                'user_id' => null,
                'schedule_id' => $schedule->id,
            ]);
        }
    }

    /**
     * Apply retention policy - keep only the specified number of backups
     */
    protected function applyRetentionPolicy(BackupSchedule $schedule): void
    {
        $this->info("Applying retention policy (keep last {$schedule->retention_count} backups)...");

        // Get successful backups for this schedule, ordered by newest first
        $backups = BackupLog::where('schedule_id', $schedule->id)
            ->where('status', 'success')
            ->whereNotNull('filename')
            ->orderBy('created_at', 'desc')
            ->get();

        // Keep only the retention count, delete older ones
        $toDelete = $backups->skip($schedule->retention_count);

        foreach ($toDelete as $backup) {
            $filePath = 'Laravel/' . $backup->filename;
            
            if (Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->delete($filePath);
                $this->info("Deleted old backup: {$backup->filename}");
            }

            // Update log to indicate file was deleted
            $backup->update(['filename' => null]);
        }

        if ($toDelete->isEmpty()) {
            $this->info('No old backups to delete.');
        }
    }
}
