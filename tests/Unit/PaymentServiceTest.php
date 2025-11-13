<?php

namespace NmDigitalHub\SumitPayment\Tests\Unit;

use NmDigitalHub\SumitPayment\Services\PaymentService;
use NmDigitalHub\SumitPayment\Services\ApiService;
use Orchestra\Testbench\TestCase;
use Mockery;

class PaymentServiceTest extends TestCase
{
    protected PaymentService $paymentService;
    protected $apiServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->apiServiceMock = Mockery::mock(ApiService::class);
        $this->paymentService = new PaymentService($this->apiServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_validates_payment_fields_correctly()
    {
        config(['sumit-payment.payment.pci_mode' => 'yes']);

        $invalidCardData = [
            'card_number' => 'invalid',
            'cvv' => 'abc',
            'exp_month' => 13,
            'exp_year' => 2020,
        ];

        $errors = $this->paymentService->validatePaymentFields($invalidCardData);

        $this->assertNotEmpty($errors);
    }

    /** @test */
    public function it_accepts_valid_payment_fields()
    {
        config(['sumit-payment.payment.pci_mode' => 'yes']);

        $validCardData = [
            'card_number' => '4580123456789012',
            'cvv' => '123',
            'exp_month' => '12',
            'exp_year' => (string)(date('Y') + 1),
        ];

        $errors = $this->paymentService->validatePaymentFields($validCardData);

        $this->assertEmpty($errors);
    }

    protected function getPackageProviders($app)
    {
        return ['NmDigitalHub\SumitPayment\SumitPaymentServiceProvider'];
    }
}
