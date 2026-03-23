<?php

namespace App\Listeners\Security;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user instanceof User ? $event->user : null;

        Log::channel('security_stack')->info('auth.login.success', [
            'user_id' => $user?->id,
            'email' => $user?->email,
            'ip' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'remember' => $event->remember,
        ]);
    }
}
