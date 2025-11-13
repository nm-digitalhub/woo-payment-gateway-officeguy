<?php

namespace Tests\Unit;

use App\Settings\PaymentSettings;
use App\Services\TokenService;
use PHPUnit\Framework\TestCase;

class TokenServiceTest extends TestCase
{
    public function test_token_service_checks_support()
    {
        $settings = $this->createStub(PaymentSettings::class);
        $settings->support_tokens = true;
        $settings->token_param = '5';

        $service = new TokenService($settings);

        $this->assertTrue($service->isTokenSupportEnabled());
        $this->assertEquals('5', $service->getTokenParam());
    }

    public function test_token_service_stores_token()
    {
        $settings = $this->createStub(PaymentSettings::class);
        $settings->support_tokens = true;

        $service = new TokenService($settings);

        $result = $service->storeToken('user123', [
            'token' => 'tok_12345',
            'last_four' => '4242',
        ]);

        $this->assertTrue($result);
    }
}
