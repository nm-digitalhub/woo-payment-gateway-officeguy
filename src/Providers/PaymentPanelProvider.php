<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Providers;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;

/**
 * @deprecated This class is deprecated as of the plugin refactoring.
 * Use AdminPanelProvider with PaymentPlugin instead.
 * This file is kept for backward compatibility but should not be used in new implementations.
 * 
 * @see AdminPanelProvider
 * @see \NmDigitalhub\WooPaymentGatewayAdmin\PaymentPlugin
 */
class PaymentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('payment')
            ->path('admin/payment')
            ->colors([
                'primary' => Color::Amber,
            ])
            // TODO: Re-enable after fixing Filament v4 property type compatibility
            // See: https://github.com/filamentphp/filament/discussions/...
            // ->discoverResources(in: __DIR__ . '/../Filament/Resources', for: 'NmDigitalhub\\WooPaymentGatewayAdmin\\Filament\\Resources')
            // ->discoverPages(in: __DIR__ . '/../Filament/Pages', for: 'NmDigitalhub\\WooPaymentGatewayAdmin\\Filament\\Pages')
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
            ]);
    }
}
