# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2024-01-01

### Added
- Initial Laravel package release
- Complete migration from WooCommerce plugin to Laravel
- `ApiService` for SUMIT API integration with Guzzle HTTP client
- `PaymentService` for payment processing and validation
- `TokenService` for secure payment token management
- `RecurringBillingService` for subscription and recurring payment management
- `StockService` for inventory synchronization
- `DonationService` for donation receipt handling
- `MarketplaceService` for multi-vendor marketplace support (Dokan, WCFM, WC Vendors)
- `PaymentTransaction` model for transaction records
- `PaymentToken` model for secure token storage
- `RecurringBilling` model for subscription management
- `PaymentProcessed` event for successful payment notifications
- `PaymentFailed` event for payment failure notifications
- `LogPaymentSuccess` listener for logging successful payments
- `LogPaymentFailure` listener for logging payment failures
- `PaymentController` for payment processing endpoints
- `TokenController` for token management endpoints
- `ValidatePaymentRequest` middleware for request validation
- Database migrations for transactions, tokens, and recurring billings
- Filament V4 admin resources:
  - `PaymentTransactionResource` for transaction management
  - `PaymentTokenResource` for token management
  - `SumitPaymentSettings` page for configuration
- Web routes for payment and token operations
- Comprehensive configuration file with environment-based settings
- Unit tests for `ApiService` and `PaymentService`
- Feature tests for `PaymentController`
- PHPUnit configuration
- Comprehensive README documentation
- API documentation (API.md)
- Migration guide (MIGRATION.md)
- Package auto-discovery support
- Service provider with automatic service registration
- Event listener registration replacing WooCommerce hooks

### Changed
- Replaced WordPress `wp_remote_post()` with Guzzle HTTP client
- Replaced WooCommerce actions/filters with Laravel Events/Listeners
- Replaced WordPress admin pages with Filament V4 admin panels
- Migrated from procedural PHP to object-oriented Laravel architecture
- Changed from WordPress options to Laravel configuration system
- Migrated from WooCommerce order system to generic order data structure

### Removed
- All WooCommerce dependencies
- WordPress-specific functions and hooks
- Legacy procedural code structure
- WordPress admin UI components

### Security
- Implemented Laravel's built-in security features
- Added request validation middleware
- Encrypted sensitive data in database (tokens, citizen IDs)
- Implemented proper authentication for token management endpoints
- Added CSRF protection for all POST routes
- Sanitized API request logging to prevent sensitive data exposure

### Performance
- Improved API request handling with Guzzle
- Optimized database queries with Eloquent ORM
- Implemented service singletons for better performance
- Added database indexes for frequently queried fields

## Migration Notes

### Breaking Changes from WooCommerce Plugin

1. **Database Structure**: New database tables with different schema
   - Migrate existing transaction data manually if needed
   - Token data must be regenerated (cannot be migrated due to encryption)

2. **API**: RESTful endpoints instead of WordPress AJAX
   - Update any custom integrations to use new endpoints
   - Authentication required for token management

3. **Events**: Laravel events instead of WooCommerce hooks
   - Update custom hook implementations to event listeners
   - See MIGRATION.md for specific hook mappings

4. **Admin UI**: Filament panels instead of WordPress admin
   - Access via Filament admin panel
   - Different URL structure and navigation

### Upgrading from WooCommerce Plugin

1. Install the Laravel package
2. Run migrations to create new tables
3. Configure environment variables
4. Update custom code to use new events
5. Test payment processing thoroughly
6. Deactivate WooCommerce plugin after verification

For detailed migration instructions, see MIGRATION.md

## Compatibility

- PHP: ^8.1|^8.2|^8.3
- Laravel: ^10.0|^11.0
- Filament: ^3.0

## Support

For support and bug reports:
- Email: support@sumit.co.il
- Documentation: See README.md, API.md, and MIGRATION.md
