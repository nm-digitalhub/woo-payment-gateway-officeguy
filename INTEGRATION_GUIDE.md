# PaymentPlugin Integration Guide

This guide shows how to integrate the PaymentPlugin into an existing Filament admin panel.

## Overview

The PaymentPlugin is a Filament v4 plugin that provides payment gateway management functionality. It follows the plugin architecture pattern, integrating seamlessly into your existing admin panel without creating a separate panel.

## Integration Steps

### 1. Install the Package

```bash
composer require nm-digitalhub/woo-payment-gateway-admin
```

### 2. Publish Migrations

```bash
php artisan vendor:publish --tag="settings-migrations"
php artisan migrate
```

### 3. Create Admin Panel (if needed)

If you don't already have a Filament admin panel:

```bash
php artisan make:filament-panel admin
```

This creates `app/Providers/Filament/AdminPanelProvider.php`.

### 4. Register the Plugin

Open your `app/Providers/Filament/AdminPanelProvider.php` and add the plugin:

```php
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use NmDigitalhub\WooPaymentGatewayAdmin\PaymentPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->plugin(PaymentPlugin::make()) // Add this line
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
```

### 5. Create Admin User

```bash
php artisan make:filament-user
```

### 6. Access the Admin Panel

Start your development server:

```bash
php artisan serve
```

Navigate to `http://localhost:8000/admin` and log in.

You'll see the following resources in your admin panel navigation:
- **Payment Settings** - Configure API credentials, environment, and token settings
- **Transactions** - View and manage payment transactions
- **Payment Tokens** - Manage stored payment methods

## What the Plugin Provides

### Resources

1. **PaymentTokenResource** - Manage payment tokens
   - View stored payment methods
   - See card details (last 4 digits, expiry)
   - Set default payment methods

2. **TransactionResource** - Manage transactions
   - View all payment transactions
   - Filter by status (pending, completed, failed, refunded)
   - Search and sort capabilities

### Pages

1. **ManagePaymentSettings** - Configure payment gateway settings
   - API credentials (api_key, secret_key, private_key, public_key)
   - Environment settings (sandbox_mode, environment)
   - Token configuration (support_tokens, token_param)
   - Merchant details (merchant_id, company_id)
   - Webhook URL

## Using Settings in Your Code

The plugin provides type-safe settings through dependency injection:

```php
use NmDigitalhub\WooPaymentGatewayAdmin\Settings\PaymentSettings;
use NmDigitalhub\WooPaymentGatewayAdmin\Services\PaymentService;

class MyController
{
    public function __construct(
        protected PaymentService $paymentService,
        protected PaymentSettings $settings,
    ) {}

    public function processPayment()
    {
        // Service automatically uses current settings from database
        $result = $this->paymentService->charge([
            'amount' => 100.00,
            'currency' => 'USD',
        ]);
        
        // Or access settings directly
        $apiKey = $this->settings->api_key;
        $isSandbox = $this->settings->sandbox_mode;
    }
}
```

## Architecture

The plugin follows Filament's plugin architecture:

- **Does NOT** create a separate admin panel
- **Does NOT** configure panel-level settings (id, path, middleware)
- **DOES** register resources, pages, and widgets into your existing panel
- **DOES** follow dependency injection patterns
- **DOES** use database-backed settings (not config files)

## Troubleshooting

### Plugin not showing in admin panel

Make sure you:
1. Cleared Laravel's cache: `php artisan cache:clear`
2. Rebuilt optimizations: `php artisan optimize:clear`
3. Restarted your development server

### Database errors

Make sure you ran migrations:
```bash
php artisan migrate
```

### Settings not saving

Check that the `settings` table exists in your database and that the Spatie Settings package is properly configured.

## Advanced Configuration

### Customizing Plugin Behavior

You can extend the PaymentPlugin class to customize its behavior:

```php
use NmDigitalhub\WooPaymentGatewayAdmin\PaymentPlugin;

class CustomPaymentPlugin extends PaymentPlugin
{
    public function boot(Panel $panel): void
    {
        parent::boot($panel);
        
        // Add custom boot logic here
    }
}
```

Then register your custom plugin:

```php
->plugin(CustomPaymentPlugin::make())
```

## Support

For issues or questions:
- Open an issue on GitHub
- Check the main README.md for more documentation
- Review the MIGRATION_GUIDE.md for upgrade information
