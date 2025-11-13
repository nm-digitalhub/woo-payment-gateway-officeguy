<?php

namespace NmDigitalHub\SumitPayment\Tests\Feature;

use NmDigitalHub\SumitPayment\Services\PaymentService;
use NmDigitalHub\SumitPayment\Services\TokenService;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /** @test */
    public function it_processes_payment_successfully()
    {
        $paymentServiceMock = Mockery::mock(PaymentService::class);
        $paymentServiceMock->shouldReceive('validatePaymentFields')
            ->andReturn([]);
        $paymentServiceMock->shouldReceive('processPayment')
            ->andReturn([
                'success' => true,
                'transaction_id' => 123,
                'document_id' => 'DOC-456',
            ]);

        $this->app->instance(PaymentService::class, $paymentServiceMock);

        $response = $this->postJson('/sumit-payment/process', [
            'order_id' => '12345',
            'amount' => 100.00,
            'currency' => 'ILS',
            'items' => [
                ['name' => 'Test Product', 'price' => 100.00, 'quantity' => 1]
            ],
            'customer' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
            'payment_method' => [
                'card_number' => '4580123456789012',
                'cvv' => '123',
                'exp_month' => 12,
                'exp_year' => 2025,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'transaction_id' => 123,
        ]);
    }

    /** @test */
    public function it_returns_validation_errors()
    {
        $paymentServiceMock = Mockery::mock(PaymentService::class);
        $paymentServiceMock->shouldReceive('validatePaymentFields')
            ->andReturn(['Card number is invalid']);

        $this->app->instance(PaymentService::class, $paymentServiceMock);

        $response = $this->postJson('/sumit-payment/process', [
            'order_id' => '12345',
            'amount' => 100.00,
            'currency' => 'ILS',
            'items' => [],
            'customer' => [],
            'payment_method' => [
                'card_number' => 'invalid',
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['success', 'errors']);
    }

    /** @test */
    public function it_requires_authentication_for_token_endpoints()
    {
        $response = $this->getJson('/sumit-payment/tokens');

        $response->assertStatus(401);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return ['NmDigitalHub\SumitPayment\SumitPaymentServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
