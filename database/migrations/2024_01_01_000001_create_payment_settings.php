<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.api_key', '');
        $this->migrator->add('payment.secret_key', '');
        $this->migrator->add('payment.sandbox_mode', false);
        $this->migrator->add('payment.webhook_url', null);
        $this->migrator->add('payment.merchant_id', null);
        $this->migrator->add('payment.company_id', null);
        $this->migrator->add('payment.private_key', null);
        $this->migrator->add('payment.public_key', null);
        $this->migrator->add('payment.environment', 'www');
        $this->migrator->add('payment.support_tokens', false);
        $this->migrator->add('payment.authorize_only', false);
        $this->migrator->add('payment.token_param', '5');
    }
};
