<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientTelemetryController extends Controller
{
    public function store(Request $request)
    {
        $payload = $request->validate([
            'type' => ['required', 'string', 'max:80'],
            'level' => ['nullable', 'in:info,warning,error'],
            'message' => ['required', 'string', 'max:2000'],
            'url' => ['nullable', 'string', 'max:2048'],
            'source' => ['nullable', 'string', 'max:2048'],
            'stack' => ['nullable', 'string', 'max:12000'],
            'status' => ['nullable', 'integer', 'between:100,599'],
            'method' => ['nullable', 'string', 'max:16'],
            'context' => ['nullable', 'array'],
            'context.*' => ['nullable'],
        ]);

        $level = $payload['level'] ?? ($this->isErrorLevel($payload) ? 'error' : 'warning');

        $context = [
            'type' => $payload['type'],
            'message' => $payload['message'],
            'url' => $payload['url'] ?? $request->headers->get('referer'),
            'source' => $payload['source'] ?? null,
            'stack' => $payload['stack'] ?? null,
            'status' => $payload['status'] ?? null,
            'method' => $payload['method'] ?? null,
            'context' => $payload['context'] ?? null,
            'user_id' => optional($request->user())->id,
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        Log::channel('client')->log($level, 'client.telemetry', $context);

        return response()->noContent();
    }

    private function isErrorLevel(array $payload): bool
    {
        return ($payload['status'] ?? 0) >= 500
            || in_array($payload['type'], ['js_error', 'unhandled_rejection'], true);
    }
}
