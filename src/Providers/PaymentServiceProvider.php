<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * PaymentServiceProvider - Service Provider for Payment Gateway Admin Package
 * 
 * This provider handles package bootstrapping including:
 * - Publishing configuration files
 * - Publishing migrations
 * - Registering services
 * 
 * Note: This is NOT a panel provider. The PaymentPlugin should be manually
 * registered in your application's admin panel provider.
 */
class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register package services if needed
        // Example: $this->app->singleton(PaymentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration files
        $this->publishes([
            __DIR__ . '/../../config/payment.php' => config_path('payment.php'),
        ], 'payment-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'payment-migrations');

        // Load migrations if needed
        // $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}
