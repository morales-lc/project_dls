<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

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
            'marc_file' => 'required|file'
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
        // respond based on request type
        if ($expectsJson) {
            return response()->json(['success' => true, 'message' => 'MARC import finished.', 'output' => strlen($output) > 2000 ? substr($output, 0, 2000) . '...' : $output]);
        }

        return redirect()->route('marc.import.form')->with('success', 'MARC import finished. ' . (strlen($output) > 200 ? substr($output, 0, 200) . '...' : $output));
    }
}
