<?php

declare(strict_types=1);

namespace LikePlatform\Webhooks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Webhook endpoint registered by a user.
 *
 * Each endpoint subscribes to specific events and receives
 * HTTP POST payloads when those events fire.
 */
class WebhookEndpoint extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'url',
        'secret',
        'events',
        'is_active',
        'last_sent_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'events' => 'array',
            'is_active' => 'boolean',
            'last_sent_at' => 'datetime',
        ];
    }

    /**
     * Get the delivery attempts for this endpoint.
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }

    /**
     * Check if this endpoint is subscribed to a given event.
     */
    public function isSubscribedTo(string $event): bool
    {
        return in_array($event, $this->events ?? [], true);
    }

    /**
     * Scope for active endpoints only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
