<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Language::updateOrCreate(
            ['code' => 'en'],
            [
                'name'        => 'English',
                'native_name' => 'English',
                'direction'   => 'ltr',
                'is_default'  => true,
                'is_active'   => true,
            ]
        );

        Language::updateOrCreate(
            ['code' => 'ar'],
            [
                'name'        => 'Arabic',
                'native_name' => 'العربية',
                'direction'   => 'rtl',
                'is_default'  => false,
                'is_active'   => true,
            ]
        );
    }
}
