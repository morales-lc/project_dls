<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\BackupLog;
use App\Models\BackupSchedule;

/**
 * System Backup Controller
 * 
 * Manages system backups including database and file backups.
 * Provides backup creation, download, and logging functionality.
 * Uses Spatie Laravel Backup package.
 * 
 * @package App\Http\Controllers
 */
class BackupController extends Controller
{
    /**
     * Display the backup management interface
     * 
     * Shows recent backup logs with user information and status,
     * plus backup schedules configuration.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get recent backup logs from database
        $logs = BackupLog::with('user', 'schedule')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        // Get all backup schedules
        $schedules = BackupSchedule::orderBy('frequency')->get();
        
        return view('admin-backup', [
            'logs' => $logs,
            'schedules' => $schedules,
        ]);
    }
    
    /**
     * Download a backup file
     * 
     * Serves a backup file for download. Includes security check to prevent
     * directory traversal attacks.
     * 
     * @param string $file Filename to download
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 404 if file not found
     */
    public function download($file)
    {
        // Sanitize filename to prevent directory traversal
        $file = basename($file);
        $path = 'Laravel/' . $file;
        
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Backup file not found.');
        }
        
        $fullPath = Storage::disk('local')->path($path);
        return response()->download($fullPath);
    }
    
    /**
     * Download a backup file and delete it after download
     * 
     * Streams the file directly to the client and removes it from storage
     * after successful transmission. Used for temporary/one-time downloads.
     * 
     * @param string $file Filename to download and delete
     * @return void (sends file and exits)
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 404 if file not found
     */
    public function downloadAndDelete($file)
    {
        // Sanitize filename to prevent directory traversal
        $file = basename($file);
        $path = 'Laravel/' . $file;
        
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Backup file not found.');
        }
        
        $fullPath = Storage::disk('local')->path($path);
        
        // Clear any output buffers to prevent corruption
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set headers for binary download
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($fullPath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        
        // Read and output the file in chunks
        $handle = fopen($fullPath, 'rb');
        while (!feof($handle)) {
            echo fread($handle, 8192);
            flush();
        }
        fclose($handle);
        
        // Delete the file after sending
        @unlink($fullPath);
        
        exit;
    }

    /**
     * Execute a backup operation
     * 
     * Runs the Laravel Backup package command to create a backup.
     * Supports three types: full (database + files), database only, or files only.
     * Logs the operation and provides automatic download on success.
     * 
     * @param Request $request HTTP request with 'type' parameter (full/database/files)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function run(Request $request)
    {
        $type = $request->input('type', 'full');
        
        // Get list of existing backup files before running backup
        $existingFiles = collect(Storage::disk('local')->files('Laravel'))
            ->filter(function ($file) {
                return preg_match('/\\.zip$/i', $file);
            })
            ->all();
        
        // Build the command with proper arguments
        $baseCommand = 'php artisan backup:run';
        $command = match ($type) {
            'database' => $baseCommand . ' --only-db',
            'files' => $baseCommand . ' --only-files',
            default => $baseCommand,
        };
        
        // Change to project directory and run command
        $projectPath = base_path();
        $fullCommand = "cd /d \"{$projectPath}\" && {$command} 2>&1";
        
        // Execute and capture output
        $output = shell_exec($fullCommand);
        
        // Check if backup was successful
        $success = strpos($output, 'Backup completed!') !== false || 
                   strpos($output, 'Successfully copied zip') !== false;
        
        if ($success) {
            // Find the newly created backup file
            $newFiles = collect(Storage::disk('local')->files('Laravel'))
                ->filter(function ($file) {
                    return preg_match('/\\.zip$/i', $file);
                })
                ->diff($existingFiles)
                ->first();
            
            $filename = null;
            $fileSize = null;
            
            if ($newFiles) {
                $filename = basename($newFiles);
                $fileSize = Storage::disk('local')->size($newFiles) / 1024 / 1024; // Convert to MB
                
                // Store backup info in session for download (flash = auto-clear after next request)
                $request->session()->flash('download_backup', $newFiles);
                $message = ucfirst($type).' backup completed successfully! Download will start automatically.';
            } else {
                $message = ucfirst($type).' backup completed successfully!';
            }
            
            // Save log to database
            BackupLog::create([
                'type' => $type,
                'frequency' => 'manual',
                'status' => 'success',
                'filename' => $filename,
                'file_size_mb' => $fileSize,
                'output' => $output,
                'user_id' => Auth::id(),
                'schedule_id' => null,
            ]);
            
            return redirect()->route('admin.backup')
                ->with('success', $message)
                ->with('backup_output', $output);
        } else {
            // Save failed log to database
            BackupLog::create([
                'type' => $type,
                'frequency' => 'manual',
                'status' => 'failed',
                'filename' => null,
                'file_size_mb' => null,
                'output' => $output ?: 'No output received',
                'user_id' => Auth::id(),
                'schedule_id' => null,
            ]);
            
            $message = 'Backup failed! Please check the error output below.';
            return redirect()->route('admin.backup')
                ->with('error', $message)
                ->with('backup_output', $output ?: 'No output received');
        }
    }

    /**
     * Store a new backup schedule
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'frequency' => 'required|in:daily,weekly,monthly',
            'backup_type' => 'required|in:full,database,files',
            'scheduled_time' => 'required|date_format:H:i',
            'retention_count' => 'required|integer|min:1|max:365',
        ], [
            'retention_count.min' => 'Retention count must be at least 1.',
            'retention_count.max' => 'Retention count cannot exceed 365.',
        ]);

        // Handle checkbox separately
        $validated['enabled'] = $request->has('enabled');

        $schedule = BackupSchedule::create($validated);
        
        // Calculate first run time
        $schedule->next_run_at = $schedule->calculateNextRun();
        $schedule->save();

        return redirect()->route('admin.backup')
            ->with('success', ucfirst($validated['frequency']) . ' backup schedule created successfully!');
    }

    /**
     * Update a backup schedule
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSchedule(Request $request, $id)
    {
        $schedule = BackupSchedule::findOrFail($id);

        $validated = $request->validate([
            'frequency' => 'required|in:daily,weekly,monthly',
            'backup_type' => 'required|in:full,database,files',
            'scheduled_time' => 'required|date_format:H:i',
            'retention_count' => 'required|integer|min:1|max:365',
        ], [
            'retention_count.min' => 'Retention count must be at least 1.',
            'retention_count.max' => 'Retention count cannot exceed 365.',
        ]);

        // Handle checkbox separately
        $validated['enabled'] = $request->has('enabled');

        $schedule->update($validated);
        
        // Recalculate next run time
        $schedule->next_run_at = $schedule->calculateNextRun();
        $schedule->save();

        return redirect()->route('admin.backup')
            ->with('success', 'Backup schedule updated successfully!');
    }

    /**
     * Delete a backup schedule
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroySchedule($id)
    {
        $schedule = BackupSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('admin.backup')
            ->with('success', 'Backup schedule deleted successfully!');
    }

    /**
     * Toggle schedule enabled status
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleSchedule($id)
    {
        $schedule = BackupSchedule::findOrFail($id);
        $schedule->enabled = !$schedule->enabled;
        $schedule->save();

        $status = $schedule->enabled ? 'enabled' : 'disabled';
        return redirect()->route('admin.backup')
            ->with('success', "Backup schedule {$status} successfully!");
    }
}
