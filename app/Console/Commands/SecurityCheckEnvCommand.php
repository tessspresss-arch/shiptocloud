<?php

namespace App\Console\Commands;

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use ReflectionClass;

class SecurityCheckEnvCommand extends Command
{
    protected $signature = 'security:check-env';

    protected $description = 'Validate environment and CSRF security settings';

    public function handle(): int
    {
        $issues = [];
        $warnings = [];

        $appEnv = (string) Config::get('app.env');
        $isProduction = strtolower($appEnv) === 'production';

        if (!Config::get('app.key')) {
            $issues[] = 'APP_KEY is missing.';
        }

        if ($isProduction && Config::get('app.debug')) {
            $issues[] = 'APP_DEBUG must be false in production.';
        }

        $appUrl = (string) Config::get('app.url');
        if ($isProduction && !str_starts_with(strtolower($appUrl), 'https://')) {
            $issues[] = 'APP_URL should use HTTPS in production.';
        }

        $logLevel = strtolower((string) env('LOG_LEVEL', 'debug'));
        if ($isProduction && in_array($logLevel, ['debug', 'trace'], true)) {
            $warnings[] = 'LOG_LEVEL is very verbose in production (debug/trace).';
        }

        $dbPassword = (string) Config::get('database.connections.' . Config::get('database.default') . '.password', '');
        if ($isProduction && trim($dbPassword) === '') {
            $warnings[] = 'DB password is empty in production.';
        }

        $sessionSecure = Config::get('session.secure');
        if ($isProduction && $sessionSecure !== true) {
            $warnings[] = 'SESSION_SECURE_COOKIE should be true in production.';
        }

        $csrfExcept = $this->csrfExceptUris();
        if (in_array('*', $csrfExcept, true)) {
            $issues[] = 'CSRF middleware excludes all routes ("*"). This is insecure.';
        }

        $this->info('Environment check summary');
        $this->line('- APP_ENV: ' . $appEnv);
        $this->line('- APP_DEBUG: ' . (Config::get('app.debug') ? 'true' : 'false'));
        $this->line('- APP_URL: ' . $appUrl);
        $this->line('- SESSION_SECURE_COOKIE: ' . var_export($sessionSecure, true));
        $this->line('- CSRF excluded URIs: ' . (empty($csrfExcept) ? 'none' : implode(', ', $csrfExcept)));

        foreach ($warnings as $warning) {
            $this->warn('Warning: ' . $warning);
        }

        foreach ($issues as $issue) {
            $this->error('Issue: ' . $issue);
        }

        if ($issues !== []) {
            return self::FAILURE;
        }

        $this->info('Security checks passed.');
        return self::SUCCESS;
    }

    private function csrfExceptUris(): array
    {
        $middleware = app(VerifyCsrfToken::class);
        $reflection = new ReflectionClass($middleware);
        $property = $reflection->getProperty('except');
        $property->setAccessible(true);

        $value = $property->getValue($middleware);

        return is_array($value) ? $value : [];
    }
}

