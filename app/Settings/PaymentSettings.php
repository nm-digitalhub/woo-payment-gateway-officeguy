<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaymentSettings extends Settings
{
    public string $api_key;
    public string $secret_key;
    public bool $sandbox_mode;
    public ?string $webhook_url;
    public ?string $merchant_id;
    public ?string $company_id;
    public ?string $private_key;
    public ?string $public_key;
    public string $environment;
    public bool $support_tokens;
    public bool $authorize_only;
    public string $token_param;

    public static function group(): string
    {
        return 'payment';
    }
}
