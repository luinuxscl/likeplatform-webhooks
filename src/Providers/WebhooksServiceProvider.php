<?php

declare(strict_types=1);

namespace LikePlatform\Webhooks\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use LikePlatform\Contracts\Webhooks\WebhookDispatcherContract;
use LikePlatform\Contracts\Webhooks\WebhookEventContract;
use LikePlatform\Contracts\Webhooks\WebhookReceiverContract;
use LikePlatform\Webhooks\Console\RetryFailedDeliveries;
use LikePlatform\Webhooks\Contracts\WebhookDispatcher;
use LikePlatform\Webhooks\Contracts\WebhookReceiver;

class WebhooksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/webhooks.php', 'likeplatform-webhooks'
        );

        $this->app->bind(WebhookDispatcherContract::class, WebhookDispatcher::class);
        $this->app->bind(WebhookReceiverContract::class, WebhookReceiver::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/webhooks.php');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'likeplatform-webhooks');
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'likeplatform-webhooks');

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command('likeplatform:webhooks-retry')
                ->everyFiveMinutes()
                ->withoutOverlapping();
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                RetryFailedDeliveries::class,
            ]);

            $this->publishes([
                __DIR__.'/../../config/webhooks.php' => config_path('likeplatform-webhooks.php'),
            ], 'likeplatform-webhooks-config');
        }
    }
}
