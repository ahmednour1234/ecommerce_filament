<?php

namespace Tests\Feature\Finance;

use App\Filament\Pages\Finance\IncomeReportPage;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Tests\TestCase;

/**
 * Test suite for IncomeReportPage form method
 * 
 * Tests that the form() method can handle:
 * - Being called without arguments (for Filament actions)
 * - Receiving a Form instance
 * - Receiving an Infolist instance (converts to Form)
 */
class IncomeReportPageTest extends TestCase
{
    public function test_form_method_signature_accepts_null(): void
    {
        // Test that the method signature allows null parameter
        $reflection = new \ReflectionMethod(IncomeReportPage::class, 'form');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertTrue($parameters[0]->allowsNull());
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    public function test_form_method_can_be_called_without_arguments(): void
    {
        // This test verifies the method can be called without arguments
        // which was the original error: "Too few arguments to function"
        $page = new IncomeReportPage();
        
        // Should not throw ArgumentCountError
        try {
            $form = $page->form();
            $this->assertInstanceOf(Form::class, $form);
        } catch (\ArgumentCountError $e) {
            $this->fail('form() method should accept no arguments: ' . $e->getMessage());
        }
    }

    public function test_form_method_accepts_form_instance(): void
    {
        $page = new IncomeReportPage();
        $formInstance = Form::make($page)->statePath('data');
        
        $result = $page->form($formInstance);
        
        $this->assertInstanceOf(Form::class, $result);
    }

    public function test_form_method_handles_infolist_instance(): void
    {
        $page = new IncomeReportPage();
        $infolistInstance = Infolist::make($page);
        
        // Should convert Infolist to Form without throwing TypeError
        try {
            $result = $page->form($infolistInstance);
            $this->assertInstanceOf(Form::class, $result);
            $this->assertNotInstanceOf(Infolist::class, $result);
        } catch (\TypeError $e) {
            $this->fail('form() method should handle Infolist instances: ' . $e->getMessage());
        }
    }
}
