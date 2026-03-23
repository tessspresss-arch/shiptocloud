<?php

namespace App\Services\Settings;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class SettingsMaintenanceService
{
    public function runDatabaseBackup(): void
    {
        Artisan::call('backup:run', ['--only-db' => true]);
    }

    public function listBackupFiles(): array
    {
        $backupRoot = storage_path('app/backups/database');
        if (!File::exists($backupRoot)) {
            return [];
        }

        return collect(File::allFiles($backupRoot))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(function ($file) {
                $absolutePath = $file->getRealPath();
                $relativePath = str_replace(storage_path('app') . DIRECTORY_SEPARATOR, '', $absolutePath);
                return [
                    'name' => $file->getFilename(),
                    'relative_path' => str_replace('\\', '/', $relativePath),
                    'size_human' => $this->formatBytes($file->getSize()),
                    'updated_at' => date('d/m/Y H:i', $file->getMTime()),
                ];
            })->values()->all();
    }

    public function resolveDownloadPath(?string $file): ?string
    {
        $backupRoot = storage_path('app/backups/database');
        $target = (string) $file;
        if ($target === '') {
            $backups = $this->listBackupFiles();
            $target = $backups[0]['relative_path'] ?? '';
        }
        if ($target === '') {
            return null;
        }

        $relativePath = str_replace(['..\\', '../'], '', $target);
        $fullPath = storage_path('app/' . ltrim($relativePath, '/\\'));
        if (!str_starts_with($fullPath, $backupRoot) || !File::exists($fullPath)) {
            return null;
        }
        return $fullPath;
    }

    public function restoreDatabaseFromFile(string $restoreFile): void
    {
        $connectionName = config('database.default');
        $connection = config("database.connections.{$connectionName}");
        if (!is_array($connection)) {
            throw new \RuntimeException("Database connection [{$connectionName}] is not configured.");
        }

        $driver = (string) ($connection['driver'] ?? '');
        match ($driver) {
            'mysql', 'mariadb' => $this->restoreMysql($connection, $restoreFile),
            'pgsql' => $this->restorePgsql($connection, $restoreFile),
            'sqlite' => $this->restoreSqlite($connection, $restoreFile),
            default => throw new \RuntimeException("Unsupported driver [{$driver}] for restore."),
        };
    }

    public function clearCaches(): void
    {
        Artisan::call('optimize:clear');
        Cache::forget('all_settings');
    }

    public function systemStats(): array
    {
        return [
            'users' => \App\Models\User::count(),
            'patients' => \App\Models\Patient::count() ?? 0,
            'consultations' => \App\Models\Consultation::count() ?? 0,
            'documents' => \App\Models\DocumentMedical::count() ?? 0,
            'disk_usage' => $this->getDiskUsage(),
            'database_size' => $this->getDatabaseSize(),
        ];
    }

    private function restoreMysql(array $connection, string $restoreFile): void
    {
        $binary = (string) env('DB_CLIENT_BINARY', 'mysql');
        $command = [$binary, '--host=' . (string) ($connection['host'] ?? '127.0.0.1'), '--port=' . (string) ($connection['port'] ?? '3306'), '--user=' . (string) ($connection['username'] ?? '')];
        if ((string) ($connection['password'] ?? '') !== '') {
            $command[] = '--password=' . (string) $connection['password'];
        }
        $command[] = (string) ($connection['database'] ?? '');
        $process = new Process($command);
        $process->setTimeout(300);
        $process->setInput(File::get($restoreFile));
        $process->run();
        if (!$process->isSuccessful()) {
            $error = trim($process->getErrorOutput()) ?: 'mysql command failed.';
            throw new \RuntimeException($error . ' Configurez DB_CLIENT_BINARY si mysql n est pas dans le PATH.');
        }
    }

    private function restorePgsql(array $connection, string $restoreFile): void
    {
        $binary = (string) env('PG_CLIENT_BINARY', 'psql');
        $env = (string) ($connection['password'] ?? '') !== '' ? ['PGPASSWORD' => (string) $connection['password']] : null;
        $command = [$binary, '--host=' . (string) ($connection['host'] ?? '127.0.0.1'), '--port=' . (string) ($connection['port'] ?? '5432'), '--username=' . (string) ($connection['username'] ?? ''), '--dbname=' . (string) ($connection['database'] ?? '')];
        $process = new Process($command, null, $env);
        $process->setTimeout(300);
        $process->setInput(File::get($restoreFile));
        $process->run();
        if (!$process->isSuccessful()) {
            $error = trim($process->getErrorOutput()) ?: 'psql command failed.';
            throw new \RuntimeException($error . ' Configurez PG_CLIENT_BINARY si psql n est pas dans le PATH.');
        }
    }

    private function restoreSqlite(array $connection, string $restoreFile): void
    {
        $databasePath = (string) ($connection['database'] ?? '');
        if ($databasePath === '' || $databasePath === ':memory:') {
            throw new \RuntimeException('Cannot restore sqlite in-memory database.');
        }
        if (!preg_match('/\.(sqlite|db)$/i', $restoreFile)) {
            throw new \RuntimeException('Pour SQLite, chargez un fichier .sqlite ou .db.');
        }
        if (!preg_match('/^[A-Za-z]:[\\\\\/]/', $databasePath) && !str_starts_with($databasePath, '/')) {
            $databasePath = base_path($databasePath);
        }
        File::copy($restoreFile, $databasePath);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' o';
        }
        $units = ['Ko', 'Mo', 'Go', 'To'];
        $value = $bytes / 1024;
        foreach ($units as $unit) {
            if ($value < 1024 || $unit === 'To') {
                return number_format($value, $value >= 10 ? 1 : 2, ',', ' ') . ' ' . $unit;
            }
            $value /= 1024;
        }
        return number_format($value, 2, ',', ' ') . ' To';
    }

    private function getDiskUsage(): string
    {
        $path = storage_path('app');
        $size = 0;
        if (is_dir($path)) {
            foreach (scandir($path) ?: [] as $file) {
                if ($file !== '.' && $file !== '..') {
                    $size += $this->getDirectorySize($path . '/' . $file);
                }
            }
        }
        return $this->formatBytes($size);
    }

    private function getDirectorySize(string $path): int
    {
        $size = 0;
        if (is_file($path)) {
            $size = filesize($path) ?: 0;
        } elseif (is_dir($path)) {
            foreach (scandir($path) ?: [] as $file) {
                if ($file !== '.' && $file !== '..') {
                    $size += $this->getDirectorySize($path . '/' . $file);
                }
            }
        }
        return $size;
    }

    private function getDatabaseSize(): string
    {
        try {
            $database = env('DB_DATABASE');
            $results = DB::select('SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size FROM information_schema.tables WHERE table_schema = ?', [$database]);
            if ($results && isset($results[0]->size)) {
                return round($results[0]->size, 2) . ' MB';
            }
            return '0 MB';
        } catch (\Exception) {
            return 'N/A';
        }
    }
}
