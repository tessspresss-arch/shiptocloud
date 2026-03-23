<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforceAdminTwoFactorSetup
{
    public function handle(Request $request, Closure $next)
    {
        if (! (bool) config('auth.two_factor.enforce_admin_setup', false)) {
            return $next($request);
        }

        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        $requiredRoles = (array) config('auth.two_factor.required_roles', ['admin']);
        if (! $user->hasAnyRole($requiredRoles)) {
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

        $configured = (bool) $user->two_factor_enabled && !empty($user->two_factor_secret) && !empty($user->two_factor_confirmed_at);

        if (! $configured && ! in_array($routeName, $allowedRoutes, true)) {
            return redirect()->route('profile.2fa.show')->withErrors([
                'error' => '2FA TOTP est obligatoire pour les administrateurs. Veuillez finaliser l’activation.',
            ]);
        }

        return $next($request);
    }
}
