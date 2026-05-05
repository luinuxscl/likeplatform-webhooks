<?php

declare(strict_types=1);

namespace LikePlatform\Webhooks\Contracts;

use LikePlatform\Contracts\Webhooks\WebhookEventContract;

/**
 * Implementation of a webhook event that can be subscribed to.
 */
final readonly class WebhookEvent implements WebhookEventContract
{
    public function __construct(
        private string $key,
        private string $name,
        private string $description,
        private array $payloadSchema = [],
    ) {}

    public function key(): string
    {
        return $this->key;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function payloadSchema(): array
    {
        return $this->payloadSchema;
    }
}
