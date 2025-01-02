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


}
