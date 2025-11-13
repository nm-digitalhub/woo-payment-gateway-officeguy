<?php

namespace NmDigitalHub\SumitPayment\Services;

use NmDigitalHub\SumitPayment\Models\RecurringBilling;
use NmDigitalHub\SumitPayment\Models\PaymentToken;
use NmDigitalHub\SumitPayment\Settings\SumitPaymentSettings;

class RecurringBillingService
{
    protected ApiService $apiService;
    protected SumitPaymentSettings $settings;

    public function __construct(ApiService $apiService, SumitPaymentSettings $settings)
    {
        $this->apiService = $apiService;
        $this->settings = $settings;
    }

    /**
     * Process recurring payment
     */
    public function processRecurringPayment(RecurringBilling $billing, PaymentToken $token): array
    {
        $request = $this->buildRecurringPaymentRequest($billing, $token);
        
        $response = $this->apiService->post(
            $request,
            '/creditguy/gateway/transaction/',
            $this->settings->send_client_ip
        );

        if ($response === null) {
            return [
                'success' => false,
                'error' => 'No response from payment gateway',
            ];
        }

        if ($response['Status'] == 0 && ($response['Data']['Success'] ?? false)) {
            $billing->last_payment_date = now();
            $billing->next_payment_date = $this->calculateNextPaymentDate($billing);
            $billing->save();

            return [
                'success' => true,
                'transaction_id' => $response['Data']['TransactionID'] ?? null,
                'document_id' => $response['Data']['DocumentID'] ?? null,
            ];
        }

        return [
            'success' => false,
            'error' => $response['Data']['ResultDescription'] ?? $response['UserErrorMessage'] ?? 'Payment failed',
        ];
    }

    /**
     * Build recurring payment request
     */
    protected function buildRecurringPaymentRequest(RecurringBilling $billing, PaymentToken $token): array
    {
        return [
            'Credentials' => [
                'CompanyID' => $this->settings->company_id,
                'APIKey' => $this->settings->api_key,
            ],
            'Items' => $this->prepareItems($billing),
            'VATIncluded' => 'true',
            'VATRate' => $billing->vat_rate ?? 0,
            'Customer' => $this->prepareCustomer($billing),
            'PaymentMethod' => [
                'Token' => $token->token,
            ],
            'Payments_Count' => '1',
            'SendDocumentByEmail' => $this->settings->email_document ? 'true' : 'false',
            'UpdateCustomerByEmail' => 'true',
            'DocumentDescription' => "Recurring payment for subscription #{$billing->id}",
            'MerchantNumber' => $this->settings->subscription_merchant_number,
        ];
    }

    /**
     * Prepare items for recurring payment
     */
    protected function prepareItems(RecurringBilling $billing): array
    {
        return [
            [
                'Name' => $billing->description ?? 'Subscription',
                'Price' => $billing->amount,
                'Quantity' => 1,
                'IsPriceIncludeVAT' => true,
            ],
        ];
    }

    /**
     * Prepare customer for recurring payment
     */
    protected function prepareCustomer(RecurringBilling $billing): array
    {
        return [
            'Name' => $billing->customer_name ?? '',
            'Email' => $billing->customer_email ?? '',
        ];
    }

    /**
     * Calculate next payment date based on billing frequency
     */
    protected function calculateNextPaymentDate(RecurringBilling $billing): \DateTime
    {
        $nextDate = new \DateTime($billing->next_payment_date ?? 'now');
        
        switch ($billing->frequency) {
            case 'daily':
                $nextDate->modify('+1 day');
                break;
            case 'weekly':
                $nextDate->modify('+1 week');
                break;
            case 'monthly':
                $nextDate->modify('+1 month');
                break;
            case 'yearly':
                $nextDate->modify('+1 year');
                break;
            default:
                throw new \InvalidArgumentException("Invalid billing frequency: {$billing->frequency}. Expected: daily, weekly, monthly, or yearly.");
        }

        return $nextDate;
    }

    /**
     * Create a recurring billing subscription
     */
    public function createSubscription(array $subscriptionData): RecurringBilling
    {
        return RecurringBilling::create($subscriptionData);
    }

    /**
     * Cancel a recurring billing subscription
     */
    public function cancelSubscription(int $billingId): bool
    {
        $billing = RecurringBilling::find($billingId);
        
        if ($billing) {
            $billing->status = 'cancelled';
            return $billing->save();
        }

        return false;
    }

    /**
     * Get due recurring payments
     */
    public function getDuePayments(): \Illuminate\Database\Eloquent\Collection
    {
        return RecurringBilling::where('status', 'active')
            ->where('next_payment_date', '<=', now())
            ->get();
    }
}
