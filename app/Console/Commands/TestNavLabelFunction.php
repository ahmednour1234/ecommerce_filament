<?php

namespace App\Console\Commands;

use App\Providers\Filament\AdminPanelProvider;
use App\Services\MainCore\TranslationService;
use Illuminate\Console\Command;
use ReflectionClass;

class TestNavLabelFunction extends Command
{
    protected $signature = 'test:nav-label';
    protected $description = 'Test navLabel function directly';

    public function handle()
    {
        $this->info('Testing navLabel function...');
        $this->newLine();

        // Create instance using reflection
        $provider = new AdminPanelProvider(app());
        $reflection = new ReflectionClass($provider);
        $method = $reflection->getMethod('navLabel');
        $method->setAccessible(true);

        $groups = [
            'sidebar.app_management' => 'إدارة التطبيق',
            'sidebar.notifications' => 'التنبيهات',
            'sidebar.clients' => 'العملاء',
            'sidebar.company_visas' => 'تأشيرات الشركة',
            'sidebar.follow_up' => 'المتابعة',
            'sidebar.finance' => 'قسم الحسابات',
            'sidebar.hr' => 'الموارد البشرية',
            'sidebar.housing' => 'الإيواء',
            'sidebar.branches' => 'الفروع',
            'sidebar.settings' => 'الإعدادات',
        ];

        $this->table(
            ['Key', 'Expected Arabic', 'Result', 'Status'],
            collect($groups)->map(function ($expected, $key) use ($method, $provider) {
                $closure = $method->invoke($provider, $key, $expected);
                $result = $closure();
                
                $status = ($result === $expected) ? '✓' : '✗';
                
                return [
                    $key,
                    $expected,
                    $result,
                    $status,
                ];
            })->toArray()
        );

        $this->newLine();
        $this->info('Testing TranslationService directly...');
        
        $translationService = app(TranslationService::class);
        foreach ($groups as $key => $expected) {
            $result = $translationService->get($key, 'ar', 'dashboard', null);
            $status = ($result === $expected) ? '✓' : '✗';
            $this->line("{$status} {$key}: {$result}");
        }

        return 0;
    }
}
