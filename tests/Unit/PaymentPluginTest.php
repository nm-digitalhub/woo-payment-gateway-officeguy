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
        
        $this->assertEquals('payment', $plugin->getId());
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

    public function test_plugin_register_does_not_configure_panel_settings()
    {
        $plugin = new PaymentPlugin();
        
        // Create a mock panel to verify plugin doesn't set panel-level configuration
        $panel = $this->createMock(\Filament\Panel::class);
        
        // The plugin should NOT call these panel configuration methods
        $panel->expects($this->never())->method('id');
        $panel->expects($this->never())->method('path');
        $panel->expects($this->never())->method('middleware');
        $panel->expects($this->never())->method('authMiddleware');
        $panel->expects($this->never())->method('colors');
        
        // The plugin SHOULD call discovery methods
        $panel->expects($this->once())
            ->method('discoverResources')
            ->willReturnSelf();
        
        $panel->expects($this->once())
            ->method('discoverPages')
            ->willReturnSelf();
        
        $panel->expects($this->once())
            ->method('discoverWidgets')
            ->willReturnSelf();
        
        $plugin->register($panel);
    }
}
