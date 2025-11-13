# Migration Guide from WooCommerce to Laravel

This guide helps you migrate from the legacy WooCommerce SUMIT Payment Gateway plugin to the new Laravel package.

## Overview

The Laravel package replaces all WooCommerce-specific functionality with Laravel equivalents:

| Legacy Component | Laravel Component | Purpose |
|-----------------|-------------------|---------|
| `OfficeGuyAPI.php` | `Services/ApiService.php` | HTTP API communication |
| `OfficeGuyPayment.php` | `Services/PaymentService.php` | Payment processing |
| `OfficeGuyTokens.php` | `Services/TokenService.php` | Token management |
| `OfficeGuySubscriptions.php` | `Services/RecurringBillingService.php` | Recurring payments |
| `OfficeGuyStock.php` | `Services/StockService.php` | Inventory sync |
| `OfficeGuyDonation.php` | `Services/DonationService.php` | Donation handling |
| WooCommerce Actions/Filters | Laravel Events/Listeners | Event handling |
| WP Admin Pages | Filament Resources | Admin UI |

## Step-by-Step Migration

### 1. Install Laravel Package

```bash
composer require nm-digitalhub/laravel-sumit-paymentfi
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --tag=sumit-payment-config
```

### 3. Migrate Environment Variables

Copy your existing credentials from the WooCommerce settings to `.env`:

```env
SUMIT_COMPANY_ID=your_company_id
SUMIT_API_KEY=your_api_key
SUMIT_API_PUBLIC_KEY=your_public_key
SUMIT_ENVIRONMENT=www
SUMIT_MERCHANT_NUMBER=your_merchant_number
```

### 4. Run Migrations

```bash
php artisan migrate
```

This will create the necessary database tables:
- `sumit_payment_transactions`
- `sumit_payment_tokens`
- `sumit_recurring_billings`

### 5. Update Payment Processing Code

#### Before (WooCommerce):
```php
$gateway = GetOfficeGuyGateway();
$result = OfficeGuyPayment::ProcessOrder($gateway, $order, false);
```

#### After (Laravel):
```php
use NmDigitalHub\SumitPayment\Services\PaymentService;

$paymentService = app(PaymentService::class);
$result = $paymentService->processPayment($orderData, $paymentMethod, $paymentsCount);
```

### 6. Replace WooCommerce Hooks with Laravel Events

#### Before (WooCommerce):
```php
add_action('woocommerce_payment_complete', 'my_custom_function');

function my_custom_function($order_id) {
    // Custom logic
}
```

#### After (Laravel):
```php
use NmDigitalHub\SumitPayment\Events\PaymentProcessed;

Event::listen(PaymentProcessed::class, function ($event) {
    // Custom logic
    $transaction = $event->transaction;
});
```

### 7. Update Token Management

#### Before (WooCommerce):
```php
$token = WC_Payment_Tokens::get($token_id);
```

#### After (Laravel):
```php
use NmDigitalHub\SumitPayment\Services\TokenService;

$tokenService = app(TokenService::class);
$token = $tokenService->getToken($tokenId, $userId);
```

### 8. Migrate Subscriptions

#### Before (WooCommerce):
```php
$subscription = wcs_get_subscription($subscription_id);
```

#### After (Laravel):
```php
use NmDigitalHub\SumitPayment\Services\RecurringBillingService;

$recurringService = app(RecurringBillingService::class);
$billing = RecurringBilling::find($billingId);
```

## Custom Hooks Migration

### WooCommerce Custom Installments

#### Before:
```php
function CustomInstallmentsLogic($MaximumPayments, $OrderValue) {
    return 5;
}
add_filter('sumit_maximum_installments', 'CustomInstallmentsLogic');
```

#### After:
Override the `getMaximumPayments` method by extending `PaymentService`:

```php
namespace App\Services;

use NmDigitalHub\SumitPayment\Services\PaymentService as BasePaymentService;

class CustomPaymentService extends BasePaymentService
{
    protected function getMaximumPayments(float $amount): int
    {
        return 5; // Your custom logic
    }
}
```

Then bind your custom service in a service provider:

```php
$this->app->bind(PaymentService::class, CustomPaymentService::class);
```

### WooCommerce Custom Customer Fields

#### Before:
```php
function CustomCustomerFields($Customer, $Order) {
    $Customer['Billing last name'] = $Order->get_billing_last_name();
    return $Customer;
}
add_filter('sumit_customer_fields', 'CustomCustomerFields');
```

#### After:
Listen to the `PaymentProcessed` event:

```php
use NmDigitalHub\SumitPayment\Events\PaymentProcessed;

Event::listen(PaymentProcessed::class, function ($event) {
    // Add custom fields to the response
});
```

Or extend the service to add fields before processing:

```php
class CustomPaymentService extends PaymentService
{
    protected function prepareCustomer(array $customer): array
    {
        $customer = parent::prepareCustomer($customer);
        $customer['BillingLastName'] = $customer['billing_last_name'] ?? '';
        return $customer;
    }
}
```

## Admin UI Migration

The Laravel package uses Filament for admin UI instead of WordPress admin pages.

### Access Admin Resources

1. **Transactions**: Navigate to "SUMIT Payment > Transactions" in Filament
2. **Payment Tokens**: Navigate to "SUMIT Payment > Payment Tokens" in Filament
3. **Settings**: Navigate to "SUMIT Payment > SUMIT Settings" in Filament

### Custom Admin Pages

Create custom Filament pages instead of WordPress admin pages:

```php
namespace App\Filament\Pages;

use Filament\Pages\Page;

class CustomSumitPage extends Page
{
    protected static string $view = 'filament.pages.custom-sumit-page';
}
```

## Testing Migration

After migration, test the following:

1. **Payment Processing**
   - Test successful payments
   - Test failed payments
   - Test refunds

2. **Token Management**
   - Create new tokens
   - List user tokens
   - Delete tokens
   - Set default token

3. **Recurring Billing**
   - Create subscriptions
   - Process recurring payments
   - Cancel subscriptions

4. **Admin UI**
   - View transactions
   - Manage tokens
   - Configure settings

## Rollback Plan

If you need to rollback:

1. Keep the WooCommerce plugin active alongside the Laravel package
2. Use feature flags to gradually switch traffic
3. Maintain both databases in sync during transition period

## Support

For migration assistance, contact support@sumit.co.il
