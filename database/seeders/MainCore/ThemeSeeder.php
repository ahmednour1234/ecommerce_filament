<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        Theme::updateOrCreate(
            ['name' => 'Default'],
            [
                'primary_color'   => '#4F46E5',
                'secondary_color' => '#0EA5E9',
                'accent_color'    => '#22C55E',
                'logo_light'      => null,
                'logo_dark'       => null,
                'is_default'      => true,
            ]
        );
    }
}
