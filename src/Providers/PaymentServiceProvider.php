<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * PaymentServiceProvider - Laravel Service Provider for Payment Gateway Admin Package
 * 
 * This service provider handles the registration and bootstrapping of the
 * payment gateway admin package, including settings, migrations, and views.
 * 
 * Following Laravel package development best practices:
 * https://laravel.com/docs/11.x/packages
 */
class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * 
     * This method is used to bind things into the service container.
     * 
     * @return void
     */
    public function register(): void
    {
        // Merge package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/settings.php',
            'settings'
        );
    }

    /**
     * Bootstrap services.
     * 
     * This method is called after all other service providers have been registered.
     * Use this for publishing assets, loading migrations, views, etc.
     * 
     * @return void
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'payment-gateway-admin');

        // Publishing configuration (optional for end-users)
        if ($this->app->runningInConsole()) {
            // Publish configuration
            $this->publishes([
                __DIR__ . '/../../config/settings.php' => config_path('settings.php'),
            ], 'payment-gateway-config');

            // Publish migrations
            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'payment-gateway-migrations');

            // Publish views
            $this->publishes([
                __DIR__ . '/../../resources/views' => resource_path('views/vendor/payment-gateway-admin'),
            ], 'payment-gateway-views');
        }
    }
}
