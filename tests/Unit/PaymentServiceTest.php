<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Payment Service Test
 * 
 * This test validates payment service functionality for both:
 * - Application mode (App\Services\PaymentService)
 * - Package mode (NmDigitalHub\SumitPayment\Services\PaymentService)
 */
class PaymentServiceTest extends TestCase
{
    /**
     * Test that payment service can be instantiated with settings (App mode)
     */
    public function test_app_payment_service_uses_settings()
    {
        if (!class_exists('App\Settings\PaymentSettings')) {
            $this->markTestSkipped('App\Settings\PaymentSettings not available');
        }

        if (!class_exists('App\Services\PaymentService')) {
            $this->markTestSkipped('App\Services\PaymentService not available');
        }

        // Create mock settings
        $settings = $this->createMock(\App\Settings\PaymentSettings::class);
        $settings->api_key = 'test_api_key';
        $settings->secret_key = 'test_secret_key';
        $settings->sandbox_mode = true;
        $settings->environment = 'test';

        // Create service with settings
        $service = new \App\Services\PaymentService($settings);

        // Verify sandbox mode
        $this->assertTrue($service->isSandboxMode());
    }

    /**
     * Test payment service processes charge (App mode)
     */
    public function test_app_payment_service_processes_charge()
    {
        if (!class_exists('App\Settings\PaymentSettings')) {
            $this->markTestSkipped('App\Settings\PaymentSettings not available');
        }

        if (!class_exists('App\Services\PaymentService')) {
            $this->markTestSkipped('App\Services\PaymentService not available');
        }

        $settings = $this->createMock(\App\Settings\PaymentSettings::class);
        $settings->api_key = 'test_api_key';
        $settings->secret_key = 'test_secret_key';

        $service = new \App\Services\PaymentService($settings);

        $result = $service->charge([
            'amount' => 100.00,
            'currency' => 'USD',
        ]);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('transaction_id', $result);
    }

    /**
     * Test payment field validation (Package mode)
     */
    public function test_package_validates_payment_fields_correctly()
    {
        if (!class_exists('NmDigitalHub\SumitPayment\Services\PaymentService')) {
            $this->markTestSkipped('Package PaymentService not available');
        }

        // This test would require proper package setup with Orchestra Testbench
        // Skipping for now as it requires different test base class
        $this->markTestSkipped('Package mode tests require Orchestra Testbench setup');
    }

    /**
     * Test that invalid payment fields are rejected (Package mode)
     */
    public function test_package_accepts_valid_payment_fields()
    {
        if (!class_exists('NmDigitalHub\SumitPayment\Services\PaymentService')) {
            $this->markTestSkipped('Package PaymentService not available');
        }

        // This test would require proper package setup with Orchestra Testbench
        // Skipping for now as it requires different test base class
        $this->markTestSkipped('Package mode tests require Orchestra Testbench setup');
    }
}
