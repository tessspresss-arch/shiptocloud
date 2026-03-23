<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
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

        if ($roles === []) {
            return $next($request);
        }

        $allowedRoles = array_values(array_filter(array_map('trim', $roles)));

        if (!$user->hasAnyRole($allowedRoles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden.',
                ], 403);
            }

            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
