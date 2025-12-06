<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Setting;
use App\Models\MainCore\Currency;
use App\Models\MainCore\Language;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaultCurrency = Currency::where('is_default', true)->first();
        $defaultLanguage = Language::where('is_default', true)->first();

        $settings = [
            [
                'key'   => 'app.name',
                'value' => 'MainCore Dashboard',
                'group' => 'app',
                'type'  => 'string',
            ],
            [
                'key'   => 'app.timezone',
                'value' => 'Africa/Cairo',
                'group' => 'app',
                'type'  => 'string',
            ],
            [
                'key'   => 'app.default_language',
                'value' => $defaultLanguage?->code,
                'group' => 'app',
                'type'  => 'string',
            ],
            [
                'key'   => 'app.default_currency',
                'value' => $defaultCurrency?->code,
                'group' => 'app',
                'type'  => 'string',
            ],
        ];

        foreach ($settings as $data) {
            Setting::updateOrCreate(
                ['key' => $data['key']],
                [
                    'value'     => $data['value'],
                    'group'     => $data['group'],
                    'type'      => $data['type'],
                    'is_public' => true,
                    'autoload'  => true,
                ]
            );
        }
    }
}
