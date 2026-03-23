<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTwoFactorChallengePassed
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        $routeName = (string) optional($request->route())->getName();
        $allowedRoutes = [
            'logout',
            'two-factor.challenge',
            'two-factor.challenge.verify',
            'profile.2fa.show',
            'profile.2fa.enable',
            'profile.2fa.disable',
            'profile.2fa.recovery',
        ];

        if (in_array($routeName, $allowedRoutes, true)) {
            return $next($request);
        }

        $enabled = (bool) $user->two_factor_enabled && !empty($user->two_factor_confirmed_at) && !empty($user->two_factor_secret);
        if (! $enabled) {
            return $next($request);
        }

        $challengeTs = (int) $request->session()->get('two_factor_passed_at', 0);
        $ttl = (int) config('auth.two_factor.challenge_ttl_seconds', 43200);

        if ($challengeTs <= 0 || ($challengeTs + $ttl) < now()->timestamp) {
            $request->session()->forget('two_factor_passed_at');

            return redirect()->route('two-factor.challenge')->withErrors([
                'code' => 'Veuillez confirmer votre code 2FA pour continuer.',
            ]);
        }

        return $next($request);
    }
}
