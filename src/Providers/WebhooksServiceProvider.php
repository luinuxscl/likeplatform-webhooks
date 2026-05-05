<?php

declare(strict_types=1);

namespace LikePlatform\Webhooks\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider for the LikePlatform Webhooks package.
 *
 * Registers routes, migrations, config, and translations.
 * Binds contract implementations in the container.
 */
class WebhooksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/webhooks.php', 'likeplatform-webhooks'
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/webhooks.php');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'likeplatform-webhooks');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/webhooks.php' => config_path('likeplatform-webhooks.php'),
            ], 'likeplatform-webhooks-config');
        }
    }
}
