# Plugin Integration Guide

This guide shows you how to integrate the WooCommerce Payment Gateway Admin plugin into your Laravel Filament application.

## Prerequisites

- Laravel 11 or 12
- Filament v4 already installed
- An existing Filament admin panel configured

## Installation

### Step 1: Install the Package

```bash
composer require nm-digitalhub/woo-payment-gateway-admin
```

### Step 2: Publish Assets (Optional)

```bash
# Publish migrations and configuration
php artisan vendor:publish --provider="NmDigitalhub\WooPaymentGatewayAdmin\Providers\PaymentServiceProvider"
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

## Integration with Your Admin Panel

### Register the Plugin

Edit your existing admin panel provider file:

**File:** `app/Providers/Filament/AdminPanelProvider.php`

```php
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use NmDigitalhub\WooPaymentGatewayAdmin\PaymentPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([
                // Your pages
            ])
            ->widgets([
                // Your widgets
            ])
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
            // ✅ Add this line to register the Payment Gateway plugin
            ->plugin(PaymentPlugin::make());
    }
}
```

**That's it!** The payment gateway features are now integrated into your admin panel.

## What Gets Added to Your Admin Panel

Once registered, the plugin adds the following to your admin navigation:

### Resources

1. **Transactions** - Manage payment transactions
   - View all transactions
   - Filter by status (pending, completed, failed, refunded)
   - Edit transaction details
   - Search and sort

2. **Payment Tokens** - Manage stored payment methods
   - View saved payment tokens
   - Manage card details
   - Set default payment methods

### Pages

3. **Payment Settings** - Configure payment gateway
   - API credentials (API key, secret key, public/private keys)
   - Environment settings (sandbox mode, environment)
   - Token configuration
   - Merchant details
   - Webhook URL

## Accessing the Features

After registration, you can access the payment features at:

- **Admin Panel:** `http://your-domain.com/admin`
- **Transactions:** Navigation menu → "Transactions"
- **Payment Tokens:** Navigation menu → "Payment Tokens"
- **Settings:** Navigation menu → "Payment Settings"

## Customization

### Navigation Grouping

If you want to group all payment-related items under a navigation group, you can customize the resources:

```php
// In your AppServiceProvider or a custom provider
use NmDigitalhub\WooPaymentGatewayAdmin\Filament\Resources\TransactionResource;
use NmDigitalhub\WooPaymentGatewayAdmin\Filament\Resources\PaymentTokenResource;

// Set navigation group for resources
TransactionResource::$navigationGroup = 'Payments';
PaymentTokenResource::$navigationGroup = 'Payments';
```

### Navigation Icons

Customize icons for the resources:

```php
TransactionResource::$navigationIcon = 'heroicon-o-credit-card';
PaymentTokenResource::$navigationIcon = 'heroicon-o-key';
```

### Navigation Sort Order

Control the order of items in the navigation:

```php
TransactionResource::$navigationSort = 1;
PaymentTokenResource::$navigationSort = 2;
```

## Configuration

### Settings Management

The plugin uses Spatie Laravel Settings for configuration. Settings are stored in the database and can be managed through the admin interface.

To access settings programmatically:

```php
use NmDigitalhub\WooPaymentGatewayAdmin\Settings\PaymentSettings;

class MyController
{
    public function __construct(
        protected PaymentSettings $settings
    ) {}

    public function process()
    {
        $apiKey = $this->settings->api_key;
        $isSandbox = $this->settings->sandbox_mode;
    }
}
```

### Service Layer

The plugin provides service classes for common operations:

```php
use NmDigitalhub\WooPaymentGatewayAdmin\Services\PaymentService;
use NmDigitalhub\WooPaymentGatewayAdmin\Services\TokenService;
use NmDigitalhub\WooPaymentGatewayAdmin\Services\RefundService;

class PaymentController
{
    public function __construct(
        protected PaymentService $paymentService,
        protected TokenService $tokenService,
        protected RefundService $refundService,
    ) {}

    public function charge()
    {
        $result = $this->paymentService->charge([
            'amount' => 100.00,
            'currency' => 'USD',
        ]);
    }
}
```

## Troubleshooting

### Plugin Not Showing Up

If the payment gateway features don't appear in your admin panel:

1. **Clear caches:**
   ```bash
   php artisan filament:clear-cache
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. **Verify the plugin is registered:**
   Check your `AdminPanelProvider.php` has the `->plugin(PaymentPlugin::make())` line.

3. **Check Composer autoload:**
   ```bash
   composer dump-autoload
   ```

### Settings Not Persisting

If settings don't save:

1. **Run migrations:**
   ```bash
   php artisan migrate
   ```

2. **Check the settings table exists:**
   ```sql
   SELECT * FROM settings;
   ```

3. **Verify Spatie Settings configuration:**
   Check `config/settings.php` exists.

### Resource Discovery Issues

If resources aren't discovered:

1. **Check file permissions** - Ensure PHP can read the `vendor` directory
2. **Verify namespace** - Resources should be in the correct namespace
3. **Clear Filament cache:**
   ```bash
   php artisan filament:clear-cache
   ```

## Migration from Separate Panel

If you previously used this package when it created a separate panel at `/admin/payment`, you need to update:

### Before (Old - Separate Panel)
```php
// ❌ This is no longer needed - remove it
use NmDigitalhub\WooPaymentGatewayAdmin\Providers\PaymentPanelProvider;

// The package auto-registered a separate panel
```

### After (New - Plugin Integration)
```php
// ✅ Register as a plugin in your existing admin panel
use NmDigitalhub\WooPaymentGatewayAdmin\PaymentPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->plugin(PaymentPlugin::make())
        // ... rest of configuration
}
```

### Update Routes

- ❌ Old: `/admin/payment/transactions`
- ✅ New: `/admin/transactions`

The plugin now integrates into your existing admin panel's routing structure.

## Support

For issues or questions:

1. Check the [README.md](README.md) for general information
2. Review the [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md) for detailed migration instructions
3. Check the Filament documentation: https://filamentphp.com/docs
4. Open an issue on GitHub

## Example Application

Here's a minimal Laravel application setup with the plugin:

### Composer.json
```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0",
        "filament/filament": "^4.1",
        "nm-digitalhub/woo-payment-gateway-admin": "^1.0"
    }
}
```

### .env
```env
APP_NAME="My Payment App"
DB_CONNECTION=mysql
DB_DATABASE=my_database
```

### AdminPanelProvider.php
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
            ->plugin(PaymentPlugin::make());
    }
}
```

That's all you need! The plugin will handle the rest.
