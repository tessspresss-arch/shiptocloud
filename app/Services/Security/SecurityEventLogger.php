<?php

namespace App\Services\Security;

use App\Models\SecurityEvent;
use Illuminate\Http\Request;

class SecurityEventLogger
{
    public function log(
        string $eventType,
        string $severity = 'info',
        ?int $userId = null,
        ?array $context = null,
        ?Request $request = null
    ): SecurityEvent {
        return SecurityEvent::create([
            'event_type' => $eventType,
            'severity' => $severity,
            'user_id' => $userId,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'context' => $context,
        ]);
    }
}
