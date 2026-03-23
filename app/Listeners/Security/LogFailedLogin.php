<?php

namespace App\Listeners\Security;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        $ip = request()?->ip() ?? 'unknown';
        $email = (string) ($event->credentials['email'] ?? 'unknown');

        Log::channel('security_stack')->warning('auth.login.failed', [
            'email' => $email,
            'ip' => $ip,
            'user_agent' => request()?->userAgent(),
        ]);

        $cacheKey = 'security:failed-login:ip:' . $ip;
        $count = (int) Cache::get($cacheKey, 0) + 1;
        Cache::put($cacheKey, $count, now()->addMinutes(15));

        if ($count >= 8) {
            Log::channel('security_stack')->alert('security.anomaly.failed_login_spike', [
                'ip' => $ip,
                'failed_attempts_15m' => $count,
                'email_last_seen' => $email,
            ]);
        }
    }
}
