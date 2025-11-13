<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Providers;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use NmDigitalhub\WooPaymentGatewayAdmin\PaymentPlugin;

/**
 * AdminPanelProvider - Main Admin Panel Provider
 * 
 * This provider creates the main admin panel and registers
 * the PaymentPlugin to it.
 */
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: __DIR__ . '/../Filament/Resources', for: 'NmDigitalhub\\WooPaymentGatewayAdmin\\Filament\\Resources')
            ->discoverPages(in: __DIR__ . '/../Filament/Pages', for: 'NmDigitalhub\\WooPaymentGatewayAdmin\\Filament\\Pages')
            ->pages([])
            ->discoverWidgets(in: __DIR__ . '/../Filament/Widgets', for: 'NmDigitalhub\\WooPaymentGatewayAdmin\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            ])
            ->authMiddleware([
                \Illuminate\Auth\Middleware\Authenticate::class,
            ])
            ->plugin(PaymentPlugin::make());
    }
}
