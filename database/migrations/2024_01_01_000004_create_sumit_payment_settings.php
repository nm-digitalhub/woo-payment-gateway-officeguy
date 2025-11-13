<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('sumit_payment.api_base_url', 'https://api.sumit.co.il');
        $this->migrator->add('sumit_payment.api_dev_url', 'http://dev.api.sumit.co.il');
        $this->migrator->add('sumit_payment.api_timeout', 180);
        $this->migrator->add('sumit_payment.api_ssl_verify', true);

        $this->migrator->add('sumit_payment.company_id', null);
        $this->migrator->add('sumit_payment.api_key', null);
        $this->migrator->add('sumit_payment.api_public_key', null);

        $this->migrator->add('sumit_payment.environment', 'www');

        $this->migrator->add('sumit_payment.testing_mode', false);
        $this->migrator->add('sumit_payment.authorize_only', false);
        $this->migrator->add('sumit_payment.authorize_added_percent', 0.0);
        $this->migrator->add('sumit_payment.authorize_minimum_addition', 0.0);
        $this->migrator->add('sumit_payment.draft_document', true);
        $this->migrator->add('sumit_payment.email_document', true);
        $this->migrator->add('sumit_payment.merchant_number', null);
        $this->migrator->add('sumit_payment.subscription_merchant_number', null);
        $this->migrator->add('sumit_payment.pci_mode', 'redirect');
        $this->migrator->add('sumit_payment.token_param', 'J2');
        $this->migrator->add('sumit_payment.send_client_ip', true);

        $this->migrator->add('sumit_payment.document_default_language', 'he');
        $this->migrator->add('sumit_payment.document_auto_language', true);

        $this->migrator->add('sumit_payment.max_installments', 12);

        $this->migrator->add('sumit_payment.stock_sync_enabled', false);

        $this->migrator->add('sumit_payment.donations_enabled', false);

        $this->migrator->add('sumit_payment.marketplace_dokan_enabled', false);
        $this->migrator->add('sumit_payment.marketplace_wcfm_enabled', false);
        $this->migrator->add('sumit_payment.marketplace_wcvendors_enabled', false);

        $this->migrator->add('sumit_payment.logging_enabled', true);
        $this->migrator->add('sumit_payment.logging_level', 'debug');
    }
};
