<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use NmDigitalhub\WooPaymentGatewayAdmin\Filament\Pages\ManagePaymentSettings;
use NmDigitalhub\WooPaymentGatewayAdmin\Filament\Resources\TransactionResource;
use NmDigitalhub\WooPaymentGatewayAdmin\Filament\Resources\PaymentTokenResource;

/**
 * PaymentPlugin - Filament v4 Panel Plugin
 * 
 * This plugin provides payment gateway administration functionality for Filament panels.
 * It follows the official Filament v4 plugin architecture as documented at:
 * https://filamentphp.com/docs/4.x/plugins/panel-plugins
 * 
 * The plugin can be registered in any Filament panel using:
 * $panel->plugin(PaymentPlugin::make())
 * 
 * Features:
 * - Payment settings management
 * - Transaction resource (planned)
 * - Payment token resource (planned)
 * - Custom widgets
 * 
 * @package NmDigitalhub\WooPaymentGatewayAdmin
 */
class PaymentPlugin implements Plugin
{
    /**
     * Get the unique identifier for this plugin.
     * 
     * This ID is used by Filament to register and retrieve the plugin.
     *
     * @return string The plugin identifier
     */
    public function getId(): string
    {
        return 'payment';
    }

    /**
     * Register the plugin with the panel.
     * 
     * This method is called when the panel is being set up. It registers
     * resources, pages, and widgets that should be available in the panel.
     *
     * @param Panel $panel The Filament panel instance
     * @return void
     */
    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                // Resources are currently disabled due to Filament v4 compatibility
                // TODO: Re-enable after fixing Filament v4 property type compatibility
                // See: https://github.com/filamentphp/filament/discussions/...
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

    /**
     * Boot the plugin.
     * 
     * This method is called when the plugin is being booted. It's used for
     * panel-specific initialization that should only happen when the panel
     * is actually being used.
     *
     * @param Panel $panel The Filament panel instance
     * @return void
     */
    public function boot(Panel $panel): void
    {
        // Plugin boot logic can go here if needed
        // For example: registering event listeners, setting up hooks, etc.
    }

    /**
     * Create a new plugin instance using Laravel's service container.
     * 
     * This method provides a convenient way to instantiate the plugin
     * and allows for dependency injection if needed.
     *
     * @return static A new plugin instance
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Alias for make() method.
     * 
     * Provides an alternative way to instantiate the plugin.
     *
     * @return static A new plugin instance
     */
    public static function get(): static
    {
        return static::make();
    }
}
