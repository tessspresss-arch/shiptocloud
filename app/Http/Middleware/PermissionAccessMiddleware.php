<?php

namespace App\Http\Middleware;

use App\Services\Security\ClinicalAuthorizationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionAccessMiddleware
{
    public function handle(Request $request, Closure $next, string $permissionCode): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login');
        }

        $authorization = app(ClinicalAuthorizationService::class);
        if (!$authorization->allowsCode($user, $permissionCode)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }

            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
