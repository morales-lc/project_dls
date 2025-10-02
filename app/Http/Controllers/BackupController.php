<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        // Show backup options and status (now from private/Laravel)
        $backups = collect(Storage::disk('local')->files('private/Laravel'))
            ->filter(function ($file) {
                return preg_match('/\\.(zip|tar|gz|sql)$/i', $file);
            })
            ->sortDesc()
            ->values();
        return view('admin-backup', [
            'backups' => $backups,
        ]);
    }
    public function download($file)
    {
        $path = 'private/Laravel/' . $file;
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }
        return response()->download(storage_path('app/' . $path));
    }

    public function run(Request $request)
    {
        $type = $request->input('type', 'full');
        $command = match ($type) {
            'database' => 'backup:run --only-db',
            'files' => 'backup:run --only-files',
            default => 'backup:run',
        };
        $exitCode = Artisan::call($command);
        $output = Artisan::output();
        $message = ucfirst($type).' backup completed!';
        if ($exitCode !== 0) {
            $message = 'Backup failed! Output: ' . $output;
        }
        return redirect()->route('admin.backup')->with('success', $message)->with('backup_output', $output);
    }
}
