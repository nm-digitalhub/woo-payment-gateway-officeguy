<?php

namespace NmDigitalHub\SumitPayment\Services;

use NmDigitalHub\SumitPayment\Models\PaymentTransaction;
use NmDigitalHub\SumitPayment\Events\PaymentProcessed;
use NmDigitalHub\SumitPayment\Events\PaymentFailed;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

class PaymentService
{
    protected ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get API credentials from config
     */
    public function getCredentials(): array
    {
        return [
            'CompanyID' => Config::get('sumit-payment.credentials.company_id'),
            'APIKey' => Config::get('sumit-payment.credentials.api_key'),
        ];
    }

    /**
     * Get public API credentials from config
     */
    public function getPublicCredentials(): array
    {
        return [
            'CompanyID' => Config::get('sumit-payment.credentials.company_id'),
            'APIPublicKey' => Config::get('sumit-payment.credentials.api_public_key'),
        ];
    }

    /**
     * Process a payment transaction
     */
    public function processPayment(array $orderData, array $paymentMethod, int $paymentsCount = 1): array
    {
        $request = $this->buildPaymentRequest($orderData, $paymentMethod, $paymentsCount);
        
        $response = $this->apiService->post(
            $request,
            '/creditguy/gateway/transaction/',
            Config::get('sumit-payment.payment.send_client_ip', true)
        );

        if ($response === null) {
            Event::dispatch(new PaymentFailed($orderData, 'No response from payment gateway'));
            return [
                'success' => false,
                'error' => 'No response from payment gateway',
            ];
        }

        if ($response['Status'] == 0 && ($response['Data']['Success'] ?? false)) {
            // Payment successful
            $transaction = $this->createTransaction($orderData, $response);
            
            Event::dispatch(new PaymentProcessed($transaction, $response));
            
            return [
                'success' => true,
                'transaction_id' => $transaction->id,
                'document_id' => $response['Data']['DocumentID'] ?? null,
                'customer_id' => $response['Data']['CustomerID'] ?? null,
                'auth_number' => $response['Data']['AuthNumber'] ?? null,
                'card_last4' => $response['Data']['Last4Digits'] ?? null,
            ];
        }

        // Payment failed
        $errorMessage = $response['Data']['ResultDescription'] ?? $response['UserErrorMessage'] ?? 'Payment failed';
        Event::dispatch(new PaymentFailed($orderData, $errorMessage));
        
        return [
            'success' => false,
            'error' => $errorMessage,
        ];
    }

    /**
     * Build payment request array
     */
    protected function buildPaymentRequest(array $orderData, array $paymentMethod, int $paymentsCount): array
    {
        $request = [
            'Credentials' => $this->getCredentials(),
            'Items' => $this->prepareItems($orderData['items'] ?? []),
            'VATIncluded' => 'true',
            'VATRate' => $orderData['vat_rate'] ?? 0,
            'Customer' => $this->prepareCustomer($orderData['customer'] ?? []),
            'AuthoriseOnly' => Config::get('sumit-payment.payment.testing_mode') ? 'true' : 'false',
            'DraftDocument' => Config::get('sumit-payment.payment.draft_document') ? 'true' : 'false',
            'SendDocumentByEmail' => Config::get('sumit-payment.payment.email_document') ? 'true' : 'false',
            'DocumentDescription' => $orderData['description'] ?? '',
            'Payments_Count' => (string) $paymentsCount,
            'MaximumPayments' => $this->getMaximumPayments($orderData['total'] ?? 0),
            'DocumentLanguage' => $this->getDocumentLanguage(),
            'MerchantNumber' => Config::get('sumit-payment.payment.merchant_number'),
        ];

        // Add authorization settings if enabled
        if (Config::get('sumit-payment.payment.authorize_only')) {
            $request['AutoCapture'] = 'false';
            $request['AuthorizeAmount'] = $this->calculateAuthorizeAmount($orderData['total'] ?? 0);
        }

        // Add payment method
        if (isset($paymentMethod['token'])) {
            $request['PaymentMethod'] = $this->getPaymentMethodFromToken($paymentMethod['token']);
        } elseif (isset($paymentMethod['single_use_token'])) {
            $request['SingleUseToken'] = $paymentMethod['single_use_token'];
        } elseif (isset($paymentMethod['card'])) {
            $request['PaymentMethod'] = $this->getPaymentMethodFromCard($paymentMethod['card']);
        }

        // Add redirect URL if using redirect flow
        if (Config::get('sumit-payment.payment.pci_mode') === 'redirect' && isset($orderData['redirect_url'])) {
            $request['RedirectURL'] = $orderData['redirect_url'];
        }

        return $request;
    }

    /**
     * Prepare items for payment request
     */
    protected function prepareItems(array $items): array
    {
        $preparedItems = [];

        foreach ($items as $item) {
            $preparedItem = [
                'Name' => $item['name'] ?? '',
                'Price' => $item['price'] ?? 0,
                'Quantity' => $item['quantity'] ?? 1,
                'IsPriceIncludeVAT' => true,
            ];

            if (isset($item['sku'])) {
                $preparedItem['CatalogNumber'] = $item['sku'];
            }

            $preparedItems[] = $preparedItem;
        }

        return $preparedItems;
    }

    /**
     * Prepare customer data for payment request
     */
    protected function prepareCustomer(array $customer): array
    {
        return [
            'Name' => $customer['name'] ?? '',
            'Email' => $customer['email'] ?? '',
            'Phone' => $customer['phone'] ?? '',
            'Address' => $customer['address'] ?? '',
            'City' => $customer['city'] ?? '',
            'Country' => $customer['country'] ?? '',
            'ZipCode' => $customer['zip_code'] ?? '',
        ];
    }

    /**
     * Get payment method from token
     */
    protected function getPaymentMethodFromToken(string $token): array
    {
        return [
            'Token' => $token,
        ];
    }

    /**
     * Get payment method from card data
     */
    protected function getPaymentMethodFromCard(array $card): array
    {
        return [
            'CreditCard_Number' => $card['number'] ?? '',
            'CreditCard_CVV' => $card['cvv'] ?? '',
            'CreditCard_ExpirationMonth' => $card['exp_month'] ?? '',
            'CreditCard_ExpirationYear' => $card['exp_year'] ?? '',
            'CreditCard_CitizenID' => $card['citizen_id'] ?? '',
        ];
    }

    /**
     * Get maximum allowed installments
     */
    protected function getMaximumPayments(float $amount): int
    {
        return Config::get('sumit-payment.installments.max_payments', 12);
    }

    /**
     * Get document language
     */
    protected function getDocumentLanguage(): string
    {
        if (Config::get('sumit-payment.documents.auto_language', true)) {
            return app()->getLocale();
        }
        
        return Config::get('sumit-payment.documents.default_language', 'he');
    }

    /**
     * Calculate authorize amount with added percentage/minimum
     */
    protected function calculateAuthorizeAmount(float $orderAmount): float
    {
        $authorizeAmount = $orderAmount;
        
        $addedPercent = Config::get('sumit-payment.payment.authorize_added_percent', 0);
        if ($addedPercent > 0) {
            $authorizeAmount = $authorizeAmount * (1 + $addedPercent / 100);
        }

        $minimumAddition = Config::get('sumit-payment.payment.authorize_minimum_addition', 0);
        if ($minimumAddition > 0 && ($authorizeAmount - $orderAmount) < $minimumAddition) {
            $authorizeAmount = $orderAmount + $minimumAddition;
        }

        return round($authorizeAmount, 2);
    }

    /**
     * Create transaction record
     */
    protected function createTransaction(array $orderData, array $response): PaymentTransaction
    {
        return PaymentTransaction::create([
            'order_id' => $orderData['order_id'] ?? null,
            'amount' => $orderData['total'] ?? 0,
            'currency' => $orderData['currency'] ?? 'ILS',
            'status' => 'completed',
            'transaction_id' => $response['Data']['TransactionID'] ?? null,
            'document_id' => $response['Data']['DocumentID'] ?? null,
            'customer_id' => $response['Data']['CustomerID'] ?? null,
            'auth_number' => $response['Data']['AuthNumber'] ?? null,
            'card_last4' => $response['Data']['Last4Digits'] ?? null,
            'response_data' => $response,
        ]);
    }

    /**
     * Process refund
     */
    public function processRefund(string $transactionId, float $amount): array
    {
        $request = [
            'Credentials' => $this->getCredentials(),
            'TransactionID' => $transactionId,
            'Amount' => $amount,
        ];

        $response = $this->apiService->post($request, '/creditguy/gateway/refund/', false);

        if ($response === null) {
            return [
                'success' => false,
                'error' => 'No response from payment gateway',
            ];
        }

        if ($response['Status'] == 0 && ($response['Data']['Success'] ?? false)) {
            return [
                'success' => true,
                'refund_id' => $response['Data']['RefundID'] ?? null,
            ];
        }

        return [
            'success' => false,
            'error' => $response['UserErrorMessage'] ?? 'Refund failed',
        ];
    }

    /**
     * Validate payment fields
     */
    public function validatePaymentFields(array $paymentData): array
    {
        $errors = [];

        // If using saved token, no validation needed
        if (isset($paymentData['token']) && $paymentData['token'] !== 'new') {
            return $errors;
        }

        $pciMode = Config::get('sumit-payment.payment.pci_mode');

        if ($pciMode === 'yes') {
            // Validate card details
            if (empty($paymentData['card_number']) || !ctype_digit($paymentData['card_number'])) {
                $errors[] = 'Card number is invalid';
            }

            if (empty($paymentData['cvv']) || !ctype_digit($paymentData['cvv'])) {
                $errors[] = 'Card security code is invalid';
            }

            $currentYear = (int) date('Y');
            $expMonth = (int) ($paymentData['exp_month'] ?? 0);
            $expYear = (int) ($paymentData['exp_year'] ?? 0);

            if ($expMonth < 1 || $expMonth > 12 || $expYear < $currentYear || $expYear > $currentYear + 20) {
                $errors[] = 'Card expiration date is invalid';
            }
        } elseif ($pciMode === 'no') {
            // Validate single-use token
            if (empty($paymentData['single_use_token'])) {
                $errors[] = 'Payment token is required';
            }
        }

        return $errors;
    }
}
