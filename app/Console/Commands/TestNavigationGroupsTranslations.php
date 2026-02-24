<?php

namespace App\Console\Commands;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use App\Services\MainCore\TranslationService;
use Illuminate\Console\Command;

class TestNavigationGroupsTranslations extends Command
{
    protected $signature = 'test:nav-groups-translations';
    protected $description = 'Test navigation groups translations';

    public function handle()
    {
        $this->info('Testing Navigation Groups Translations...');
        $this->newLine();

        $groups = [
            'app_management' => 'إدارة التطبيق',
            'notifications' => 'التنبيهات',
            'clients' => 'العملاء',
            'company_visas' => 'تأشيرات الشركة',
            'follow_up' => 'المتابعة',
            'finance' => 'قسم الحسابات',
            'hr' => 'الموارد البشرية',
            'housing' => 'الإيواء',
            'branches' => 'الفروع',
            'settings' => 'الإعدادات',
            'packages' => 'باقات العروض',
            'agents' => 'الوكلاء',
            'candidates' => 'المرشحين',
            'recruitment_contracts' => 'عقود الاستقدام',
            'rental' => 'قسم التأجير',
            'service_transfer' => 'نقل الخدمات',
            'system_movement' => 'حركة النظام المرجعي',
        ];

        $translationService = app(TranslationService::class);
        $arabic = Language::where('code', 'ar')->first();
        $english = Language::where('code', 'en')->first();

        if (!$arabic || !$english) {
            $this->error('Arabic or English language not found in database!');
            $this->info('Run: php artisan db:seed --class="Database\Seeders\MainCore\SidebarNavigationTranslationsSeeder"');
            return 1;
        }

        $this->table(
            ['Group Key', 'Arabic Translation', 'English Translation', 'Status'],
            collect($groups)->map(function ($expectedAr, $key) use ($translationService, $arabic, $english) {
                $keyWithPrefix = "sidebar.{$key}";
                
                // Check Arabic
                $arTranslation = Translation::where('key', $keyWithPrefix)
                    ->where('group', 'dashboard')
                    ->where('language_id', $arabic->id)
                    ->first();
                
                $arValue = $arTranslation ? $arTranslation->value : 'NOT FOUND';
                $arStatus = $arValue === $expectedAr ? '✓' : '✗';
                
                // Check English
                $enTranslation = Translation::where('key', $keyWithPrefix)
                    ->where('group', 'dashboard')
                    ->where('language_id', $english->id)
                    ->first();
                
                $enValue = $enTranslation ? $enTranslation->value : 'NOT FOUND';
                
                // Test service
                $serviceAr = $translationService->get($keyWithPrefix, 'ar', 'dashboard', null);
                $serviceStatus = ($serviceAr && $serviceAr !== $keyWithPrefix) ? '✓' : '✗';
                
                return [
                    $key,
                    $arValue . ' ' . $arStatus,
                    $enValue,
                    $serviceStatus,
                ];
            })->toArray()
        );

        $this->newLine();
        $this->info('Testing Arabic translations via TranslationService...');
        
        foreach ($groups as $key => $expectedAr) {
            $keyWithPrefix = "sidebar.{$key}";
            $result = $translationService->get($keyWithPrefix, 'ar', 'dashboard', null);
            
            if ($result && $result !== $keyWithPrefix && $result === $expectedAr) {
                $this->line("✓ {$key}: {$result}");
            } else {
                $this->error("✗ {$key}: " . ($result ?: 'NOT FOUND'));
            }
        }

        $this->newLine();
        $this->info('If translations are missing, run:');
        $this->line('php artisan db:seed --class="Database\Seeders\MainCore\SidebarNavigationTranslationsSeeder"');
        
        return 0;
    }
}
