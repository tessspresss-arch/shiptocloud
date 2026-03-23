<?php

namespace App\Services\Integration;

use App\Models\WebhookDelivery;
use App\Models\WebhookSubscription;
use Illuminate\Support\Facades\Http;

class WebhookDispatcher
{
    public function dispatch(string $event, array $payload): void
    {
        $subscriptions = WebhookSubscription::query()
            ->where('direction', 'outgoing')
            ->where('event', $event)
            ->where('active', true)
            ->get();

        foreach ($subscriptions as $subscription) {
            $delivery = WebhookDelivery::create([
                'subscription_id' => $subscription->id,
                'status' => 'pending',
                'attempts' => 0,
                'payload' => $payload,
            ]);

            $response = Http::timeout(max(1, (int) floor($subscription->timeout_ms / 1000)))
                ->withHeaders($this->signatureHeaders($subscription->secret, $payload))
                ->post($subscription->url, $payload);

            $delivery->update([
                'status' => $response->successful() ? 'delivered' : 'failed',
                'attempts' => 1,
                'response' => $response->body(),
                'response_status' => $response->status(),
                'delivered_at' => now(),
            ]);
        }
    }

    private function signatureHeaders(?string $secret, array $payload): array
    {
        if (!$secret) {
            return [];
        }

        $rawPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($rawPayload === false) {
            $rawPayload = '{}';
        }

        return [
            'X-Webhook-Signature' => hash_hmac('sha256', $rawPayload, $secret),
        ];
    }
}
