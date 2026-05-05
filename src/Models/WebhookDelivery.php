<?php

declare(strict_types=1);

namespace LikePlatform\Webhooks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Record of a webhook delivery attempt.
 *
 * Tracks each HTTP request sent to an endpoint, including
 * response status, duration, and error details.
 */
class WebhookDelivery extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'webhook_endpoint_id',
        'event',
        'payload',
        'response_status',
        'response_body',
        'duration_ms',
        'error',
        'attempt',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'duration_ms' => 'integer',
            'attempt' => 'integer',
        ];
    }

    /**
     * Get the endpoint this delivery belongs to.
     */
    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(WebhookEndpoint::class, 'webhook_endpoint_id');
    }

    /**
     * Check if this delivery was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->response_status >= 200 && $this->response_status < 300;
    }

    /**
     * Mark this delivery as failed.
     */
    public function hasFailed(): bool
    {
        return !$this->isSuccessful();
    }
}
