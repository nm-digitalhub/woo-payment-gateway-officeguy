<?php

namespace NmDigitalHub\SumitPayment\Settings;

use Spatie\LaravelSettings\Settings;

class SumitPaymentSettings extends Settings
{
    // API Configuration
    public string $api_base_url;
    public string $api_dev_url;
    public int $api_timeout;
    public bool $api_ssl_verify;

    // Credentials
    public ?string $company_id;
    public ?string $api_key;
    public ?string $api_public_key;

    // Environment
    public string $environment;

    // Payment Configuration
    public bool $testing_mode;
    public bool $authorize_only;
    public float $authorize_added_percent;
    public float $authorize_minimum_addition;
    public bool $draft_document;
    public bool $email_document;
    public ?string $merchant_number;
    public ?string $subscription_merchant_number;
    public string $pci_mode;
    public string $token_param;
    public bool $send_client_ip;

    // Document Configuration
    public string $document_default_language;
    public bool $document_auto_language;

    // Installments
    public int $max_installments;

    // Stock Configuration
    public bool $stock_sync_enabled;

    // Donations
    public bool $donations_enabled;

    // Marketplace
    public bool $marketplace_dokan_enabled;
    public bool $marketplace_wcfm_enabled;
    public bool $marketplace_wcvendors_enabled;

    // Logging
    public bool $logging_enabled;
    public string $logging_level;

    public static function group(): string
    {
        return 'sumit_payment';
    }
}
