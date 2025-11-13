# Migration Guide: Filament v3 to v4 & Spatie Settings Integration

## Overview

This document provides a comprehensive guide for the migration to Filament v4 and integration of Spatie Laravel Settings in the WooCommerce Payment Gateway admin layer.

## What Changed

### 1. Settings Management

#### Before (WooCommerce Plugin)
```php
// Direct config/env access
$apiKey = config('payment.api_key');
$secretKey = env('PAYMENT_SECRET_KEY');
```

#### After (Spatie Laravel Settings)
```php
// Type-safe settings class via dependency injection
use App\Settings\PaymentSettings;

class PaymentService {
    public function __construct(
        protected PaymentSettings $settings
    ) {}
    
    public function charge() {
        $apiKey = $this->settings->api_key;
        $secretKey = $this->settings->secret_key;
    }
}
```

**Benefits:**
- Type safety: IDE autocomplete and type checking
- Centralized: Single source of truth
- Cacheable: Automatic caching support
- Database-backed: Settings persist in database
- Versioned: Migration-based approach

### 2. Admin Interface

#### Before (None)
No Laravel-based admin interface existed.

#### After (Filament v4)
Complete admin panel with:
- Transaction management
- Token management
- Settings configuration
- Modern UI with search, filters, and actions

### 3. Service Architecture

#### Before (Procedural)
```php
// Direct function calls
function processPayment($data) {
    $apiKey = config('payment.api_key');
    // ...
}
```

#### After (Service Layer)
```php
// Service classes with dependency injection
class PaymentService {
    public function __construct(
        protected PaymentSettings $settings
    ) {}
    
    public function charge(array $data): array {
        // Uses $this->settings
    }
}
```

## Filament v4 Migration Details

### Panel Configuration

**New in v4:**
```php
// app/Providers/PaymentPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('payment')
        ->path('admin/payment')
        ->discoverResources(...)
        ->discoverPages(...)
        ->middleware([...]);
}
```

### Resource Structure

**Filament v4 Resource:**
```php
use Filament\Forms\Form;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('transaction_id'),
            // ...
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([...])
            ->filters([...])
            ->actions([...]);
    }
}
```

**Key Changes from v3:**
- `form()` now returns `Form` object
- `table()` now returns `Table` object
- Actions are defined using `Tables\Actions\` namespace
- Filters use `Tables\Filters\` namespace

### Page Structure

**Filament v4 Pages:**
```php
// List page
class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            // Actions
        ];
    }
}

// Edit page
class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
```

### Settings Page

**New SettingsPage class:**
```php
use Filament\Pages\SettingsPage;

class ManagePaymentSettings extends SettingsPage
{
    protected static string $settings = PaymentSettings::class;
    
    public function form(Form $form): Form
    {
        return $form->schema([
            // Form components bound to settings properties
        ]);
    }
}
```

**How it works:**
- Automatically loads settings from database
- Saves changes back to settings repository
- Validates input based on form rules
- Caches settings if caching is enabled

## Spatie Settings Integration

### Settings Class Definition

```php
namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaymentSettings extends Settings
{
    public string $api_key;
    public string $secret_key;
    public bool $sandbox_mode;
    // ... more properties
    
    public static function group(): string
    {
        return 'payment';
    }
}
```

### Settings Migration

```php
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.api_key', '');
        $this->migrator->add('payment.secret_key', '');
        $this->migrator->add('payment.sandbox_mode', false);
        // ...
    }
};
```

### Accessing Settings

**Via Dependency Injection (Recommended):**
```php
class PaymentService
{
    public function __construct(
        protected PaymentSettings $settings
    ) {}
}
```

**Via Facade/Helper:**
```php
$settings = app(PaymentSettings::class);
$apiKey = $settings->api_key;
```

**In Filament Forms:**
```php
Forms\Components\TextInput::make('api_key')
    ->label('API Key')
    ->required()
```

The form automatically binds to `PaymentSettings::$api_key`.

## Service Layer Pattern

### PaymentService

**Purpose:** Process payment charges

**Usage:**
```php
use App\Services\PaymentService;

class PaymentController
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}
    
    public function charge(Request $request)
    {
        $result = $this->paymentService->charge([
            'amount' => $request->amount,
            'currency' => 'USD',
        ]);
        
        return response()->json($result);
    }
}
```

### TokenService

**Purpose:** Manage payment tokens

**Usage:**
```php
use App\Services\TokenService;

class TokenController
{
    public function __construct(
        protected TokenService $tokenService
    ) {}
    
    public function store(Request $request)
    {
        $result = $this->tokenService->storeToken(
            $request->user()->id,
            $request->tokenData
        );
        
        return response()->json($result);
    }
}
```

### RefundService

**Purpose:** Process refunds

**Usage:**
```php
use App\Services\RefundService;

class RefundController
{
    public function __construct(
        protected RefundService $refundService
    ) {}
    
    public function refund(Request $request)
    {
        $result = $this->refundService->processRefund(
            $request->transaction_id,
            $request->amount
        );
        
        return response()->json($result);
    }
}
```

## Testing

### Unit Tests

**Example test:**
```php
namespace Tests\Unit;

use App\Settings\PaymentSettings;
use App\Services\PaymentService;
use PHPUnit\Framework\TestCase;

class PaymentServiceTest extends TestCase
{
    public function test_payment_service_uses_settings()
    {
        $settings = $this->createMock(PaymentSettings::class);
        $settings->api_key = 'test_api_key';
        
        $service = new PaymentService($settings);
        
        // Test service behavior
    }
}
```

**Running tests:**
```bash
./vendor/bin/phpunit
```

## Configuration

### config/settings.php

```php
return [
    'settings' => [
        \App\Settings\PaymentSettings::class,
    ],
    
    'default_repository' => 'database',
    
    'repositories' => [
        'database' => [
            'type' => DatabaseSettingsRepository::class,
        ],
    ],
    
    'cache' => [
        'enabled' => env('SETTINGS_CACHE_ENABLED', false),
    ],
];
```

### config/app.php

```php
return [
    'providers' => [
        App\Providers\PaymentPanelProvider::class,
    ],
];
```

## Database Schema

### Settings Table

Spatie Laravel Settings creates a `settings` table:

```sql
CREATE TABLE `settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `payload` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_group_name_unique` (`group`,`name`)
);
```

**Example row:**
```json
{
  "group": "payment",
  "name": "api_key",
  "payload": "\"your_api_key_here\"",
  "locked": false
}
```

## Breaking Changes

### 1. No More Filament v3

If you were using Filament v3, you must update:
- Resource `form()` and `table()` methods
- Action definitions
- Filter definitions
- Page structures

### 2. Settings Access Pattern

Old:
```php
config('payment.api_key')
env('PAYMENT_SECRET')
```

New:
```php
app(PaymentSettings::class)->api_key
// or via DI
$this->settings->api_key
```

### 3. Service Instantiation

Old:
```php
$service = new PaymentService();
```

New:
```php
// Via container (automatically injects settings)
$service = app(PaymentService::class);

// Via dependency injection
public function __construct(PaymentService $service) {}
```

## Rollback Plan

If you need to rollback:

1. **Database:** Keep a backup before running migrations
2. **Settings:** Export current settings:
   ```php
   $settings = app(PaymentSettings::class);
   $backup = json_encode($settings->toArray());
   ```
3. **Code:** Use git to revert commits
4. **Dependencies:** Run `composer install` with old composer.lock

## Troubleshooting

### Settings Not Loading

**Problem:** Settings return default/null values

**Solution:**
1. Run migrations: `php artisan migrate`
2. Discover settings: `php artisan settings:discover`
3. Clear cache: `php artisan cache:clear`

### Filament Panel Not Accessible

**Problem:** 404 on `/admin/payment`

**Solution:**
1. Clear route cache: `php artisan route:clear`
2. Check panel is registered in service provider
3. Verify middleware configuration

### Service Not Receiving Settings

**Problem:** Settings are null in service constructor

**Solution:**
1. Ensure PaymentSettings is registered in `config/settings.php`
2. Run discovery: `php artisan settings:discover`
3. Check service is being resolved via Laravel container

## Best Practices

### 1. Always Use Dependency Injection

✅ Good:
```php
public function __construct(PaymentSettings $settings) {}
```

❌ Bad:
```php
$settings = config('payment');
```

### 2. Type Hint Everything

✅ Good:
```php
public function charge(array $data): array
```

❌ Bad:
```php
public function charge($data)
```

### 3. Use Filament Actions

✅ Good:
```php
Tables\Actions\EditAction::make()
```

❌ Bad:
```php
<a href="/edit">Edit</a>
```

### 4. Test With Mocks

✅ Good:
```php
$settings = $this->createMock(PaymentSettings::class);
```

❌ Bad:
```php
$settings = app(PaymentSettings::class); // Uses real DB
```

## Additional Resources

- [Filament v4 Documentation](https://filamentphp.com/docs)
- [Spatie Laravel Settings](https://github.com/spatie/laravel-settings)
- [Laravel Service Container](https://laravel.com/docs/container)
- [PHPUnit Testing](https://phpunit.de/documentation.html)

## Support

For issues or questions:
1. Check this migration guide
2. Review the README.md
3. Check Filament/Spatie documentation
4. Open an issue on GitHub
