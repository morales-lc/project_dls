<?php

namespace App\Jobs;

use App\Models\BackupLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class RunManualBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public function __construct(public int $backupLogId)
    {
    }

    public function handle(): void
    {
        $log = BackupLog::find($this->backupLogId);
        if (!$log) {
            return;
        }

        $existingFiles = collect(Storage::disk('local')->files('Laravel'))
            ->filter(fn ($file) => preg_match('/\\.zip$/i', $file))
            ->all();

        $options = [];
        if ($log->type === 'database') {
            $options['--only-db'] = true;
        }
        if ($log->type === 'files') {
            $options['--only-files'] = true;
        }

        $exitCode = Artisan::call('backup:run', $options);
        $output = Artisan::output();

        $newFile = collect(Storage::disk('local')->files('Laravel'))
            ->filter(fn ($file) => preg_match('/\\.zip$/i', $file))
            ->diff($existingFiles)
            ->first();

        if ($exitCode === 0) {
            $log->status = 'success';
            $log->output = $output ?: 'Backup completed successfully.';

            if ($newFile) {
                $log->filename = basename($newFile);
                $sizeMb = Storage::disk('local')->size($newFile) / 1024 / 1024;
                $log->setAttribute('file_size_mb', round($sizeMb, 2));
            }

            $log->save();
            return;
        }

        $log->status = 'failed';
        $log->output = $output ?: 'Backup failed with no command output.';
        $log->save();
    }

    public function failed(\Throwable $exception): void
    {
        $log = BackupLog::find($this->backupLogId);
        if (!$log) {
            return;
        }

        $log->status = 'failed';
        $log->output = trim(($log->output ?? '') . "\n" . $exception->getMessage());
        $log->save();
    }
}
