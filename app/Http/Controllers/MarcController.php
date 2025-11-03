<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\MarcImportLog;

class MarcController extends Controller
{
    // Show the MARC import form (admin)
    public function showForm()
    {
        return view('admin-import');
    }

    public function import(Request $request)
    {
        // Determine if the caller expects JSON (AJAX)
        $expectsJson = $request->wantsJson() || $request->ajax() || str_contains($request->header('Accept') ?? '', 'application/json');

        $request->validate([
            'marc_file' => 'required|file',
            'delete_missing' => 'nullable|boolean'
        ]);

        //  Store uploaded file in storage/app/private/marc_uploads
        $path = $request->file('marc_file')->store('marc_uploads');
        $fullPath = storage_path('app/private/marc_uploads/' . basename($path));


        // Verify that the file exists before running Python
        if (!file_exists($fullPath)) {
            if ($expectsJson) {
                return response()->json(['success' => false, 'message' => 'Uploaded file not found after save.'], 500);
            }
            return redirect()->back()->withErrors(['marc_file' => 'Uploaded file not found after save.']);
        }

        // Python script path
        $pythonScript = base_path('scripts/import_marc.py');

        // Prefer the Windows Python Launcher `py` by default; allow override via env
        $pythonSpec = env('PYTHON_EXE'); // e.g., "py -3.10" or "C:\\Path\\to\\python.exe"
        $cmd = [];
        if (!empty($pythonSpec)) {
            // Split on whitespace to support simple "py -3" specs
            $cmd = preg_split('/\s+/', trim($pythonSpec));
        } else {
            // Use Python launcher targeting Python 3
            $cmd = ['py', '-3'];
        }

        // Append script and argument
        $cmd[] = $pythonScript;
        $cmd[] = $fullPath;
        if ($request->boolean('delete_missing')) {
            $cmd[] = '--delete-missing';
        }

        $process = new Process($cmd);

        // Provide environment variables
        $env = array_merge($_ENV, getenv() ?: [], [
            'SystemRoot' => getenv('SystemRoot') ?: (DIRECTORY_SEPARATOR === '\\' ? 'C:\\Windows' : '/'),
            'PATH' => getenv('PATH'),
            'PYTHONHASHSEED' => '0',
        ]);
        $process->setEnv($env);

        try {
            $process->run(function ($type, $buffer) {
                // allow long-running output to be logged incrementally if needed
                if ($type === Process::ERR) {
                    Log::error('MARC import (stderr): ' . $buffer);
                } else {
                    Log::info('MARC import (stdout): ' . $buffer);
                }
            });
        } catch (\Throwable $e) {
            Log::error('MARC import process failed to start: ' . $e->getMessage());
            if ($expectsJson) return response()->json(['success' => false, 'message' => 'Failed to start import process. Check server logs.'], 500);
            return redirect()->back()->withErrors(['marc_file' => 'Failed to start import process. Check server logs.']);
        }

        if (!$process->isSuccessful()) {
            $err = $process->getErrorOutput();
            Log::error('MARC import python error: ' . $err);
            if ($expectsJson) return response()->json(['success' => false, 'message' => 'Import failed', 'error' => trim(substr($err, 0, 2000))], 500);
            return redirect()->back()->withErrors(['marc_file' => 'Import failed: ' . trim(substr($err, 0, 1000))]);
        }

        $output = $process->getOutput();

        // Try to parse the structured summary from stdout
        $summary = null;
        try {
            $lines = preg_split("/\r?\n/", (string)$output) ?: [];
            // find the last line starting with IMPORT_SUMMARY:
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
            // ignore parse errors
        }
        
        // Save import log to database
        if (is_array($summary) && Auth::check()) {
            $user = Auth::user();
            $deletionEnabled = $request->boolean('delete_missing');
            
            $logSummary = sprintf(
                "Import completed successfully.\n\nFile: %s\nImported by: %s (%s)\n\nResults:\n• %d new records added\n• %d existing records updated\n• %d records unchanged\n• %d records deleted%s\n• %d errors encountered\n\nTotal records in file: %d\nUnique records: %d",
                basename($summary['file'] ?? $request->file('marc_file')->getClientOriginalName()),
                $user->name ?? $user->email,
                $user->email,
                (int)($summary['inserted'] ?? 0),
                (int)($summary['updated'] ?? 0),
                (int)($summary['unchanged'] ?? 0),
                (int)($summary['deleted'] ?? 0),
                $deletionEnabled ? '' : ' (deletion was disabled)',
                (int)($summary['errors'] ?? 0),
                (int)($summary['parsed_records'] ?? 0),
                (int)($summary['unique_keys'] ?? 0)
            );
            
            // Update the log file with actual user information
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
                    'filename' => $request->file('marc_file')->getClientOriginalName(),
                    'records_added' => (int)($summary['inserted'] ?? 0),
                    'records_updated' => (int)($summary['updated'] ?? 0),
                    'records_deleted' => (int)($summary['deleted'] ?? 0),
                    'records_unchanged' => (int)($summary['unchanged'] ?? 0),
                    'records_errors' => (int)($summary['errors'] ?? 0),
                    'total_parsed' => (int)($summary['parsed_records'] ?? 0),
                    'deletion_enabled' => $deletionEnabled,
                    'log_file_path' => $summary['log_file'] ?? null,
                    'summary' => $logSummary,
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to save import log: ' . $e->getMessage());
            }
        }
        
        // respond based on request type
        if ($expectsJson) {
            return response()->json([
                'success' => true,
                'message' => 'MARC import finished.',
                'summary' => $summary,
                'output' => strlen($output) > 2000 ? substr($output, 0, 2000) . '...' : $output
            ]);
        }

        $flash = 'MARC import finished.';
        if (is_array($summary)) {
            $flash .= sprintf(
                ' Added: %d, Updated: %d, Unchanged: %d, Deleted: %d%s. Parsed: %d, Unique: %d.',
                (int)($summary['inserted'] ?? 0),
                (int)($summary['updated'] ?? 0),
                (int)($summary['unchanged'] ?? 0),
                (int)($summary['deleted'] ?? 0),
                isset($summary['missing_count']) && ($summary['deletion_mode'] ?? '') === 'dry-run' ? 
                    ' (dry-run missing=' . (int)$summary['missing_count'] . ')' : '',
                (int)($summary['parsed_records'] ?? 0),
                (int)($summary['unique_keys'] ?? 0)
            );
        }

        return redirect()->route('marc.import.form')->with('success', $flash);
    }

    public function downloadLog($filename)
    {
        $logPath = storage_path('logs/marc_imports/' . $filename);
        
        if (!file_exists($logPath) || !str_ends_with($filename, '.log')) {
            abort(404, 'Log file not found');
        }

        return Response::download($logPath, $filename, [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function importLogs()
    {
        $logs = MarcImportLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('marc-import-logs', compact('logs'));
    }

    public function exportLogs()
    {
        $logs = MarcImportLog::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'marc_import_logs_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MarcImportLogsExport($logs),
            $filename
        );
    }
}
