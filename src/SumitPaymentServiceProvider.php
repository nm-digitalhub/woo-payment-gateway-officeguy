<?php

namespace NmDigitalHub\SumitPayment;

use Illuminate\Support\ServiceProvider;
use NmDigitalHub\SumitPayment\Services\ApiService;
use NmDigitalHub\SumitPayment\Services\PaymentService;
use NmDigitalHub\SumitPayment\Services\TokenService;
use NmDigitalHub\SumitPayment\Services\RecurringBillingService;
use NmDigitalHub\SumitPayment\Services\StockService;
use NmDigitalHub\SumitPayment\Services\DonationService;
use NmDigitalHub\SumitPayment\Services\MarketplaceService;
use NmDigitalHub\SumitPayment\Settings\SumitPaymentSettings;

class SumitPaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge package configuration (keep for backwards compatibility)
        $this->mergeConfigFrom(
            __DIR__.'/../config/sumit-payment.php',
            'sumit-payment'
        );

        // Register Settings as singleton
        $this->app->singleton(SumitPaymentSettings::class);

        // Register services as singletons with Settings injection
        $this->app->singleton(ApiService::class, function ($app) {
            return new ApiService($app->make(SumitPaymentSettings::class));
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService(
                $app->make(ApiService::class),
                $app->make(SumitPaymentSettings::class)
            );
        });

        $this->app->singleton(TokenService::class, function ($app) {
            return new TokenService(
                $app->make(ApiService::class),
                $app->make(SumitPaymentSettings::class)
            );
        });

        $this->app->singleton(RecurringBillingService::class, function ($app) {
            return new RecurringBillingService(
                $app->make(ApiService::class),
                $app->make(SumitPaymentSettings::class)
            );
        });

        $this->app->singleton(StockService::class, function ($app) {
            return new StockService(
                $app->make(ApiService::class),
                $app->make(SumitPaymentSettings::class)
            );
        });

        $this->app->singleton(DonationService::class, function ($app) {
            return new DonationService($app->make(SumitPaymentSettings::class));
        });

        $this->app->singleton(MarketplaceService::class, function ($app) {
            return new MarketplaceService(
                $app->make(ApiService::class),
                $app->make(SumitPaymentSettings::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/sumit-payment.php' => config_path('sumit-payment.php'),
        ], 'sumit-payment-config');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sumit-payment');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/sumit-payment'),
        ], 'sumit-payment-views');

        // Register event listeners
        $this->registerEventListeners();
    }

    /**
     * Register event listeners for payment processing
     */
    protected function registerEventListeners(): void
    {
        // Event listeners will be registered here
        // This replaces WooCommerce action/filter hooks
        
        $this->app['events']->listen(
            \NmDigitalHub\SumitPayment\Events\PaymentProcessed::class,
            \NmDigitalHub\SumitPayment\Listeners\LogPaymentSuccess::class
        );

        $this->app['events']->listen(
            \NmDigitalHub\SumitPayment\Events\PaymentFailed::class,
            \NmDigitalHub\SumitPayment\Listeners\LogPaymentFailure::class
        );
    }
}
