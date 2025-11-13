<?php

namespace Tests\Unit;

use Filament\Panel;
use NmDigitalhub\WooPaymentGatewayAdmin\PaymentPlugin;
use PHPUnit\Framework\TestCase;

class PaymentPluginTest extends TestCase
{
    public function test_plugin_has_correct_id()
    {
        $plugin = new PaymentPlugin();
        
        $this->assertEquals('payment', $plugin->getId());
    }

    public function test_plugin_can_be_instantiated_with_make()
    {
        $plugin = PaymentPlugin::make();
        
        $this->assertInstanceOf(PaymentPlugin::class, $plugin);
    }

    public function test_plugin_can_be_instantiated_with_get()
    {
        $plugin = PaymentPlugin::get();
        
        $this->assertInstanceOf(PaymentPlugin::class, $plugin);
    }

    public function test_plugin_implements_plugin_interface()
    {
        $plugin = new PaymentPlugin();
        
        $this->assertInstanceOf(\Filament\Contracts\Plugin::class, $plugin);
    }

    public function test_plugin_can_register_with_panel()
    {
        $plugin = new PaymentPlugin();
        $panel = $this->createMock(Panel::class);
        
        // Test that register method can be called without errors
        $plugin->register($panel);
        
        // If we got here without exceptions, the test passes
        $this->assertTrue(true);
    }

    public function test_plugin_can_boot_with_panel()
    {
        $plugin = new PaymentPlugin();
        $panel = $this->createMock(Panel::class);
        
        // Test that boot method can be called without errors
        $plugin->boot($panel);
        
        // If we got here without exceptions, the test passes
        $this->assertTrue(true);
    }
}
