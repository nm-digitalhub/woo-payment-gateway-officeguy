<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Providers;

use Filament\Panel;
use Filament\PanelProvider;
use NmDigitalhub\WooPaymentGatewayAdmin\PaymentPlugin;

/**
 * PaymentPanelProvider - Panel Provider for Payment Gateway Admin
 * 
 * This provider registers the PaymentPlugin with Filament following the
 * Filament v4 plugin architecture pattern.
 * 
 * @see https://filamentphp.com/docs/4.x/plugins/panel-plugins
 */
class PaymentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel->plugin(PaymentPlugin::make());
    }
}
