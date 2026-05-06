<?php

declare(strict_types=1);

namespace LikePlatform\Webhooks\Console;

use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use LikePlatform\Webhooks\Models\WebhookDelivery;

class RetryFailedDeliveries extends Command
{
    protected $signature = 'likeplatform:webhooks-retry
                            {--max-attempts=3 : Maximum retry attempts per delivery}
                            {--delay=60 : Seconds to wait between retry batches}';

    protected $description = 'Retry failed webhook deliveries within max attempt limit';

    public function handle(): int
    {
        $maxAttempts = (int) $this->option('max-attempts');

        $failedDeliveries = WebhookDelivery::whereNull('response_status')
            ->where('attempt', '<', $maxAttempts)
            ->with('endpoint')
            ->get();

        if ($failedDeliveries->isEmpty()) {
            $this->info('No failed deliveries to retry.');

            return self::SUCCESS;
        }

        $this->info("Found {$failedDeliveries->count()} failed deliveries to retry.");

        $retried = 0;
        $failed = 0;

        foreach ($failedDeliveries as $delivery) {
            $endpoint = $delivery->endpoint;

            if (!$endpoint || !$endpoint->is_active) {
                continue;
            }

            $attempt = $delivery->attempt + 1;
            $start = microtime(true);

            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'X-LikePlatform-Event' => $delivery->event,
                        'X-LikePlatform-Signature' => hash_hmac('sha256', json_encode($delivery->payload), (string) $endpoint->secret),
                    ])
                    ->post($endpoint->url, $delivery->payload);

                $delivery->update([
                    'response_status' => $response->status(),
                    'response_body' => mb_substr((string) $response->body(), 0, 5000),
                    'duration_ms' => (int) ((microtime(true) - $start) * 1000),
                    'attempt' => $attempt,
                    'error' => null,
                    'updated_at' => now(),
                ]);

                $retried++;
                $this->line("  <info>[OK]</info> {$delivery->event} -> {$endpoint->url} (status: {$response->status()})");
            } catch (RequestException $e) {
                $delivery->update([
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                    'updated_at' => now(),
                ]);

                $failed++;
                $this->line("  <error>[FAIL]</error> {$delivery->event} -> {$endpoint->url} ({$e->getMessage()})");
            } catch (\Throwable $e) {
                $delivery->update([
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                    'updated_at' => now(),
                ]);

                $failed++;
                $this->line("  <error>[FAIL]</error> {$delivery->event} -> {$endpoint->url} ({$e->getMessage()})");
            }
        }

        $this->info("Retried: {$retried} succeeded, {$failed} failed.");

        return self::SUCCESS;
    }
}
