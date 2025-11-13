<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SUMIT Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for SUMIT payment gateway integration
    |
    */

    'api' => [
        'base_url' => env('SUMIT_API_URL', 'https://api.sumit.co.il'),
        'dev_url' => env('SUMIT_DEV_API_URL', 'http://dev.api.sumit.co.il'),
        'timeout' => env('SUMIT_API_TIMEOUT', 180),
    ],

    'credentials' => [
        'company_id' => env('SUMIT_COMPANY_ID'),
        'api_key' => env('SUMIT_API_KEY'),
        'api_public_key' => env('SUMIT_API_PUBLIC_KEY'),
    ],

    'environment' => env('SUMIT_ENVIRONMENT', 'www'), // 'www' or 'dev'

    'payment' => [
        'testing_mode' => env('SUMIT_TESTING_MODE', false),
        'authorize_only' => env('SUMIT_AUTHORIZE_ONLY', false),
        'authorize_added_percent' => env('SUMIT_AUTHORIZE_ADDED_PERCENT', 0),
        'authorize_minimum_addition' => env('SUMIT_AUTHORIZE_MINIMUM_ADDITION', 0),
        'draft_document' => env('SUMIT_DRAFT_DOCUMENT', true),
        'email_document' => env('SUMIT_EMAIL_DOCUMENT', true),
        'merchant_number' => env('SUMIT_MERCHANT_NUMBER'),
        'subscription_merchant_number' => env('SUMIT_SUBSCRIPTION_MERCHANT_NUMBER'),
        'pci_mode' => env('SUMIT_PCI_MODE', 'redirect'), // 'yes', 'no', or 'redirect'
        'token_param' => env('SUMIT_TOKEN_PARAM', 'J2'), // 'J2' or 'J5'
        'send_client_ip' => env('SUMIT_SEND_CLIENT_IP', true),
    ],

    'documents' => [
        'default_language' => env('SUMIT_DOCUMENT_LANGUAGE', 'he'),
        'auto_language' => env('SUMIT_AUTO_DOCUMENT_LANGUAGE', true),
    ],

    'installments' => [
        'max_payments' => env('SUMIT_MAX_INSTALLMENTS', 12),
    ],

    'stock' => [
        'sync_enabled' => env('SUMIT_STOCK_SYNC_ENABLED', false),
    ],

    'donations' => [
        'enabled' => env('SUMIT_DONATIONS_ENABLED', false),
    ],

    'marketplace' => [
        'dokan_enabled' => env('SUMIT_DOKAN_ENABLED', false),
        'wcfm_enabled' => env('SUMIT_WCFM_ENABLED', false),
        'wcvendors_enabled' => env('SUMIT_WCVENDORS_ENABLED', false),
    ],

    'logging' => [
        'enabled' => env('SUMIT_LOGGING_ENABLED', true),
        'level' => env('SUMIT_LOG_LEVEL', 'debug'),
    ],
];
