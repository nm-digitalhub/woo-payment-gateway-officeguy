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
 * This is a true Filament plugin that integrates into an existing admin panel,
 * NOT a separate panel provider. It should be registered in your existing
 * admin panel like this:
 * 
 * ```php
 * // app/Providers/Filament/AdminPanelProvider.php
 * public function panel(Panel $panel): Panel
 * {
 *     return $panel
 *         ->id('admin')
 *         ->plugin(PaymentPlugin::make())
 *         // ... other panel configuration
 * }
 * ```
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
        return 'woo-payment-gateway-admin';
    }

    /**
     * Register the plugin with the Filament panel.
     * 
     * This method registers resources, pages, and widgets with the panel.
     * Note: This is a plugin, not a panel provider, so we do NOT configure
     * panel-level settings like id, path, colors, or middleware here.
     * Those should be configured in the main admin panel.
     * 
     * @param Panel $panel
     * @return void
     */
    public function register(Panel $panel): void
    {
        $panel
            // Register resources - discover all payment gateway resources
            ->discoverResources(
                in: __DIR__ . '/Filament/Resources',
                for: 'NmDigitalhub\\WooPaymentGatewayAdmin\\Filament\\Resources'
            )
            // Register pages - discover all payment gateway pages
            ->discoverPages(
                in: __DIR__ . '/Filament/Pages',
                for: 'NmDigitalhub\\WooPaymentGatewayAdmin\\Filament\\Pages'
            )
            // Register widgets - discover all payment gateway widgets
            ->discoverWidgets(
                in: __DIR__ . '/Filament/Widgets',
                for: 'NmDigitalhub\\WooPaymentGatewayAdmin\\Filament\\Widgets'
            );
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
