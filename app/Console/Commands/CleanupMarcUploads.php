<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupMarcUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marc:cleanup {--days=7 : Delete files older than this many days} {--all : Delete all MARC upload files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old MARC upload files from storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $uploadPath = storage_path('app/private/marc_uploads');
        
        if (!is_dir($uploadPath)) {
            $this->info('Upload directory does not exist. Nothing to clean.');
            return 0;
        }

        $files = glob($uploadPath . '/*.{mrc,marc}', GLOB_BRACE);
        
        if (empty($files)) {
            $this->info('No MARC files found to clean up.');
            return 0;
        }

        $deleteAll = $this->option('all');
        $daysOld = (int) $this->option('days');
        $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
        
        $deleted = 0;
        $totalSize = 0;

        foreach ($files as $file) {
            $shouldDelete = false;
            
            if ($deleteAll) {
                $shouldDelete = true;
            } else {
                $fileTime = filemtime($file);
                if ($fileTime < $cutoffTime) {
                    $shouldDelete = true;
                }
            }

            if ($shouldDelete) {
                $size = filesize($file);
                if (unlink($file)) {
                    $deleted++;
                    $totalSize += $size;
                    $this->line('Deleted: ' . basename($file) . ' (' . $this->formatBytes($size) . ')');
                }
            }
        }

        if ($deleted > 0) {
            $this->info("\nCleanup complete:");
            $this->info("Files deleted: {$deleted}");
            $this->info("Space freed: " . $this->formatBytes($totalSize));
        } else {
            $this->info('No files matched the cleanup criteria.');
        }

        return 0;
    }

    /**
     * Format bytes to human-readable size
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
