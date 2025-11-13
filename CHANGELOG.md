# Changelog

All notable changes to this project will be documented in this file.

## [4.0.0] - 2024-01-01

### Added - Hybrid Architecture with Laravel Admin Layer

This release combines the complete Laravel package migration with the Laravel admin layer, supporting both standalone package usage and hybrid WooCommerce integration.

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

#### Package Service Layer (src/)
- **ApiService** (`src/Services/ApiService.php`)
  - Guzzle HTTP client for SUMIT API integration
  - Request/response logging with sensitive data sanitization
  - Credential validation and error handling
  
- **PaymentService** (`src/Services/PaymentService.php`)
  - Payment processing and validation
  - Installment support and authorization-only flows
  - Refund handling with proper status codes
  - Sandbox mode detection
  
- **TokenService** (`src/Services/TokenService.php`)
  - Secure payment token management
  - PCI-compliant encrypted token storage
  - Token CRUD operations with expiration validation
  
- **RecurringBillingService** (`src/Services/RecurringBillingService.php`)
  - Subscription lifecycle management
  - Frequency-based billing (daily/weekly/monthly/yearly)
  - Exception handling for invalid billing frequencies
  
- **StockService** (`src/Services/StockService.php`)
  - SUMIT inventory synchronization
  
- **DonationService** (`src/Services/DonationService.php`)
  - Donation receipt handling
  
- **MarketplaceService** (`src/Services/MarketplaceService.php`)
  - Multi-vendor marketplace support (Dokan, WCFM, WC Vendors)

#### Application Service Layer (app/)
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

#### Models & Migrations
- **PaymentTransaction** - Transaction history with status tracking
- **PaymentToken** - Encrypted token storage with expiration validation
- **RecurringBilling** - Subscription state management
- Database migrations for all models

#### Event System
- **PaymentProcessed** / **PaymentFailed** events replace WooCommerce action hooks
- **LogPaymentSuccess** / **LogPaymentFailure** listeners
- Event listener registration replacing WooCommerce hooks

#### Testing
- **PaymentServiceTest** (`tests/Unit/PaymentServiceTest.php`)
  - Test settings integration
  - Test payment processing
  - Mock-based testing for both app/ and src/ implementations

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
  - Coverage configuration for both app/ and src/
  - Environment settings

#### Documentation
- **README.md** - Comprehensive hybrid architecture documentation
  - Installation instructions for both modes
  - Usage examples for package and application
  - Configuration guide
  - Testing guide

- **MIGRATION_GUIDE.md** - Detailed migration guide
  - Filament v3 to v4 migration
  - Spatie Settings integration
  - Code examples and comparisons
  - Breaking changes
  - Troubleshooting

- **IMPLEMENTATION_SUMMARY.md** - Technical implementation details
- **API.md** - Complete endpoint and service reference
- **MIGRATION.md** - WooCommerce to Laravel migration guide
- **FILAMENT_V4_MIGRATION.md** - Filament upgrade documentation

- **.env.example** - Environment configuration template
  - Laravel settings
  - Database configuration
  - Filament settings
  - Spatie Settings options

#### Infrastructure
- **composer.json** - Hybrid dependency management
  - Laravel Framework ^10.0|^11.0
  - Filament ^4.0
  - Spatie Laravel Settings ^3.0
  - Guzzle ^7.0
  - PHPUnit ^10.0
  - Supports both package (`src/`) and application (`app/`) namespaces

- **.gitignore** - Comprehensive version control exclusions
  - Vendor directory
  - Environment files
  - Cache directories
  - Build artifacts
  - Storage directories

- **Directory Structure**
  - `app/` - Laravel application code (Filament admin)
  - `src/` - Package code (reusable services)
  - `config/` - Configuration files
  - `database/` - Migrations
  - `tests/` - Test suites
  - `storage/` - Runtime files
  - `includes/` - Legacy WooCommerce plugin files
  - `templates/` - WooCommerce templates

### Changed
- Enhanced project structure to support both package and application modes
- Modernized configuration management with type-safe settings
- Replaced WordPress `wp_remote_post()` with Guzzle HTTP client
- Replaced WooCommerce actions/filters with Laravel Events/Listeners
- Replaced WordPress admin pages with Filament V4 admin panels
- Migrated from procedural PHP to object-oriented Laravel architecture
- Changed from WordPress options to Laravel configuration system
- Migrated from WooCommerce order system to generic order data structure

### Removed
- (In package mode) All WooCommerce dependencies
- (In package mode) WordPress-specific functions and hooks
- (In package mode) Legacy procedural code structure
- (In package mode) WordPress admin UI components

### Security
- Implemented Laravel's built-in security features
- Added request validation middleware (returns 503 for misconfiguration)
- Encrypted sensitive data in database (tokens, citizen IDs)
- Implemented proper authentication for token management endpoints
- Added CSRF protection for all POST routes
- Sanitized API request logging to prevent sensitive data exposure (only masks existing fields)
- Exception handling for invalid billing frequencies
- Encrypted casting for sensitive model fields

### Performance
- Improved API request handling with Guzzle
- Optimized database queries with Eloquent ORM
- Implemented service singletons for better performance
- Added database indexes for frequently queried fields

## Migration Notes

### Breaking Changes

1. **Dual Architecture Support**
   - Project now supports both package mode (`src/`) and application mode (`app/`)
   - Choose deployment mode based on your needs
   - Both modes can coexist for gradual migration

2. **Database Structure**: New database tables with different schema
   - Migrate existing transaction data manually if needed
   - Token data must be regenerated (cannot be migrated due to encryption)

3. **API**: RESTful endpoints instead of WordPress AJAX
   - Update any custom integrations to use new endpoints
   - Authentication required for token management

4. **Events**: Laravel events instead of WooCommerce hooks
   - Update custom hook implementations to event listeners
   - See MIGRATION.md for specific hook mappings

5. **Admin UI**: Filament panels instead of WordPress admin
   - Access via Filament admin panel at `/admin/payment`
   - Different URL structure and navigation

### Upgrading Paths

#### From WooCommerce Plugin to Hybrid Mode
1. Keep existing WooCommerce plugin active
2. Install Laravel dependencies with `composer install`
3. Configure `.env` file with database credentials
4. Run `php artisan migrate` to create settings tables
5. Access new admin panel at `/admin/payment`
6. Configure settings via Filament admin UI
7. Test payment processing thoroughly
8. Gradually migrate custom code to Laravel services

#### From WooCommerce Plugin to Pure Laravel Package
1. Install the Laravel package
2. Run migrations to create new tables
3. Configure environment variables or use PaymentSettings
4. Update custom code to use new events
5. Test payment processing thoroughly
6. Deactivate WooCommerce plugin after verification

For detailed migration instructions, see MIGRATION_GUIDE.md and MIGRATION.md

## Compatibility

- PHP: ^8.1|^8.2|^8.3
- Laravel: ^10.0|^11.0
- Filament: ^4.0
- Spatie Laravel Settings: ^3.0

## Support

For support and bug reports:
- Email: support@sumit.co.il
- Documentation: See README.md, API.md, MIGRATION.md, and MIGRATION_GUIDE.md
- GitHub Issues: Report issues on the project repository

## [3.3.1] - Previous Version

See readme.txt for previous version history.
