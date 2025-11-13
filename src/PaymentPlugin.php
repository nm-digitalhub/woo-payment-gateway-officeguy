<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin;

use Filament\Contracts\Plugin;
use Filament\Panel;

/**
 * PaymentPlugin - Filament v4 Plugin for Payment Gateway Admin Panel
 * 
 * This plugin provides a complete admin interface for managing payment gateway
 * settings, transactions, and payment tokens using Laravel Filament v4.
 * 
 * Following Filament v4 plugin best practices as documented at:
 * https://filamentphp.com/docs/4.x/plugins/getting-started
 * https://filamentphp.com/docs/4.x/plugins/panel-plugins
 */
class PaymentPlugin implements Plugin
{
    /**
     * Get the unique identifier for this plugin.
     * 
     * @return string
     */
    public function getId(): string
    {
        return 'payment';
    }

    /**
     * Register the plugin with the Filament panel.
     * 
     * This method is called during panel registration and is where we configure
     * the panel with our resources, pages, and widgets.
     * 
     * As a plugin, we do NOT configure panel-level settings like id, path, or middleware.
     * Those are managed by the host panel. We only register our resources, pages, and widgets.
     * 
     * @param Panel $panel
     * @return void
     */
    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(in: __DIR__ . '/Filament/Resources', for: 'NmDigitalhub\\WooPaymentGatewayAdmin\\Filament\\Resources')
            ->discoverPages(in: __DIR__ . '/Filament/Pages', for: 'NmDigitalhub\\WooPaymentGatewayAdmin\\Filament\\Pages')
            ->discoverWidgets(in: __DIR__ . '/Filament/Widgets', for: 'NmDigitalhub\\WooPaymentGatewayAdmin\\Filament\\Widgets');
    }

    /**
     * Boot the plugin.
     * 
     * This method is called after all plugins have been registered.
     * Use this for any initialization logic that needs to run after registration.
     * 
     * @param Panel $panel
     * @return void
     */
    public function boot(Panel $panel): void
    {
        // Optional boot logic can be added here if needed
    }

    /**
     * Create a new instance of the plugin.
     * 
     * This static factory method allows for fluent plugin instantiation:
     * $panel->plugin(PaymentPlugin::make())
     * 
     * @return static
     */
    public static function make(): static
    {
        return app(static::class);
    }
}
