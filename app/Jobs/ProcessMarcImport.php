<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use App\Models\MarcImportLog;

class ProcessMarcImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout for large imports
    public $tries = 1; // Don't retry failed imports automatically

    protected $filePath;
    protected $deleteMissing;
    protected $userId;
    protected $originalFilename;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $deleteMissing, $userId, $originalFilename)
    {
        $this->filePath = $filePath;
        $this->deleteMissing = $deleteMissing;
        $this->userId = $userId;
        $this->originalFilename = $originalFilename;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('MARC import job started', [
            'file' => $this->originalFilename,
            'user_id' => $this->userId,
            'delete_missing' => $this->deleteMissing
        ]);

        // Python script path
        $pythonScript = base_path('scripts/import_marc.py');

        // Get Python executable from environment or use platform defaults
        $pythonSpec = env('PYTHON_EXE');
        $cmd = [];
        if (!empty($pythonSpec)) {
            $cmd = preg_split('/\s+/', trim($pythonSpec));
        } else {
            $venvPythonWindows = base_path('.venv/Scripts/python.exe');
            $venvPythonUnix = base_path('.venv/bin/python');

            if (DIRECTORY_SEPARATOR === '\\' && file_exists($venvPythonWindows)) {
                $cmd = [$venvPythonWindows];
            } elseif (DIRECTORY_SEPARATOR !== '\\' && file_exists($venvPythonUnix)) {
                $cmd = [$venvPythonUnix];
            } elseif (DIRECTORY_SEPARATOR === '\\') {
                // Windows: prefer python on PATH (py launcher is not always available)
                $cmd = ['python'];
            } else {
                $cmd = ['python3'];
            }
        }

        $cmd[] = $pythonScript;
        $cmd[] = $this->filePath;
        if ($this->deleteMissing) {
            $cmd[] = '--delete-missing';
        }

        $process = new Process($cmd);

        // Provide environment variables
        $env = array_merge($_ENV, getenv() ?: [], [
            'SystemRoot' => getenv('SystemRoot') ?: (DIRECTORY_SEPARATOR === '\\' ? 'C:\\Windows' : '/'),
            'PATH' => getenv('PATH'),
            'PYTHONHASHSEED' => '0',
            'PYTHONUTF8' => '1',
            'PYTHONIOENCODING' => 'utf-8',
            'DB_HOST' => config('database.connections.mysql.host'),
            'DB_USERNAME' => config('database.connections.mysql.username'),
            'DB_PASSWORD' => config('database.connections.mysql.password'),
            'DB_DATABASE' => config('database.connections.mysql.database'),
        ]);
        $process->setEnv($env);

        try {
            $process->run(function ($type, $buffer) {
                if ($type === Process::ERR) {
                    Log::error('MARC import (stderr): ' . $buffer);
                } else {
                    Log::info('MARC import (stdout): ' . $buffer);
                }
            });
        } catch (\Throwable $e) {
            Log::error('MARC import process failed: ' . $e->getMessage());
            $this->cleanupFile();
            throw $e;
        }

        if (!$process->isSuccessful()) {
            $err = trim((string) $process->getErrorOutput());
            $stdout = trim((string) $process->getOutput());
            $combined = trim($err . (empty($stdout) ? '' : ("\n" . $stdout)));
            Log::error('MARC import python error: ' . $err);
            $this->cleanupFile();
            throw new \Exception('Import failed: ' . trim(substr($combined, 0, 1000)));
        }

        $output = $process->getOutput();

        // Parse summary
        $summary = null;
        try {
            $lines = preg_split("/\r?\n/", (string)$output) ?: [];
            for ($i = count($lines) - 1; $i >= 0; $i--) {
                $line = trim($lines[$i]);
                if (str_starts_with($line, 'IMPORT_SUMMARY:')) {
                    $json = substr($line, strlen('IMPORT_SUMMARY:'));
                    $dec = json_decode($json, true);
                    if (is_array($dec)) {
                        $summary = $dec;
                    }
                    break;
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Save import log
        if (is_array($summary) && $this->userId) {
            $user = \App\Models\User::find($this->userId);
            if ($user) {
                $logSummary = sprintf(
                    "Import completed successfully.\n\nFile: %s\nImported by: %s (%s)\n\nResults:\n• %d new records added\n• %d existing records updated\n• %d records unchanged\n• %d records deleted%s\n• %d errors encountered\n\nTotal records in file: %d\nUnique records: %d",
                    basename($summary['file'] ?? $this->originalFilename),
                    $user->name ?? $user->email,
                    $user->email,
                    (int)($summary['inserted'] ?? 0),
                    (int)($summary['updated'] ?? 0),
                    (int)($summary['unchanged'] ?? 0),
                    (int)($summary['deleted'] ?? 0),
                    $this->deleteMissing ? '' : ' (deletion was disabled)',
                    (int)($summary['errors'] ?? 0),
                    (int)($summary['parsed_records'] ?? 0),
                    (int)($summary['unique_keys'] ?? 0)
                );

                // Update log file with user info
                if (!empty($summary['log_file'])) {
                    $logFilePath = storage_path('logs/marc_imports/' . $summary['log_file']);
                    if (file_exists($logFilePath)) {
                        $logContent = file_get_contents($logFilePath);
                        $logContent = str_replace(
                            'Imported by: [Will be filled by Laravel]',
                            sprintf('Imported by: %s (%s)', $user->name ?? $user->email, $user->email),
                            $logContent
                        );
                        file_put_contents($logFilePath, $logContent);
                    }
                }

                try {
                    MarcImportLog::create([
                        'user_id' => $user->id,
                        'filename' => $this->originalFilename,
                        'records_added' => (int)($summary['inserted'] ?? 0),
                        'records_updated' => (int)($summary['updated'] ?? 0),
                        'records_deleted' => (int)($summary['deleted'] ?? 0),
                        'records_unchanged' => (int)($summary['unchanged'] ?? 0),
                        'records_errors' => (int)($summary['errors'] ?? 0),
                        'total_parsed' => (int)($summary['parsed_records'] ?? 0),
                        'deletion_enabled' => $this->deleteMissing,
                        'log_file_path' => $summary['log_file'] ?? null,
                        'summary' => $logSummary,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Failed to save import log: ' . $e->getMessage());
                }
            }
        }

        // Clean up uploaded file
        $this->cleanupFile();

        Log::info('MARC import job completed successfully', [
            'summary' => $summary
        ]);
    }

    /**
     * Clean up the uploaded MARC file
     */
    protected function cleanupFile(): void
    {
        try {
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
                Log::info('Deleted uploaded MARC file: ' . basename($this->filePath));
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to delete uploaded MARC file: ' . $e->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('MARC import job failed', [
            'file' => $this->originalFilename,
            'user_id' => $this->userId,
            'error' => $exception->getMessage()
        ]);

        $this->cleanupFile();
    }
}
