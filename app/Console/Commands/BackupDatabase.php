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
        
        $command = sprintf(
            'mysqldump -u%s %s > "%s"',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.database'),
            storage_path('app/backups/' . $filename)
        );

        exec($command);
        
        $this->info('Database backup created: ' . $filename);
    }
}
