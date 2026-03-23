<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class ApplyDynamicSessionLifetime
{
    public function handle(Request $request, Closure $next)
    {
        $minutes = $this->resolveSessionLifetime();

        config([
            'session.lifetime' => $minutes,
        ]);

        if (function_exists('ini_set')) {
            @ini_set('session.gc_maxlifetime', (string) ($minutes * 60));
        }

        return $next($request);
    }

    private function resolveSessionLifetime(): int
    {
        try {
            $rawValue = (int) Setting::get('session_timeout', config('session.lifetime', 90));

            // Legacy installs may still store this value in seconds.
            if ($rawValue > 1440) {
                $rawValue = (int) ceil($rawValue / 60);
            }

            return max(1, $rawValue);
        } catch (\Throwable) {
            return max(1, (int) config('session.lifetime', 90));
        }
    }
}
