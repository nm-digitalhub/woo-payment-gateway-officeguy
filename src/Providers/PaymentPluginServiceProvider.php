<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * PaymentPluginServiceProvider - Laravel Package Service Provider
 * 
 * This service provider handles package-level concerns for the payment gateway plugin:
 * - Package configuration
 * - Asset registration
 * - Migration publishing
 * - Config file publishing
 * 
 * It follows Laravel package development best practices and uses Spatie's
 * Laravel Package Tools for streamlined package setup.
 * 
 * @see https://laravel.com/docs/packages
 * @see https://github.com/spatie/laravel-package-tools
 * @package NmDigitalhub\WooPaymentGatewayAdmin\Providers
 */
class PaymentPluginServiceProvider extends PackageServiceProvider
{
    /**
     * The package name identifier.
     *
     * @var string
     */
    public static string $name = 'woo-payment-gateway-admin';

    /**
     * Configure the package.
     * 
     * This method sets up the package configuration including:
     * - Package name
     * - Config file publishing
     * - Migration registration
     *
     * @param Package $package The package instance
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile('settings')
            ->hasMigrations([
                'create_settings_table',
                'create_payment_settings',
            ]);
    }

    /**
     * Perform post-registration booting of services.
     * 
     * This method is called after all service providers have been registered.
     * It's used to register assets and other resources that should only be
     * loaded when needed.
     * 
     * Assets are registered here (not in the Plugin class) to ensure they're
     * properly managed by Filament's asset system and only loaded when the
     * plugin is actually being used.
     *
     * @return void
     */
    public function packageBooted(): void
    {
        // Register any assets needed by the plugin
        // Assets should be registered here for proper loading
        
        // Example: Register custom CSS if needed
        // FilamentAsset::register([
        //     Css::make('payment-plugin', __DIR__.'/../../resources/dist/payment-plugin.css')
        //         ->loadedOnRequest(),
        // ], static::$name);
    }
}
