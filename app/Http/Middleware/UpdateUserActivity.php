<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdateUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            $lastTracked = (int) $request->session()->get('user_activity_tracked_at', 0);
            $nowTs = now()->timestamp;

            // Avoid writing on every request.
            if (($nowTs - $lastTracked) >= 300) {
                $request->user()->forceFill([
                    'last_activity_at' => now(),
                ])->save();

                $request->session()->put('user_activity_tracked_at', $nowTs);
            }
        }

        return $next($request);
    }
}

