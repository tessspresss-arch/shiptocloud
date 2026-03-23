<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $moduleId): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return redirect()->route('login');
        }

        if (!$user->hasModuleAccess($moduleId)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Acces refuse au module: {$moduleId}",
                ], 403);
            }

            abort(403, "Acces refuse au module: {$moduleId}");
        }

        return $next($request);
    }
}
