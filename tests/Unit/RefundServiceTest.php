<?php

namespace Tests\Unit;

use App\Settings\PaymentSettings;
use App\Services\RefundService;
use PHPUnit\Framework\TestCase;

class RefundServiceTest extends TestCase
{
    public function test_refund_service_processes_refund()
    {
        $settings = $this->createStub(PaymentSettings::class);
        $settings->api_key = 'test_api_key';
        $settings->secret_key = 'test_secret_key';

        $service = new RefundService($settings);

        $result = $service->processRefund('txn_12345', 50.00);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('refund_id', $result);
        $this->assertEquals(50.00, $result['amount']);
    }

    public function test_refund_service_gets_status()
    {
        $settings = $this->createStub(PaymentSettings::class);
        $service = new RefundService($settings);

        $status = $service->getRefundStatus('rfnd_12345');

        $this->assertEquals('completed', $status);
    }
}
