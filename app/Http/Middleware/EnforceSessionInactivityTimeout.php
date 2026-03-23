<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnforceSessionInactivityTimeout
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()) {
            return $next($request);
        }

        $timeoutMinutes = $this->resolveSessionLifetime();
        $timeoutSeconds = $timeoutMinutes * 60;
        $nowTs = now()->timestamp;
        $lastActivityAt = (int) $request->session()->get('last_activity_at', 0);

        if ($lastActivityAt > 0 && ($nowTs - $lastActivityAt) >= $timeoutSeconds) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->forget('two_factor_passed_at');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Votre session a expire apres {$timeoutMinutes} minute(s) d'inactivite.",
                ], 401);
            }

            return redirect()
                ->route('login')
                ->with('status', "Votre session a expire apres {$timeoutMinutes} minute(s) d'inactivite.");
        }

        $request->session()->put('last_activity_at', $nowTs);

        return $next($request);
    }

    private function resolveSessionLifetime(): int
    {
        try {
            $rawValue = (int) config('session.lifetime', 90);

            if ($rawValue > 1440) {
                $rawValue = (int) ceil($rawValue / 60);
            }

            return max(1, $rawValue);
        } catch (\Throwable) {
            return max(1, (int) config('session.lifetime', 90));
        }
    }
}
