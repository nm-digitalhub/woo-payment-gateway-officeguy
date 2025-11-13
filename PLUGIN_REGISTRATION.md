# Plugin Registration Guide

## Overview

The `woo-payment-gateway-admin` package is now a portable, reusable Filament plugin that follows Laravel and Filament best practices. This means you have full control over how and where the plugin is integrated into your application.

## Quick Start

### 1. Install the Package

```bash
composer require nm-digitalhub/woo-payment-gateway-admin
```

The package uses Laravel's auto-discovery feature, so the `PaymentServiceProvider` will be automatically registered.

### 2. Register the Plugin in Your Panel

In your Filament panel provider (e.g., `app/Providers/Filament/AdminPanelProvider.php`), register the payment plugin:

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
            // ... your other panel configuration
            ->plugin(PaymentPlugin::make());  // ← Add this line
    }
}
```

### 3. Run Migrations

```bash
php artisan migrate
```

That's it! The payment gateway admin interface is now integrated into your Filament panel.

## What Changed?

### Before (Old Approach)
- Package included `PaymentPanelProvider` that defined its own panel configuration
- Panel configuration (id, path, colors, middleware) was hardcoded in the plugin
- Limited flexibility for end-users to customize

### After (Current Approach)
- Package provides `PaymentPlugin` that only registers its resources
- End-users manually register the plugin in their own panel provider
- Full control over panel configuration (id, path, colors, middleware)
- Standard Laravel `ServiceProvider` for package setup

## Benefits

1. **Portability**: Use the plugin in any Laravel/Filament application
2. **Flexibility**: Full control over panel configuration
3. **Standards Compliance**: Follows Laravel and Filament best practices
4. **No Conflicts**: Plugin doesn't impose its own panel configuration
5. **Reusability**: Can be registered in multiple panels if needed

## Optional: Publishing Assets

If you want to customize configuration, migrations, or views:

```bash
# Publish configuration
php artisan vendor:publish --tag="payment-gateway-config"

# Publish migrations (if you want to customize them)
php artisan vendor:publish --tag="payment-gateway-migrations"

# Publish views (if you want to customize them)
php artisan vendor:publish --tag="payment-gateway-views"
```

## Panel Configuration Examples

### Example 1: Dedicated Payment Admin Panel

If you want a completely separate panel for payment administration:

```php
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use NmDigitalhub\WooPaymentGatewayAdmin\PaymentPlugin;

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
            ->login()
            ->plugin(PaymentPlugin::make());
    }
}
```

### Example 2: Integrated into Main Admin Panel

If you want to integrate payment features into your existing admin panel:

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
            // ... your existing resources
            ->plugin(PaymentPlugin::make());
    }
}
```

## Need Help?

- Check the [README.md](README.md) for detailed documentation
- Review [Filament Plugin Documentation](https://filamentphp.com/docs/4.x/plugins/getting-started)
- Review [Laravel Package Development](https://laravel.com/docs/11.x/packages)
