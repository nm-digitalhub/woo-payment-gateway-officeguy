# WooCommerce Payment Gateway - Laravel Admin

This repository contains a WooCommerce payment gateway plugin with a modern Laravel-based admin interface.

## Architecture

The project consists of two main components:

1. **WooCommerce Plugin** (Original)
   - Located in: `includes/`, `templates/`, `officeguy-woo.php`
   - Traditional WordPress/WooCommerce payment gateway
   - Handles payment processing, tokens, subscriptions

2. **Laravel Admin Layer** (New)
   - Modern admin interface using Filament v4
   - Type-safe settings management using Spatie Laravel Settings
   - Service-oriented architecture

## Features

### Spatie Laravel Settings Integration

The payment configuration is now managed through a centralized, type-safe settings layer:

- **PaymentSettings Class** (`app/Settings/PaymentSettings.php`)
  - API credentials (api_key, secret_key, private_key, public_key)
  - Environment settings (sandbox_mode, environment)
  - Token configuration (support_tokens, token_param)
  - Merchant details (merchant_id, company_id)
  - Webhook URL

### Filament v4 Admin Panel

A complete admin interface for managing the payment gateway:

- **Transaction Management** (`app/Filament/Resources/TransactionResource.php`)
  - View and manage all payment transactions
  - Filter by status (pending, completed, failed, refunded)
  - Search and sort capabilities

- **Payment Token Management** (`app/Filament/Resources/PaymentTokenResource.php`)
  - Manage stored payment methods
  - View card details (last 4 digits, expiry date)
  - Set default payment methods

- **Settings Management** (`app/Filament/Pages/ManagePaymentSettings.php`)
  - Configure API credentials
  - Toggle sandbox mode
  - Manage token settings
  - Set webhook URLs

### Service Layer

Type-safe service classes that consume PaymentSettings:

- **PaymentService** (`app/Services/PaymentService.php`)
  - Process payment charges
  - Check sandbox mode
  - Retrieve webhook URLs

- **TokenService** (`app/Services/TokenService.php`)
  - Store and retrieve payment tokens
  - Check token support status
  - Manage token parameters (J2/J5)

- **RefundService** (`app/Services/RefundService.php`)
  - Process refunds
  - Check refund status

## Installation

### Prerequisites

- PHP ^8.1
- Composer
- WordPress with WooCommerce

### Setup

1. Install dependencies:
```bash
composer install
```

2. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

3. Run migrations:
```bash
php artisan migrate
php artisan settings:discover
```

4. Access the admin panel:
```
http://your-site.com/admin/payment
```

## Configuration

### Environment Variables

Create a `.env` file with:

```env
APP_NAME="Payment Gateway Admin"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Settings Migration

The settings are stored in the database and managed through Spatie Laravel Settings. Initial values can be set via the migration in `database/migrations/2024_01_01_000001_create_payment_settings.php`.

## Usage

### Using Settings in Code

```php
use App\Settings\PaymentSettings;
use App\Services\PaymentService;

// Inject settings into services
class MyController
{
    public function __construct(
        protected PaymentService $paymentService,
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

### Managing Settings via Admin

1. Navigate to `/admin/payment/settings`
2. Update API credentials
3. Configure environment settings
4. Save changes

Settings are automatically persisted to the database and cached.

## Testing

Run the test suite:

```bash
composer test
# or
./vendor/bin/phpunit
```

Tests cover:
- PaymentService functionality
- TokenService operations
- RefundService processing
- Settings integration

## Architecture Decisions

### Why Spatie Laravel Settings?

- **Type Safety**: All settings are strongly typed
- **Centralized**: Single source of truth for configuration
- **Cacheable**: Built-in caching support
- **Database Backed**: Settings persisted in database, not just config files
- **Versioned**: Migration-based approach for settings changes

### Why Filament v4?

- **Modern UI**: Beautiful, responsive admin interface
- **Laravel Native**: Built specifically for Laravel
- **Resource Based**: Easy to create CRUD interfaces
- **Extensible**: Easy to customize and extend
- **Active Development**: Regular updates and improvements

### Service Layer Pattern

- **Dependency Injection**: Services receive settings via DI
- **Testable**: Easy to mock and test
- **Reusable**: Services can be used across controllers, commands, jobs
- **Separation of Concerns**: Business logic separated from presentation

## Migration Notes

### From WooCommerce Plugin Config

The original WooCommerce plugin used:
- `config('payment.api_key')` → Now `$settings->api_key`
- `env('PAYMENT_SECRET')` → Now `$settings->secret_key`
- Direct DB/option access → Now `PaymentSettings` class

### Breaking Changes

1. **Filament v3 → v4**: If you were using Filament v3, all resources/pages need updates
2. **Config Access**: Direct `config()` calls for payment settings should use `PaymentSettings`
3. **Environment Variables**: Payment-specific env vars should be set via admin UI

## Integration with WooCommerce

The Laravel admin layer can coexist with the WooCommerce plugin:

1. WooCommerce plugin handles frontend payment processing
2. Laravel admin provides backend management interface
3. Settings are shared between both systems via database

## Support

For issues or questions:
- Check the documentation in this README
- Review the Filament v4 documentation: https://filamentphp.com/docs
- Review Spatie Laravel Settings: https://github.com/spatie/laravel-settings

## License

Same as the original WooCommerce plugin.
