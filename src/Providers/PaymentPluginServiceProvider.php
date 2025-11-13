<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PaymentPluginServiceProvider extends PackageServiceProvider
{
    public static string $name = 'woo-payment-gateway-admin';

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
