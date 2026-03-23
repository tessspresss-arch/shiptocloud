<?php

namespace App\Listeners\Security;

use App\Models\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class LogLogout
{
    public function handle(Logout $event): void
    {
        $user = $event->user instanceof User ? $event->user : null;

        Log::channel('security_stack')->info('auth.logout', [
            'user_id' => $user?->id,
            'email' => $user?->email,
            'ip' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
