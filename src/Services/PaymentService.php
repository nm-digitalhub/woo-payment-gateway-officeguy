<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Services;

use NmDigitalhub\WooPaymentGatewayAdmin\Settings\PaymentSettings;

class PaymentService
{
    public function __construct(
        protected PaymentSettings $settings,
    ) {}

    /**
     * Process a payment charge
     */
    public function charge(array $paymentData): array
    {
        $apiKey = $this->settings->api_key;
        $secretKey = $this->settings->secret_key;
        $environment = $this->settings->environment;
        
        // Payment processing logic would go here
        // This integrates with the existing WooCommerce payment gateway
        
        return [
            'success' => true,
            'transaction_id' => uniqid('txn_'),
        ];
    }

    /**
     * Check if we're in sandbox mode
     */
    public function isSandboxMode(): bool
    {
        return $this->settings->sandbox_mode;
    }

    /**
     * Get the webhook URL
     */
    public function getWebhookUrl(): ?string
    {
        return $this->settings->webhook_url;
    }
}
