<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use InvalidArgumentException;
use Psy\Exception\ParseErrorException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if ($this->shouldIgnoreToolingException($e)) {
                return false;
            }
        });
    }

    private function shouldIgnoreToolingException(Throwable $e): bool
    {
        if (!app()->runningInConsole()) {
            return false;
        }

        if ($e instanceof ParseErrorException) {
            return true;
        }

        if (str_contains($e->getMessage(), 'psysh_history is not allowed')) {
            return true;
        }

        if ($e instanceof InvalidArgumentException && str_contains($e->getMessage(), 'Unexpected end of input') && $this->traceContainsPsysh($e)) {
            return true;
        }

        return false;
    }

    private function traceContainsPsysh(Throwable $e): bool
    {
        if (str_contains(strtolower((string) $e->getFile()), 'psysh')) {
            return true;
        }

        foreach ($e->getTrace() as $frame) {
            $file = strtolower((string) ($frame['file'] ?? ''));
            if ($file !== '' && str_contains($file, 'psysh')) {
                return true;
            }
        }

        return false;
    }
}
