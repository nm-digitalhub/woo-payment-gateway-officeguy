# Changelog

All notable changes to this project will be documented in this file.

## [4.1.0] - 2024-01-15

### Changed - Plugin Architecture Refactoring

This release refactors the admin layer to follow Filament v4 plugin best practices.

#### Filament Plugin Architecture
- **NEW: PaymentPlugin** (`src/PaymentPlugin.php`)
  - Implements `Filament\Contracts\Plugin` interface
  - Provides modular, reusable plugin structure
  - Registers resources, pages, and widgets
  - Follows official Filament v4 plugin documentation
  - Can be registered in any Filament panel

- **NEW: AdminPanelProvider** (`src/Providers/AdminPanelProvider.php`)
  - Replaces PaymentPanelProvider with plugin-based approach
  - Lightweight panel configuration
  - Registers PaymentPlugin via `->plugin()` method
  - Maintains same URL path (`/admin/payment`)

- **NEW: PaymentPluginServiceProvider** (`src/Providers/PaymentPluginServiceProvider.php`)
  - Extends Spatie's PackageServiceProvider
  - Handles asset registration in `packageBooted()` method
  - Follows Laravel package development best practices

#### Testing
- **NEW: PaymentPluginTest** (`tests/Unit/PaymentPluginTest.php`)
  - 6 new test methods for plugin structure
  - Tests plugin ID, instantiation, and interface compliance
  - Validates register() and boot() methods
  - All 12 tests passing (6 plugin + 6 service tests)

#### Documentation Updates
- Updated README.md with Plugin Architecture section
- Updated MIGRATION_GUIDE.md with plugin migration instructions
- Added deprecation notice to PaymentPanelProvider
- Updated installation instructions to reference AdminPanelProvider

### Deprecated
- **PaymentPanelProvider** - Deprecated in favor of AdminPanelProvider + PaymentPlugin
  - File kept for backward compatibility
  - Should not be used in new implementations

### Migration Path
For existing installations, update `bootstrap/app.php` and `composer.json` to reference:
```php
NmDigitalhub\WooPaymentGatewayAdmin\Providers\AdminPanelProvider::class
```

See MIGRATION_GUIDE.md for detailed migration instructions.

## [4.0.0] - 2024-01-01

### Added - Laravel Admin Layer

This release introduces a complete Laravel-based admin layer for the WooCommerce payment gateway.

#### Spatie Laravel Settings Integration
- **PaymentSettings class** (`app/Settings/PaymentSettings.php`)
  - Type-safe configuration management
  - API credentials (api_key, secret_key, private_key, public_key)
  - Environment settings (sandbox_mode, environment, webhook_url)
  - Token configuration (support_tokens, authorize_only, token_param)
  - Merchant details (merchant_id, company_id)

- **Settings Migration** (`database/migrations/2024_01_01_000001_create_payment_settings.php`)
  - Database-backed settings storage
  - Default values for all settings
  - Versioned configuration changes

- **Configuration Files**
  - `config/settings.php` - Spatie Settings configuration
  - `config/app.php` - Laravel application configuration
  - `config/database.php` - Database connections

#### Filament v4 Admin Panel
- **Panel Provider** (`app/Providers/PaymentPanelProvider.php`)
  - Payment admin panel accessible at `/admin/payment`
  - Resource discovery and registration
  - Middleware configuration
  - Color scheme and branding

- **Transaction Resource** (`app/Filament/Resources/TransactionResource.php`)
  - List, view, and edit transactions
  - Status filtering (pending, completed, failed, refunded)
  - Search and sort capabilities
  - Bulk actions support
  - Badge-based status display

- **Payment Token Resource** (`app/Filament/Resources/PaymentTokenResource.php`)
  - Manage stored payment methods
  - Card type filtering
  - Default payment method toggling
  - User-based token listing
  - Last 4 digits and expiry date display

- **Settings Management Page** (`app/Filament/Pages/ManagePaymentSettings.php`)
  - API credentials configuration
  - Environment settings (sandbox mode, environment)
  - Token settings (support, authorization, method)
  - Webhook URL configuration
  - Auto-save to PaymentSettings

#### Service Layer
- **PaymentService** (`app/Services/PaymentService.php`)
  - Process payment charges
  - Check sandbox mode status
  - Retrieve webhook URLs
  - Uses PaymentSettings via dependency injection

- **TokenService** (`app/Services/TokenService.php`)
  - Store payment tokens
  - Retrieve tokens for users
  - Delete tokens
  - Check token support status
  - Get token parameter (J2/J5)

- **RefundService** (`app/Services/RefundService.php`)
  - Process refunds
  - Check refund status
  - Uses PaymentSettings for API credentials

#### Testing
- **PaymentServiceTest** (`tests/Unit/PaymentServiceTest.php`)
  - Test settings integration
  - Test payment processing
  - Mock-based testing

- **TokenServiceTest** (`tests/Unit/TokenServiceTest.php`)
  - Test token support checking
  - Test token storage
  - Mock PaymentSettings

- **RefundServiceTest** (`tests/Unit/RefundServiceTest.php`)
  - Test refund processing
  - Test status checking
  - Verify amount handling

- **PHPUnit Configuration** (`phpunit.xml`)
  - Test suite organization
  - Coverage configuration
  - Environment settings

#### Documentation
- **README.md** - Comprehensive project documentation
  - Architecture overview
  - Installation instructions
  - Usage examples
  - Configuration guide
  - Testing guide

- **MIGRATION_GUIDE.md** - Detailed migration guide
  - Filament v3 to v4 migration
  - Spatie Settings integration
  - Code examples and comparisons
  - Breaking changes
  - Troubleshooting

- **.env.example** - Environment configuration template
  - Laravel settings
  - Database configuration
  - Filament settings
  - Spatie Settings options

#### Infrastructure
- **composer.json** - Dependency management
  - Laravel Framework ^10.0
  - Filament ^4.1
  - Spatie Laravel Settings ^3.0
  - PHPUnit ^10.0

- **.gitignore** - Version control exclusions
  - Vendor directory
  - Environment files
  - Cache directories
  - Build artifacts

- **Directory Structure**
  - `app/` - Application code
  - `config/` - Configuration files
  - `database/` - Migrations
  - `tests/` - Test suites
  - `storage/` - Runtime files
  - `bootstrap/cache/` - Application cache

### Changed
- Enhanced project structure to support Laravel alongside WooCommerce plugin
- Modernized configuration management with type-safe settings

### Migration Notes

This is a major version release that adds Laravel functionality while maintaining backward compatibility with the existing WooCommerce plugin.

#### For Developers
- Settings are now managed via `PaymentSettings` class instead of direct config access
- Services should receive `PaymentSettings` via dependency injection
- Filament v4 APIs are used for all admin interface components

#### For Users
- No changes to the existing WooCommerce plugin functionality
- New admin interface available at `/admin/payment` after Laravel setup
- Settings can be managed via the Filament admin panel

#### Installation Steps
1. Run `composer install` to install Laravel dependencies
2. Configure `.env` file with database credentials
3. Run `php artisan migrate` to create settings tables
4. Access admin panel at `/admin/payment`

### Security
- All API credentials are stored securely in the database
- Password fields use proper encryption in forms
- CSRF protection enabled for all forms
- Authentication required for admin panel access

### Dependencies
- PHP ^8.1
- Laravel Framework ^10.0
- Filament ^4.1
- Spatie Laravel Settings ^3.0
- PHPUnit ^10.0

## [3.3.1] - Previous Version

See readme.txt for previous version history.
