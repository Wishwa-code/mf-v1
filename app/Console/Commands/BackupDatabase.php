<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup the database';

    public function handle()
    {
        // Make sure backup folder exists
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $backupPath = storage_path('app/backups/' . $filename);
        
        // Get database config
        $host = config('database.connections.mysql.host');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database = config('database.connections.mysql.database');
        
        // Try to find mysqldump executable
        $mysqldumpPath = $this->findMysqldump();
        
        // Build command with password if exists
        if (!empty($password)) {
            $command = sprintf(
                '%s -h%s -u%s -p%s %s > "%s" 2>&1',
                $mysqldumpPath,
                $host,
                $username,
                $password,
                $database,
                $backupPath
            );
        } else {
            $command = sprintf(
                '%s -h%s -u%s %s > "%s" 2>&1',
                $mysqldumpPath,
                $host,
                $username,
                $database,
                $backupPath
            );
        }

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($backupPath) && filesize($backupPath) > 0) {
            $this->info('Database backup created: ' . $filename);
        } else {
            $this->error('Backup failed!');
            if (!empty($output)) {
                $this->error('Error: ' . implode("\n", $output));
            }
            // Clean up empty file
            if (file_exists($backupPath)) {
                unlink($backupPath);
            }
        }
    }

    private function findMysqldump()
    {
        // Common paths for mysqldump
        $paths = [
            'mysqldump', // If it's in PATH
            'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe',
            'C:\Program Files\MySQL\MySQL Server 5.7\bin\mysqldump.exe',
            'C:\xampp\mysql\bin\mysqldump.exe',
            'C:\wamp\bin\mysql\mysql8.0.31\bin\mysqldump.exe',
            'C:\wamp64\bin\mysql\mysql8.0.31\bin\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump'
        ];

        foreach ($paths as $path) {
            if ($this->commandExists($path)) {
                return $path;
            }
        }

        // Fallback to just mysqldump and hope it's in PATH
        return 'mysqldump';
    }

    private function commandExists($command)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            if (file_exists($command)) {
                return true;
            }
            $output = shell_exec("where $command 2>NUL");
            return !empty($output);
        } else {
            // Unix/Linux/Mac
            if (file_exists($command)) {
                return true;
            }
            $output = shell_exec("which $command 2>/dev/null");
            return !empty($output);
        }
    }
}
