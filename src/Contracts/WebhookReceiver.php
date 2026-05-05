<?php

declare(strict_types=1);

namespace LikePlatform\Webhooks\Contracts;

use LikePlatform\Contracts\Webhooks\WebhookReceiverContract;
use Illuminate\Http\Request;

/**
 * Implementation of the webhook receiver contract.
 *
 * Validates HMAC signatures of incoming webhooks and
 * processes validated payloads from external services.
 */
final readonly class WebhookReceiver implements WebhookReceiverContract
{
    /**
     * Validate the HMAC signature of an incoming webhook request.
     */
    public function validateSignature(Request $request): bool
    {
        $signature = $request->header('X-LikePlatform-Signature');
        $secret = config('likeplatform-webhooks.incoming_secret', '');

        if (!$signature || !$secret) {
            return false;
        }

        $payload = $request->getContent();
        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signature);
    }

    /**
     * Process a validated webhook payload.
     *
     * @param string $source Identifier of the webhook source
     * @param array<string, mixed> $payload Decoded payload data
     */
    public function process(string $source, array $payload): void
    {
        event('likeplatform.webhook.received', [
            'source' => $source,
            'payload' => $payload,
        ]);
    }
}
