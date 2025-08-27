<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    public function createBackup()
    {
        try {
            $fileName = 'backup-' . date('YmdHis') . '.sql';
            $backupPath = storage_path('app/backup/' . $fileName);

            $mysqlDumpPath = '/usr/local/bin/mysqldump'; // Update with your path

            $command = sprintf(
                '%s --host=%s --port=%s --user=%s --password=%s %s > %s 2>&1',
                $mysqlDumpPath,
                env('DB_HOST'),
                env('DB_PORT'),
                env('DB_USERNAME'),
                env('DB_PASSWORD'),
                env('DB_DATABASE'),
                $backupPath
            );

            // Execute the command
            exec($command, $output, $returnValue);

            if ($returnValue !== 0) {
                // Log detailed output for debugging
                \Log::error("Database backup failed: " . implode(PHP_EOL, $output));

                return response()->json([
                    'message' => 'Database backup failed',
                    'error' => $output, // Return the output array for debugging
                ], 500);
            }

            // Return the backup file as a download response
            return response()->download($backupPath, $fileName);
        } catch (\Exception $e) {
            // Log any exceptions
            \Log::error("Exception during database backup: " . $e->getMessage());

            return response()->json([
                'message' => 'Exception during database backup',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function showBackupPage()
    {
        // Check if user is logged in
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        // Get existing backup files
        $backupPath = storage_path('app/backups');
        $backups = [];
        
        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/backup-*.sql');
            foreach ($files as $file) {
                $backups[] = [
                    'name' => basename($file),
                    'size' => $this->formatFileSize(filesize($file)),
                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                    'path' => $file
                ];
            }
            // Sort by date, newest first
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }
        
        return view('backup.index', compact('backups'));
    }
    
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function runBackup()
    {
        // Check if user is logged in
        if (!auth()->check()) {
            return redirect('/login');
        }

        try {
            // Run the artisan command
            \Artisan::call('db:backup');
            
            return back()->with('success', 'Database backup created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }
}
