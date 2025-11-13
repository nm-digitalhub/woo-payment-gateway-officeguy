# WooCommerce Payment Gateway with Laravel Admin

A comprehensive payment gateway solution combining a WooCommerce plugin with a modern Laravel-based admin interface using Filament V4 and Spatie Laravel Settings.

## Architecture

This project supports two deployment modes:

1. **Hybrid Mode (Recommended)**: WooCommerce plugin + Laravel admin interface
2. **Standalone Laravel Package**: Pure Laravel package without WooCommerce dependencies

### Hybrid Mode Structure

The project consists of two integrated components:

1. **WooCommerce Plugin** (Legacy/Frontend)
   - Located in: `includes/`, `templates/`, `officeguy-woo.php`
   - Handles payment processing on WooCommerce checkout
   - Manages tokens, subscriptions, and frontend interactions
   
2. **Laravel Admin Layer** (Modern/Backend)
   - Located in: `app/`, `config/`, `database/`
   - Modern admin interface using Filament v4
   - Type-safe settings management using Spatie Laravel Settings
   - Service-oriented architecture

### Standalone Package Structure

For pure Laravel applications without WooCommerce:
- Package namespace: `NmDigitalHub\SumitPayment`
- Located in: `src/` directory
- All business logic available as Laravel services
- Fully documented API

## Features

### Payment Processing
- **Complete SUMIT API Integration**: Payment processing, refunds, authorization-only flows
- **Token Management**: PCI-compliant secure storage of payment tokens
- **Recurring Billing**: Support for subscription and recurring payment models  
- **Installment Payments**: Multiple payment installments support
- **Stock Synchronization**: Inventory management and stock sync
- **Donation Support**: Specialized handling for donation receipts
- **Marketplace Integration**: Multi-vendor support (Dokan, WCFM, WC Vendors)

### Spatie Laravel Settings Integration

Centralized, type-safe configuration management:

- **PaymentSettings Class** (`app/Settings/PaymentSettings.php`)
  - API credentials (api_key, secret_key, private_key, public_key)
  - Environment settings (sandbox_mode, environment)
  - Token configuration (support_tokens, token_param)
  - Merchant details (merchant_id, company_id)
  - Webhook URL

### Filament v4 Admin Panel

Modern admin interface at `/admin/payment`:

- **Transaction Management** (`app/Filament/Resources/TransactionResource.php`)
  - View and manage all payment transactions
  - Filter by status (pending, completed, failed, refunded)
  - Search and sort capabilities
  - Export functionality

- **Payment Token Management** (`app/Filament/Resources/PaymentTokenResource.php`)
  - Manage stored payment methods
  - View card details (last 4 digits, expiry date)
  - Set default payment methods
  - Secure token operations

- **Settings Management** (`app/Filament/Pages/ManagePaymentSettings.php`)
  - Configure API credentials
  - Toggle sandbox mode
  - Manage token settings
  - Set webhook URLs
  - Live validation of settings

### Service Layer

Type-safe service classes available in both modes:

- **PaymentService** - Process charges, check sandbox mode, retrieve webhook URLs
- **TokenService** - Store and retrieve payment tokens, manage token parameters
- **RefundService** - Process refunds and check refund status
- **ApiService** - HTTP client with logging and credential validation
- **RecurringBillingService** - Subscription lifecycle management
- **StockService** - Inventory synchronization
- **DonationService** - Donation receipt handling
- **MarketplaceService** - Multi-vendor marketplace support

## Installation

### Prerequisites

- PHP ^8.1
- Composer
- MySQL or compatible database
- (Optional) WordPress with WooCommerce for hybrid mode

### Hybrid Mode Setup

1. Install dependencies:
```bash
composer install
```

2. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

3. Configure database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

4. Run migrations:
```bash
php artisan migrate
php artisan settings:discover
```

5. Access the admin panel:
```
http://your-site.com/admin/payment
```

### Standalone Package Installation

```bash
composer require nm-digitalhub/woo-payment-gateway-officeguy
```

Publish configuration:
```bash
php artisan vendor:publish --tag=sumit-payment-config
```

Run migrations:
```bash
php artisan migrate
```

## Configuration

### Environment Variables

Add to your `.env` file:

```env
APP_NAME="Payment Gateway Admin"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Payment Gateway (Optional - can be set via admin UI)
SUMIT_COMPANY_ID=your_company_id
SUMIT_API_KEY=your_api_key
SUMIT_API_PUBLIC_KEY=your_public_key
SUMIT_ENVIRONMENT=www
SUMIT_TESTING_MODE=false
SUMIT_MERCHANT_NUMBER=your_merchant_number
```

### Managing Settings

Settings can be managed via:
1. Filament admin panel at `/admin/payment/settings`
2. Direct database updates
3. Laravel tinker for programmatic access

Settings are automatically cached for performance.

## Usage

### In Laravel Applications

```php
use App\Settings\PaymentSettings;
use App\Services\PaymentService;

class CheckoutController
{
    public function __construct(
        protected PaymentService $paymentService,
    ) {}

    public function charge()
    {
        $result = $this->paymentService->charge([
            'amount' => 100.00,
            'currency' => 'ILS',
            'order_id' => '12345',
        ]);
        
        if ($result['success']) {
            // Payment successful
        }
    }
}
```

### As a Package

```php
use NmDigitalHub\SumitPayment\Services\PaymentService;

$paymentService = app(PaymentService::class);

$result = $paymentService->processPayment([
    'order_id' => '12345',
    'total' => 100.00,
    'currency' => 'ILS',
    'items' => [
        ['name' => 'Product 1', 'price' => 100.00, 'quantity' => 1]
    ],
    'customer' => [
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]
], $paymentMethod, $paymentsCount);
```

### Managing Payment Tokens

```php
use App\Services\TokenService;

$tokenService = app(TokenService::class);

// Create token
$result = $tokenService->createToken([
    'card_number' => '4580123456789012',
    'cvv' => '123',
    'exp_month' => 12,
    'exp_year' => 2025
], $userId);

// Get user tokens
$tokens = $tokenService->getUserTokens($userId);
```

### Event System

Laravel events replace WooCommerce hooks:

```php
use NmDigitalHub\SumitPayment\Events\PaymentProcessed;
use NmDigitalHub\SumitPayment\Events\PaymentFailed;

// Listen to payment events
Event::listen(PaymentProcessed::class, function ($event) {
    // Handle payment success
    $transaction = $event->transaction;
});

Event::listen(PaymentFailed::class, function ($event) {
    // Handle payment failure
    $error = $event->error;
});
```

## Testing

Run the test suite:

```bash
composer test
# or
./vendor/bin/phpunit
```

Tests cover:
- Payment service functionality
- Token service operations
- Refund service processing
- Settings integration
- API service communication
- Event dispatching

## API Routes

The package provides these routes (prefix: `/sumit-payment`):

- `POST /process` - Process payment
- `GET /redirect` - Handle redirect callback
- `POST /refund` - Process refund
- `GET /tokens` - List user tokens (authenticated)
- `POST /tokens` - Create token (authenticated)
- `DELETE /tokens/{id}` - Delete token (authenticated)

## Migration from WooCommerce Plugin

This package replaces the legacy WooCommerce plugin with the following mappings:

| Legacy Component | New Component |
|-----------------|---------------|
| `OfficeGuyAPI.php` | `Services/ApiService.php` |
| `OfficeGuyPayment.php` | `Services/PaymentService.php` |
| `OfficeGuyTokens.php` | `Services/TokenService.php` + `Models/PaymentToken.php` |
| `OfficeGuySubscriptions.php` | `Services/RecurringBillingService.php` |
| `OfficeGuyStock.php` | `Services/StockService.php` |
| `OfficeGuyDonation.php` | `Services/DonationService.php` |
| Marketplace files | `Services/MarketplaceService.php` |
| WooCommerce hooks | Laravel Events & Listeners |

For detailed migration instructions, see [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md).

## Architecture Decisions

### Why Hybrid Architecture?

- **Backward Compatibility**: Existing WooCommerce sites continue to work
- **Modern Admin**: New Filament v4 admin interface for better UX
- **Gradual Migration**: Allows incremental migration from WooCommerce to pure Laravel
- **Flexibility**: Use as plugin, application, or package

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

## Documentation

- [README.md](README.md) - This file
- [CHANGELOG.md](CHANGELOG.md) - Version history and breaking changes
- [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md) - Migration from WooCommerce and Filament v3
- [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) - Technical implementation details
- [API.md](API.md) - Complete API reference (package mode)
- [MIGRATION.md](MIGRATION.md) - WooCommerce to Laravel migration guide (package mode)

## Support

For support and bug reports:
- Email: support@sumit.co.il
- GitHub Issues: [Report an issue](https://github.com/nm-digitalhub/woo-payment-gateway-officeguy/issues)
- Documentation: See all documentation files in this repository

## License

MIT License - See [LICENSE](LICENSE) file for details

## Credits

Developed by NM Digital Hub for SUMIT Payment Gateway integration.
