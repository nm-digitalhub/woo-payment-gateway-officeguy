<?php

namespace Tests\Unit;

use App\Settings\PaymentSettings;
use App\Services\PaymentService;
use PHPUnit\Framework\TestCase;

class PaymentServiceTest extends TestCase
{
    public function test_payment_service_uses_settings()
    {
        // Create mock settings
        $settings = $this->createMock(PaymentSettings::class);
        $settings->api_key = 'test_api_key';
        $settings->secret_key = 'test_secret_key';
        $settings->sandbox_mode = true;
        $settings->environment = 'test';

        // Create service with settings
        $service = new PaymentService($settings);

        // Verify sandbox mode
        $this->assertTrue($service->isSandboxMode());
    }

    public function test_payment_service_processes_charge()
    {
        $settings = $this->createMock(PaymentSettings::class);
        $settings->api_key = 'test_api_key';
        $settings->secret_key = 'test_secret_key';

        $service = new PaymentService($settings);

        $result = $service->charge([
            'amount' => 100.00,
            'currency' => 'USD',
        ]);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('transaction_id', $result);
    }
}
