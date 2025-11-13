# API Documentation

This document describes the API endpoints and methods available in the SUMIT Payment Laravel package.

## Payment Endpoints

### Process Payment

**Endpoint:** `POST /sumit-payment/process`

**Description:** Process a payment transaction

**Request Body:**
```json
{
  "order_id": "12345",
  "amount": 100.00,
  "currency": "ILS",
  "items": [
    {
      "name": "Product Name",
      "price": 100.00,
      "quantity": 1,
      "sku": "PROD-001"
    }
  ],
  "customer": {
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+972501234567",
    "address": "123 Main St",
    "city": "Tel Aviv",
    "country": "IL",
    "zip_code": "12345"
  },
  "payment_method": {
    "card_number": "4580123456789012",
    "cvv": "123",
    "exp_month": 12,
    "exp_year": 2025,
    "citizen_id": "123456789"
  },
  "payments_count": 1
}
```

**Response (Success):**
```json
{
  "success": true,
  "transaction_id": 123,
  "document_id": "DOC-456",
  "customer_id": "CUST-789",
  "auth_number": "AUTH-012",
  "card_last4": "9012"
}
```

**Response (Error):**
```json
{
  "success": false,
  "error": "Card number is invalid"
}
```

### Handle Redirect Callback

**Endpoint:** `GET /sumit-payment/redirect`

**Description:** Handle payment redirect callback from SUMIT

**Query Parameters:**
- `OG-OrderID`: Order identifier
- `Success`: Payment success status (true/false)

**Response:** Redirects to success or failure page

### Process Refund

**Endpoint:** `POST /sumit-payment/refund`

**Description:** Process a refund for a transaction

**Request Body:**
```json
{
  "transaction_id": "TRANS-123",
  "amount": 50.00
}
```

**Response (Success):**
```json
{
  "success": true,
  "refund_id": "REFUND-456"
}
```

## Token Endpoints

All token endpoints require authentication.

### List User Tokens

**Endpoint:** `GET /sumit-payment/tokens`

**Description:** Get all payment tokens for the authenticated user

**Response:**
```json
{
  "success": true,
  "tokens": [
    {
      "id": 1,
      "card_type": "card",
      "card_last4": "9012",
      "card_brand": "Visa",
      "exp_month": "12",
      "exp_year": "2025",
      "is_default": true,
      "created_at": "2024-01-01T00:00:00Z"
    }
  ]
}
```

### Create Token

**Endpoint:** `POST /sumit-payment/tokens`

**Description:** Create a new payment token

**Request Body (PCI Mode):**
```json
{
  "card_number": "4580123456789012",
  "cvv": "123",
  "exp_month": 12,
  "exp_year": 2025,
  "citizen_id": "123456789"
}
```

**Request Body (Tokenized Mode):**
```json
{
  "single_use_token": "TOKEN-ABC123"
}
```

**Response (Success):**
```json
{
  "success": true,
  "token": {
    "id": 1,
    "card_last4": "9012",
    "exp_month": "12",
    "exp_year": "2025"
  }
}
```

### Delete Token

**Endpoint:** `DELETE /sumit-payment/tokens/{tokenId}`

**Description:** Delete a payment token

**Response:**
```json
{
  "success": true
}
```

### Set Default Token

**Endpoint:** `POST /sumit-payment/tokens/{tokenId}/set-default`

**Description:** Set a token as the default payment method

**Response:**
```json
{
  "success": true
}
```

## Service Classes

### PaymentService

#### `processPayment(array $orderData, array $paymentMethod, int $paymentsCount = 1): array`

Process a payment transaction.

**Parameters:**
- `$orderData`: Order information including items, customer, total
- `$paymentMethod`: Payment method details (card, token, or single-use token)
- `$paymentsCount`: Number of installments (default: 1)

**Returns:**
```php
[
    'success' => true|false,
    'transaction_id' => 123,
    'document_id' => 'DOC-456',
    'error' => 'Error message' // Only on failure
]
```

#### `processRefund(string $transactionId, float $amount): array`

Process a refund for a transaction.

**Parameters:**
- `$transactionId`: Transaction identifier
- `$amount`: Refund amount

**Returns:**
```php
[
    'success' => true|false,
    'refund_id' => 'REFUND-456',
    'error' => 'Error message' // Only on failure
]
```

#### `validatePaymentFields(array $paymentData): array`

Validate payment fields before processing.

**Parameters:**
- `$paymentData`: Payment method data to validate

**Returns:** Array of validation errors (empty if valid)

### TokenService

#### `createToken(array $cardData, ?int $userId = null): array`

Create a new payment token.

**Parameters:**
- `$cardData`: Card information
- `$userId`: User ID to associate token with (optional)

**Returns:**
```php
[
    'success' => true|false,
    'token' => PaymentToken, // Only on success
    'error' => 'Error message' // Only on failure
]
```

#### `getToken(int $tokenId, ?int $userId = null): ?PaymentToken`

Get a token by ID.

#### `getUserTokens(int $userId): Collection`

Get all tokens for a user.

#### `deleteToken(int $tokenId, ?int $userId = null): bool`

Delete a token.

#### `setDefaultToken(int $tokenId, int $userId): bool`

Set a token as default for a user.

### RecurringBillingService

#### `processRecurringPayment(RecurringBilling $billing, PaymentToken $token): array`

Process a recurring payment.

#### `createSubscription(array $subscriptionData): RecurringBilling`

Create a new subscription.

#### `cancelSubscription(int $billingId): bool`

Cancel a subscription.

#### `getDuePayments(): Collection`

Get all due recurring payments.

### StockService

#### `syncStock(array $products): array`

Sync stock levels from SUMIT.

#### `updateStockAfterPurchase(string $productId, int $quantity): array`

Update stock after a purchase.

#### `getStockLevel(string $productId): ?int`

Get current stock level for a product.

### ApiService

#### `post(array $request, string $path, ?bool $sendClientIP = null): ?array`

Send POST request to SUMIT API.

#### `checkCredentials(string $companyId, string $apiKey): ?string`

Validate API credentials.

#### `checkPublicCredentials(string $companyId, string $apiPublicKey): ?string`

Validate public API credentials.

## Events

### PaymentProcessed

Dispatched when a payment is successfully processed.

**Properties:**
- `$transaction`: PaymentTransaction model
- `$response`: API response data

**Example:**
```php
use NmDigitalHub\SumitPayment\Events\PaymentProcessed;

Event::listen(PaymentProcessed::class, function ($event) {
    $transaction = $event->transaction;
    $response = $event->response;
    // Custom logic here
});
```

### PaymentFailed

Dispatched when a payment fails.

**Properties:**
- `$orderData`: Order data array
- `$errorMessage`: Error message

**Example:**
```php
use NmDigitalHub\SumitPayment\Events\PaymentFailed;

Event::listen(PaymentFailed::class, function ($event) {
    $orderData = $event->orderData;
    $error = $event->errorMessage;
    // Custom error handling
});
```

## Models

### PaymentTransaction

**Fields:**
- `id`: Primary key
- `order_id`: External order identifier
- `amount`: Transaction amount
- `currency`: Currency code (ILS, USD, etc.)
- `status`: Transaction status (completed, failed, pending, refunded)
- `transaction_id`: SUMIT transaction ID
- `document_id`: SUMIT document ID
- `customer_id`: SUMIT customer ID
- `auth_number`: Authorization number
- `card_last4`: Last 4 digits of card
- `card_brand`: Card brand
- `response_data`: Full API response (JSON)
- `error_message`: Error message if failed
- `processed_at`: Processing timestamp

**Scopes:**
- `successful()`: Get successful transactions
- `failed()`: Get failed transactions
- `pending()`: Get pending transactions

### PaymentToken

**Fields:**
- `id`: Primary key
- `user_id`: Associated user ID
- `token`: Encrypted token string
- `card_type`: Card type
- `card_last4`: Last 4 digits
- `card_brand`: Card brand
- `exp_month`: Expiration month
- `exp_year`: Expiration year
- `citizen_id`: Citizen ID (encrypted)
- `is_default`: Default token flag

**Methods:**
- `isExpired()`: Check if token is expired
- `getFormattedExpiration()`: Get formatted expiration date
- `getMaskedCardNumber()`: Get masked card number

**Scopes:**
- `active()`: Get non-expired tokens
- `default()`: Get default tokens

### RecurringBilling

**Fields:**
- `id`: Primary key
- `user_id`: Associated user ID
- `payment_token_id`: Associated token ID
- `amount`: Billing amount
- `currency`: Currency code
- `vat_rate`: VAT rate
- `frequency`: Billing frequency (daily, weekly, monthly, yearly)
- `description`: Subscription description
- `customer_name`: Customer name
- `customer_email`: Customer email
- `status`: Subscription status (active, cancelled, suspended)
- `next_payment_date`: Next payment date
- `last_payment_date`: Last payment date
- `started_at`: Subscription start date
- `ended_at`: Subscription end date

**Scopes:**
- `active()`: Get active subscriptions
- `cancelled()`: Get cancelled subscriptions
- `due()`: Get subscriptions with due payments

**Methods:**
- `isActive()`: Check if subscription is active
- `isDue()`: Check if payment is due
