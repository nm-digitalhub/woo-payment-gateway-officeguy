<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use NmDigitalhub\WooPaymentGatewayAdmin\Filament\Pages\ManagePaymentSettings;
use NmDigitalhub\WooPaymentGatewayAdmin\Filament\Resources\TransactionResource;
use NmDigitalhub\WooPaymentGatewayAdmin\Filament\Resources\PaymentTokenResource;

class PaymentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'payment';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                // Resources are currently disabled due to Filament v4 compatibility
                // TODO: Re-enable after fixing Filament v4 property type compatibility
                // TransactionResource::class,
                // PaymentTokenResource::class,
            ])
            ->pages([
                ManagePaymentSettings::class,
            ])
            ->discoverWidgets(
                in: __DIR__ . '/Filament/Widgets',
                for: 'NmDigitalhub\\WooPaymentGatewayAdmin\\Filament\\Widgets'
            );
    }

    public function boot(Panel $panel): void
    {
        // Plugin boot logic can go here if needed
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return static::make();
    }
}
