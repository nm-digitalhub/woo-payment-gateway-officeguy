<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Providers;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use NmDigitalhub\WooPaymentGatewayAdmin\PaymentPlugin;

/**
 * AdminPanelProvider - Filament Panel Configuration
 * 
 * This provider configures the payment gateway admin panel and registers
 * the PaymentPlugin following Filament v4 best practices.
 * 
 * The panel is accessible at: /admin/payment
 * 
 * Architecture:
 * - Panel configuration (path, colors, middleware) is handled here
 * - Feature registration (resources, pages, widgets) is handled by PaymentPlugin
 * - This separation allows the plugin to be reused in multiple panels if needed
 * 
 * @see PaymentPlugin
 * @package NmDigitalhub\WooPaymentGatewayAdmin\Providers
 */
class AdminPanelProvider extends PanelProvider
{
    /**
     * Configure the Filament panel.
     * 
     * This method sets up the admin panel configuration including:
     * - Panel ID and URL path
     * - Color scheme
     * - PaymentPlugin registration
     * - Middleware stack
     * - Authentication requirements
     *
     * @param Panel $panel The Filament panel instance
     * @return Panel The configured panel
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('payment')
            ->path('admin/payment')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->plugin(PaymentPlugin::make())
            ->middleware([
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            ])
            ->authMiddleware([
                \Illuminate\Auth\Middleware\Authenticate::class,
            ]);
    }
}
