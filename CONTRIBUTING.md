# Contributing to Laravel SUMIT Payment Package

Thank you for considering contributing to this package! This document provides guidelines for contributing.

## Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Focus on what is best for the community
- Show empathy towards other community members

## How to Contribute

### Reporting Bugs

Before creating a bug report:
1. Check if the bug has already been reported
2. Ensure you're using the latest version
3. Verify the bug is reproducible

When reporting a bug, include:
- A clear and descriptive title
- Steps to reproduce the issue
- Expected behavior
- Actual behavior
- Your environment (PHP version, Laravel version, etc.)
- Any error messages or logs

### Suggesting Enhancements

Enhancement suggestions are welcome! Please include:
- A clear and descriptive title
- Detailed description of the proposed feature
- Use cases and examples
- Why this enhancement would be useful

### Pull Requests

1. Fork the repository
2. Create a new branch from `main`
3. Make your changes
4. Write or update tests
5. Ensure all tests pass
6. Update documentation if needed
7. Submit a pull request

#### Pull Request Guidelines

- Follow PSR-12 coding standards
- Write clear commit messages
- Include tests for new features
- Update CHANGELOG.md
- Keep pull requests focused on a single concern

## Development Setup

### Prerequisites

- PHP 8.1 or higher
- Composer
- Laravel 10.x or 11.x

### Installation

```bash
# Clone your fork
git clone https://github.com/your-username/woo-payment-gateway-officeguy.git

# Install dependencies
composer install

# Run tests
vendor/bin/phpunit
```

### Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test file
vendor/bin/phpunit tests/Unit/ApiServiceTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html coverage
```

## Coding Standards

### PHP Code Style

Follow PSR-12 standards:

```php
<?php

namespace NmDigitalHub\SumitPayment\Services;

class ExampleService
{
    protected ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function doSomething(): array
    {
        // Implementation
        return [];
    }
}
```

### Documentation

- Add PHPDoc blocks for all classes and methods
- Include parameter types and return types
- Provide examples for complex functionality

```php
/**
 * Process a payment transaction
 *
 * @param array $orderData Order information including items and customer
 * @param array $paymentMethod Payment method details
 * @param int $paymentsCount Number of installments
 * @return array Result array with success status and transaction details
 */
public function processPayment(array $orderData, array $paymentMethod, int $paymentsCount = 1): array
{
    // Implementation
}
```

### Testing

- Write unit tests for services
- Write feature tests for controllers
- Aim for high code coverage
- Use meaningful test method names

```php
/** @test */
public function it_processes_payment_successfully()
{
    // Arrange
    $orderData = [...];
    
    // Act
    $result = $this->paymentService->processPayment($orderData, $paymentMethod);
    
    // Assert
    $this->assertTrue($result['success']);
}
```

## Commit Message Guidelines

Use conventional commit messages:

```
feat: add support for multi-currency payments
fix: correct token expiration validation
docs: update API documentation
test: add tests for refund processing
refactor: simplify payment validation logic
chore: update dependencies
```

### Commit Message Format

```
<type>: <subject>

<body>

<footer>
```

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `test`: Adding or updating tests
- `refactor`: Code refactoring
- `chore`: Maintenance tasks
- `perf`: Performance improvements

## Project Structure

```
src/
├── Controllers/       # HTTP controllers
├── Events/           # Laravel events
├── Filament/         # Filament admin resources
│   ├── Pages/       # Filament pages
│   └── Resources/   # Filament resources
├── Listeners/        # Event listeners
├── Middleware/       # HTTP middleware
├── Models/          # Eloquent models
├── Services/        # Business logic services
└── Settings/        # Settings classes

database/
├── migrations/      # Database migrations
└── seeders/        # Database seeders

tests/
├── Feature/        # Feature tests
└── Unit/          # Unit tests
```

## Adding New Features

When adding a new feature:

1. **Plan**: Discuss major changes in an issue first
2. **Implement**: Write clean, tested code
3. **Document**: Update relevant documentation
4. **Test**: Ensure all tests pass
5. **Submit**: Create a pull request

### Example: Adding a New Service

1. Create service class in `src/Services/`
2. Register in `SumitPaymentServiceProvider`
3. Write unit tests in `tests/Unit/`
4. Update documentation in README.md and API.md
5. Add changelog entry

## Documentation

Update these files when relevant:
- `README.md` - Main package documentation
- `API.md` - API endpoint and service documentation
- `MIGRATION.md` - Migration guide from WooCommerce
- `CHANGELOG.md` - Version history

## Questions?

Feel free to:
- Open an issue for questions
- Email: support@sumit.co.il
- Review existing documentation

## License

By contributing, you agree that your contributions will be licensed under the MIT License.
