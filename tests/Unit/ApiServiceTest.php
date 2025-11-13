<?php

namespace NmDigitalHub\SumitPayment\Tests\Unit;

use NmDigitalHub\SumitPayment\Services\ApiService;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Config;

class ApiServiceTest extends TestCase
{
    protected ApiService $apiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiService = new ApiService();
    }

    /** @test */
    public function it_builds_correct_url_for_production_environment()
    {
        Config::set('sumit-payment.environment', 'www');
        Config::set('sumit-payment.api.base_url', 'https://api.sumit.co.il');

        $url = $this->apiService->getUrl('/test/path');

        $this->assertEquals('https://api.sumit.co.il/test/path', $url);
    }

    /** @test */
    public function it_builds_correct_url_for_dev_environment()
    {
        Config::set('sumit-payment.environment', 'dev');
        Config::set('sumit-payment.api.dev_url', 'http://dev.api.sumit.co.il');

        $url = $this->apiService->getUrl('/test/path');

        $this->assertEquals('http://dev.api.sumit.co.il/test/path', $url);
    }

    protected function getPackageProviders($app)
    {
        return ['NmDigitalHub\SumitPayment\SumitPaymentServiceProvider'];
    }
}
