# WooCommerce Payment Gateway - Laravel Admin

This repository contains a payment gateway with a modern Laravel-based admin interface powered by Filament v4 and Spatie Laravel Settings.

## Architecture

The project is a **Laravel 11/12 package** (library) with optional WooCommerce integration:

1. **Laravel Admin Layer** (Primary - Package)
   - Modern admin interface using Filament v4
   - Type-safe settings management using Spatie Laravel Settings
   - Service-oriented architecture
   - Database-driven configuration
   - Located in: `src/`, `config/`, `database/`, `resources/`

2. **WooCommerce Plugin** (Legacy - Optional Integration)
   - Traditional WordPress/WooCommerce payment gateway
   - Can optionally integrate with Laravel layer
   - Located in: `includes/`, `templates/`, `officeguy-woo.php`

## Features

### Spatie Laravel Settings Integration

The payment configuration is managed through a centralized, type-safe settings layer:

- **PaymentSettings Class** (`src/Settings/PaymentSettings.php`)
  - API credentials (api_key, secret_key, private_key, public_key)
  - Environment settings (sandbox_mode, environment)
  - Token configuration (support_tokens, token_param)
  - Merchant details (merchant_id, company_id)
  - Webhook URL
  - **Database-backed storage** (not config files)
  - **Runtime configurable** via admin interface

### Filament v4 Admin Panel

A complete standalone admin interface for managing the payment gateway, implemented as a Filament v4 Plugin following best practices:

- **Plugin Architecture** (`src/PaymentPlugin.php`)
  - Implements `Filament\Contracts\Plugin` interface
  - Clean separation of concerns
  - Follows Filament v4 plugin design patterns
  - Registered via `PaymentPlugin::make()` factory method
  - See: [Filament Plugin Documentation](https://filamentphp.com/docs/4.x/plugins/getting-started)

- **Settings Management** (`src/Filament/Pages/ManagePaymentSettings.php`)
  - Configure API credentials
  - Toggle sandbox mode
  - Manage token settings
  - Set webhook URLs
  - All changes persist to database immediately

- **Transaction Management** (Planned - awaiting Filament compatibility fix)
  - View and manage all payment transactions
  - Filter by status (pending, completed, failed, refunded)
  - Search and sort capabilities

- **Payment Token Management** (Planned - awaiting Filament compatibility fix)
  - Manage stored payment methods
  - View card details (last 4 digits, expiry date)
  - Set default payment methods

### Service Layer

Type-safe service classes that consume PaymentSettings via dependency injection:

- **PaymentService** (`src/Services/PaymentService.php`)
  - Process payment charges
  - Check sandbox mode
  - Retrieve webhook URLs

- **TokenService** (`src/Services/TokenService.php`)
  - Store and retrieve payment tokens
  - Check token support status
  - Manage token parameters (J2/J5)

- **RefundService** (`src/Services/RefundService.php`)
  - Process refunds
  - Check refund status

## Installation

### Prerequisites

- PHP ^8.2
- Composer
- Laravel ^11.0 or ^12.0
- MySQL/PostgreSQL database
- Web server (Apache/Nginx) or PHP development server

### As a Composer Package (Recommended)

1. **Install via Composer:**
```bash
composer require nm-digitalhub/woo-payment-gateway-admin
```

2. **Publish configuration and migrations:**
```bash
php artisan vendor:publish --provider="NmDigitalhub\WooPaymentGatewayAdmin\Providers\PaymentPanelProvider"
```

3. **Run migrations:**
```bash
php artisan migrate
```

### Development Setup (Clone Repository)

1. **Clone the repository:**
```bash
git clone https://github.com/nm-digitalhub/woo-payment-gateway-officeguy.git
cd woo-payment-gateway-officeguy
```

2. **Install dependencies:**
```bash
composer install
```

3. **Configure environment:**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database in `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=payment_gateway
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run migrations:**
```bash
php artisan migrate
```

6. **Create an admin user (optional for now):**
```bash
php artisan tinker
>>> $user = new NmDigitalhub\WooPaymentGatewayAdmin\Models\User();
>>> $user->name = 'Admin';
>>> $user->email = 'admin@example.com';
>>> $user->password = bcrypt('password');
>>> $user->save();
```

7. **Serve the application:**
```bash
php artisan serve
```

8. **Access the admin panel:**
```
http://localhost:8000/admin/payment
```

### WordPress/WooCommerce Integration (Optional)

If you also want to use the WooCommerce plugin:

1. Copy this repository to your WordPress plugins directory
2. Activate the "SUMIT Payment Gateway" plugin in WordPress admin
3. Configure WooCommerce settings
4. The plugin can optionally share settings with the Laravel layer via database

## Configuration

### Environment Variables

Key environment variables in `.env`:

```env
APP_NAME="Payment Gateway Admin"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...  # Generated by php artisan key:generate
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=payment_gateway
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Spatie Settings
SETTINGS_CACHE_ENABLED=false  # Set to true in production

# Session/Cache
SESSION_DRIVER=database
CACHE_DRIVER=database
```

### Database-Driven Settings

Unlike traditional config files, payment settings are stored in the database and managed through the Filament admin interface:

1. Navigate to `/admin/payment/settings`
2. Update API credentials, environment, token settings
3. Click "Save"
4. Changes are immediately available to all services

## Usage

### Using Settings in Code

```php
use NmDigitalhub\WooPaymentGatewayAdmin\Settings\PaymentSettings;
use NmDigitalhub\WooPaymentGatewayAdmin\Services\PaymentService;

// Inject settings into services (automatic via Laravel container)
class MyController
{
    public function __construct(
        protected PaymentService $paymentService,
        protected PaymentSettings $settings,
    ) {}

    public function charge()
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

### Managing Settings via Admin

1. Navigate to `/admin/payment/settings`
2. Update API credentials
3. Configure environment settings
4. Save changes (persists to database immediately)

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit
# or
php artisan test
```

Tests cover:
- PaymentService functionality
- TokenService operations
- RefundService processing
- Settings integration via dependency injection

## Architecture Decisions

### Why Laravel Package Structure?

- **Reusability**: Can be installed in any Laravel application via Composer
- **Framework Integration**: Integrates seamlessly with Laravel 11/12
- **Modern Development**: Use latest Laravel features
- **Better Testing**: Easy to unit test without WordPress
- **Flexibility**: Can integrate with any frontend or framework
- **Portability**: Not tied to a specific application structure

### Why Spatie Laravel Settings?

- **Type Safety**: All settings are strongly typed
- **Runtime Configuration**: Change settings without code deployment
- **Database Backed**: Settings persisted in database, not config files
- **Centralized**: Single source of truth
- **Cacheable**: Built-in caching support
- **Versioned**: Migration-based approach for settings changes

### Why Filament v4?

- **Modern UI**: Beautiful, responsive admin interface
- **Laravel Native**: Built specifically for Laravel
- **Resource Based**: Easy to create CRUD interfaces
- **Extensible**: Easy to customize
- **Active Development**: Regular updates

### Why Filament v4 Plugin Architecture?

- **Best Practices**: Follows official Filament v4 plugin patterns
- **Modularity**: Clean separation between provider and plugin logic
- **Reusability**: Plugin can be registered in multiple panels if needed
- **Maintainability**: Clear structure makes code easier to understand and maintain
- **Standards Compliance**: Aligns with Filament documentation and community standards
- **Future-Proof**: Compatible with future Filament updates and patterns

### Service Layer Pattern

- **Dependency Injection**: Services receive settings via DI
- **Testable**: Easy to mock and test with PHPUnit
- **Reusable**: Services work across controllers, commands, jobs
- **Separation of Concerns**: Business logic separated from presentation

## Migration from WooCommerce Plugin

If you're migrating from using only the WooCommerce plugin:

### Before (WooCommerce Plugin Only)
- Settings stored in WordPress options
- Configuration via WooCommerce admin pages
- Tightly coupled to WordPress

### After (Laravel Standalone)
- Settings stored in Laravel database
- Configuration via Filament admin interface
- Can run independently or alongside WordPress
- WooCommerce plugin becomes optional integration layer

### Migration Steps

1. Set up Laravel application (follow installation steps above)
2. Migrate settings data from WordPress to Laravel database
3. Update any custom integrations to use Laravel services
4. WooCommerce plugin can continue to work via shared database

## Database Schema

### Core Tables

- `users` - Admin users with Filament access
- `transactions` - Payment transaction records
- `payment_tokens` - Stored payment methods
- `settings` - Spatie Laravel Settings storage
- `cache` - Database cache for settings/sessions
- `sessions` - User session storage

## API Integration

The service layer provides a clean API for payment operations:

```php
// Process a payment
$paymentService->charge($paymentData);

// Store a token
$tokenService->storeToken($userId, $tokenData);

// Process a refund
$refundService->processRefund($transactionId, $amount);
```

All services automatically use current settings from the database.

## Development

### Adding New Settings

1. Add property to `src/Settings/PaymentSettings.php`
2. Create migration in `database/migrations/`
3. Run migration: `php artisan migrate`
4. Update admin form in `src/Filament/Pages/ManagePaymentSettings.php`

### Adding New Services

1. Create service class in `src/Services/`
2. Inject `PaymentSettings` via constructor
3. Register in service container if needed
4. Use via dependency injection

## Known Issues

### Filament v4 Compatibility

Due to PHP 8.3 property type strictness and Filament v4.2, resource/page auto-discovery is temporarily disabled. This is a known upstream issue being tracked. The core functionality (Settings management, Services) works perfectly.

Workaround: Manually register resources/pages in Panel Provider when needed.

## Support

For issues or questions:
- Check this README
- Review Laravel 11 documentation: https://laravel.com/docs/11.x
- Review Filament v4 documentation: https://filamentphp.com/docs/4.x
- Review Spatie Laravel Settings: https://github.com/spatie/laravel-settings
- Open an issue on GitHub

## License

Same as the original WooCommerce plugin.
