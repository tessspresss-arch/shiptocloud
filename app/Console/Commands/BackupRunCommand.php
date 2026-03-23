<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BackupRunCommand extends Command
{
    protected $signature = 'backup:run
        {--only-db : Keep compatibility with backup packages and backup only database}
        {--keep-days=14 : Delete backup files older than this number of days}';

    protected $description = 'Create a database backup in storage/app/backups/database';

    public function handle(): int
    {
        if (!$this->option('only-db')) {
            $this->warn('This project currently supports database backups only. Use --only-db.');
        }

        $connectionName = config('database.default');
        $connection = config("database.connections.{$connectionName}");

        if (!is_array($connection)) {
            $this->error("Database connection [{$connectionName}] is not configured.");
            return self::FAILURE;
        }

        $driver = (string) ($connection['driver'] ?? '');
        $databaseName = (string) ($connection['database'] ?? 'database');
        $timestamp = now()->format('Ymd_His');

        $backupDirectory = storage_path('app/backups/database/' . now()->format('Y/m'));
        File::ensureDirectoryExists($backupDirectory);

        $safeDatabaseName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $databaseName) ?: 'database';
        $backupFile = "{$backupDirectory}/backup_{$safeDatabaseName}_{$timestamp}.sql";

        try {
            match ($driver) {
                'mysql', 'mariadb' => $this->backupMysql($connection, $backupFile),
                'pgsql' => $this->backupPgsql($connection, $backupFile),
                'sqlite' => $this->backupSqlite($connection, $backupFile),
                default => throw new \RuntimeException("Unsupported driver [{$driver}] for backup."),
            };

            $deleted = $this->cleanupOldBackups((int) $this->option('keep-days'));

            $this->info("Database backup created: {$backupFile}");
            if ($deleted > 0) {
                $this->info("Old backup files deleted: {$deleted}");
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function backupMysql(array $connection, string $backupFile): void
    {
        $binary = (string) env('DB_DUMP_BINARY', 'mysqldump');
        $host = (string) ($connection['host'] ?? '127.0.0.1');
        $port = (string) ($connection['port'] ?? '3306');
        $database = (string) ($connection['database'] ?? '');
        $username = (string) ($connection['username'] ?? '');
        $password = (string) ($connection['password'] ?? '');

        $command = [
            $binary,
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $username,
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            '--default-character-set=utf8mb4',
        ];

        if ($password !== '') {
            $command[] = '--password=' . $password;
        }

        $command[] = $database;

        $process = new Process($command);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            $error = trim($process->getErrorOutput()) ?: 'mysqldump command failed.';
            throw new \RuntimeException($error . ' Set DB_DUMP_BINARY in .env if mysqldump is not in PATH.');
        }

        File::put($backupFile, $process->getOutput());
    }

    private function backupPgsql(array $connection, string $backupFile): void
    {
        $binary = (string) env('PG_DUMP_BINARY', 'pg_dump');
        $host = (string) ($connection['host'] ?? '127.0.0.1');
        $port = (string) ($connection['port'] ?? '5432');
        $database = (string) ($connection['database'] ?? '');
        $username = (string) ($connection['username'] ?? '');
        $password = (string) ($connection['password'] ?? '');

        $command = [
            $binary,
            '--host=' . $host,
            '--port=' . $port,
            '--username=' . $username,
            '--format=plain',
            '--no-owner',
            '--no-privileges',
            $database,
        ];

        $env = $password !== '' ? ['PGPASSWORD' => $password] : null;

        $process = new Process($command, null, $env);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            $error = trim($process->getErrorOutput()) ?: 'pg_dump command failed.';
            throw new \RuntimeException($error . ' Set PG_DUMP_BINARY in .env if pg_dump is not in PATH.');
        }

        File::put($backupFile, $process->getOutput());
    }

    private function backupSqlite(array $connection, string $backupFile): void
    {
        $databasePath = (string) ($connection['database'] ?? '');

        if ($databasePath === '' || $databasePath === ':memory:') {
            throw new \RuntimeException('Cannot backup sqlite in-memory database.');
        }

        if (!$this->isAbsolutePath($databasePath)) {
            $databasePath = base_path($databasePath);
        }

        if (!File::exists($databasePath)) {
            throw new \RuntimeException("SQLite database file not found: {$databasePath}");
        }

        File::copy($databasePath, $backupFile);
    }

    private function cleanupOldBackups(int $keepDays): int
    {
        $keepDays = max($keepDays, 1);
        $cutoffTimestamp = now()->subDays($keepDays)->getTimestamp();
        $backupRoot = storage_path('app/backups/database');

        if (!File::exists($backupRoot)) {
            return 0;
        }

        $deleted = 0;
        foreach (File::allFiles($backupRoot) as $file) {
            if ($file->getMTime() < $cutoffTimestamp) {
                File::delete($file->getRealPath());
                $deleted++;
            }
        }

        return $deleted;
    }

    private function isAbsolutePath(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        if (str_starts_with($path, '/') || str_starts_with($path, '\\')) {
            return true;
        }

        return preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) === 1;
    }
}
