# Laravel SUMIT Payment Gateway Package

A comprehensive Laravel package for SUMIT payment gateway integration with Filament V4 admin panels, migrated from the legacy WooCommerce plugin.

## Features

- **Payment Processing**: Complete payment gateway integration with SUMIT API
- **Token Management**: Secure storage and management of payment tokens (PCI compliant)
- **Recurring Billing**: Support for subscription and recurring payment models
- **Stock Synchronization**: Inventory management and stock sync capabilities
- **Donation Support**: Specialized handling for donation receipts
- **Marketplace Integration**: Multi-vendor support (Dokan, WCFM, WC Vendors)
- **Filament Admin Panels**: Modern admin UI for transaction and token management
- **Event-Driven Architecture**: Laravel events replace legacy WooCommerce hooks

## Installation

```bash
composer require nm-digitalhub/laravel-sumit-paymentfi
```

### Publish Configuration

```bash
php artisan vendor:publish --tag=sumit-payment-config
```

### Run Migrations

```bash
php artisan migrate
```

## Configuration

Add the following environment variables to your `.env` file:

```env
SUMIT_COMPANY_ID=your_company_id
SUMIT_API_KEY=your_api_key
SUMIT_API_PUBLIC_KEY=your_public_key
SUMIT_ENVIRONMENT=www
SUMIT_TESTING_MODE=false
SUMIT_MERCHANT_NUMBER=your_merchant_number
```

## Usage

### Processing Payments

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
use NmDigitalHub\SumitPayment\Services\TokenService;

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

### Recurring Billing

```php
use NmDigitalHub\SumitPayment\Services\RecurringBillingService;

$recurringService = app(RecurringBillingService::class);

$subscription = $recurringService->createSubscription([
    'user_id' => 1,
    'amount' => 99.99,
    'frequency' => 'monthly',
    'description' => 'Monthly subscription'
]);
```

## Events

The package dispatches the following events:

- `PaymentProcessed`: Fired when a payment is successfully processed
- `PaymentFailed`: Fired when a payment fails

Listen to these events in your application:

```php
use NmDigitalHub\SumitPayment\Events\PaymentProcessed;

Event::listen(PaymentProcessed::class, function ($event) {
    // Handle payment success
    $transaction = $event->transaction;
});
```

## API Routes

The package provides the following routes:

- `POST /sumit-payment/process` - Process payment
- `GET /sumit-payment/redirect` - Handle redirect callback
- `POST /sumit-payment/refund` - Process refund
- `GET /sumit-payment/tokens` - List user tokens
- `POST /sumit-payment/tokens` - Create token
- `DELETE /sumit-payment/tokens/{id}` - Delete token

## Testing

```bash
vendor/bin/phpunit
```

## Migration from WooCommerce

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

## License

MIT License

## Support

For support, contact support@sumit.co.il
