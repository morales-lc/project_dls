<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\BackupLog;

class BackupController extends Controller
{
    public function index()
    {
        // Get recent backup logs from database
        $logs = BackupLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        return view('admin-backup', [
            'logs' => $logs,
        ]);
    }
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
                
                // Store backup info in session for download
                $request->session()->put('download_backup', $newFiles);
                $message = ucfirst($type).' backup completed successfully! Download will start automatically.';
            } else {
                $message = ucfirst($type).' backup completed successfully!';
            }
            
            // Save log to database
            BackupLog::create([
                'type' => $type,
                'status' => 'success',
                'filename' => $filename,
                'file_size_mb' => $fileSize,
                'output' => $output,
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->route('admin.backup')
                ->with('success', $message)
                ->with('backup_output', $output);
        } else {
            // Save failed log to database
            BackupLog::create([
                'type' => $type,
                'status' => 'failed',
                'filename' => null,
                'file_size_mb' => null,
                'output' => $output ?: 'No output received',
                'user_id' => Auth::id(),
            ]);
            
            $message = 'Backup failed! Please check the error output below.';
            return redirect()->route('admin.backup')
                ->with('error', $message)
                ->with('backup_output', $output ?: 'No output received');
        }
    }
}
