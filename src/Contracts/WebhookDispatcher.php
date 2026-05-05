<?php

declare(strict_types=1);

namespace LikePlatform\Webhooks\Contracts;

use LikePlatform\Contracts\Webhooks\WebhookDispatcherContract;
use LikePlatform\Webhooks\Models\WebhookEndpoint;
use LikePlatform\Webhooks\Models\WebhookDelivery;
use Illuminate\Support\Facades\Http;

/**
 * Implementation of the webhook dispatcher contract.
 *
 * Dispatches webhook events to all subscribed URLs using HTTP POST.
 * Records each delivery attempt with status, duration, and errors.
 */
final readonly class WebhookDispatcher implements WebhookDispatcherContract
{
    /**
     * Dispatch a webhook event to all subscribed URLs.
     *
     * @param string $event Event name (e.g., 'user.created', 'api_key.revoked')
     * @param array<string, mixed> $payload Event payload data
     */
    public function dispatch(string $event, array $payload): void
    {
        $endpoints = WebhookEndpoint::active()
            ->whereJsonContains('events', $event)
            ->get();

        foreach ($endpoints as $endpoint) {
            $this->sendToEndpoint($endpoint, $event, $payload);
        }
    }

    /**
     * Get all URLs subscribed to a given event.
     *
     * @param string $event Event name
     * @return array<string> List of subscribed webhook URLs
     */
    public function getSubscribedUrls(string $event): array
    {
        return WebhookEndpoint::active()
            ->whereJsonContains('events', $event)
            ->pluck('url')
            ->toArray();
    }

    /**
     * Send payload to a specific endpoint and record the delivery.
     */
    private function sendToEndpoint(WebhookEndpoint $endpoint, string $event, array $payload): void
    {
        $start = microtime(true);

        try {
            $response = Http::timeout(10)
                ->withHeader('X-LikePlatform-Event', $event)
                ->withHeader('X-LikePlatform-Signature', $this->sign($endpoint->secret, $payload))
                ->post($endpoint->url, $payload);

            $status = $response->status();
            $body = $response->body();
            $error = null;
        } catch (\Throwable $e) {
            $status = 0;
            $body = null;
            $error = $e->getMessage();
        }

        $duration = (int) ((microtime(true) - $start) * 1000);

        WebhookDelivery::create([
            'webhook_endpoint_id' => $endpoint->id,
            'event' => $event,
            'payload' => $payload,
            'response_status' => $status,
            'response_body' => $body,
            'duration_ms' => $duration,
            'error' => $error,
            'attempt' => 1,
        ]);

        $endpoint->update(['last_sent_at' => now()]);
    }

    /**
     * Generate HMAC signature for the payload.
     */
    private function sign(string $secret, array $payload): string
    {
        return hash_hmac('sha256', json_encode($payload), $secret);
    }
}
