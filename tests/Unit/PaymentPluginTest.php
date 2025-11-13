<?php

namespace Tests\Unit;

use NmDigitalhub\WooPaymentGatewayAdmin\PaymentPlugin;
use PHPUnit\Framework\TestCase;

/**
 * PaymentPluginTest - Tests for the PaymentPlugin Filament v4 implementation
 * 
 * These tests verify that the PaymentPlugin properly implements the
 * Filament Plugin interface and follows the plugin pattern.
 */
class PaymentPluginTest extends TestCase
{
    public function test_plugin_has_correct_id()
    {
        $plugin = new PaymentPlugin();
        
        $this->assertEquals('woo-payment-gateway-admin', $plugin->getId());
    }

    public function test_plugin_implements_filament_plugin_interface()
    {
        $plugin = new PaymentPlugin();
        
        $this->assertInstanceOf(\Filament\Contracts\Plugin::class, $plugin);
    }

    public function test_plugin_has_required_methods()
    {
        $plugin = new PaymentPlugin();
        
        // Verify all required Plugin interface methods exist
        $this->assertTrue(method_exists($plugin, 'getId'));
        $this->assertTrue(method_exists($plugin, 'register'));
        $this->assertTrue(method_exists($plugin, 'boot'));
    }

    public function test_plugin_has_make_factory_method()
    {
        // Test that make() static method exists
        $this->assertTrue(method_exists(PaymentPlugin::class, 'make'));
    }

    public function test_plugin_id_is_string()
    {
        $plugin = new PaymentPlugin();
        
        $this->assertIsString($plugin->getId());
        $this->assertNotEmpty($plugin->getId());
    }
}
