<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\BackupLog;
use App\Models\BackupSchedule;
use App\Jobs\RunManualBackup;

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
     * Stream a backup file safely to the client.
     *
     * @param string $path Relative local disk path
     * @param string $downloadName Filename for the client
     * @param bool $deleteAfterSend Delete file after streaming completes
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function streamBackupFile(string $path, string $downloadName, bool $deleteAfterSend = false)
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        @ini_set('zlib.output_compression', '0');
        @ini_set('output_buffering', 'Off');
        ignore_user_abort(true);

        // Remove any buffered output so binary downloads start with PK signature bytes.
        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        $absolutePath = Storage::disk('local')->path($path);

        $response = response()->download($absolutePath, $downloadName, [
            'Content-Type' => 'application/zip',
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);

        if ($deleteAfterSend) {
            $response->deleteFileAfterSend(true);
        }

        return $response;
    }

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
        
        return $this->streamBackupFile($path, $file, false);
    }
    
    /**
     * Download a backup file and delete it after download
     * 
     * Streams the file directly to the client and removes it from storage
     * after successful transmission. Used for temporary/one-time downloads.
     * 
        * @param string $file Filename to download and delete
        * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
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
        
        return $this->streamBackupFile($path, $file, true);
    }

    /**
     * Delete a backup file from storage using a backup log record
     *
     * @param int $id Backup log id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFile($id)
    {
        $log = BackupLog::findOrFail($id);

        if (!$log->filename) {
            return redirect()->route('admin.backup')
                ->with('error', 'No backup file is associated with this log entry.');
        }

        $path = 'Laravel/' . basename($log->filename);

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        $log->filename = null;
        $log->file_size_mb = null;
        $log->output = trim(($log->output ?? '') . "\nBackup file deleted from storage by " . optional(Auth::user())->name . '.');
        $log->save();

        return redirect()->route('admin.backup')
            ->with('success', 'Backup file deleted successfully.');
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
        $validated = $request->validate([
            'type' => 'required|in:full,database,files',
        ]);

        $log = BackupLog::create([
            'type' => $validated['type'],
            'frequency' => 'manual',
            'status' => 'processing',
            'filename' => null,
            'file_size_mb' => null,
            'output' => 'Backup queued and waiting for worker...',
            'user_id' => Auth::id(),
            'schedule_id' => null,
        ]);

        RunManualBackup::dispatch($log->id);

        return redirect()->route('admin.backup')
            ->with('success', ucfirst($validated['type']) . ' backup queued. You can continue using the system while it runs in the background.');
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
