<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StoragePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set proper permissions for storage directories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting storage permissions...');

        $storagePath = storage_path();

        if (PHP_OS_FAMILY === 'Windows') {
            $this->warn('Windows detected. Permissions may not apply. Ensure storage directories are writable.');
            return;
        }

        // Set permissions for storage directory
        exec("chown -R www-data:www-data {$storagePath}", $output, $returnVar);
        if ($returnVar === 0) {
            $this->info('Storage permissions set successfully.');
        } else {
            $this->error('Failed to set storage permissions.');
        }

        // Set directory permissions to 755
        exec("find {$storagePath} -type d -exec chmod 755 {} \;", $output, $returnVar);
        if ($returnVar === 0) {
            $this->info('Directory permissions set to 755.');
        }

        // Set file permissions to 644
        exec("find {$storagePath} -type f -exec chmod 644 {} \;", $output, $returnVar);
        if ($returnVar === 0) {
            $this->info('File permissions set to 644.');
        }

        $this->info('Storage permissions configuration completed.');
    }
}
