<?php

namespace Database\Seeders\Messaging;

use App\Models\MainCore\SmsSetting;
use Illuminate\Database\Seeder;

class SmsSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'current_balance' => '0',
            'sender_name' => '',
            'daily_limit' => '500',
            'is_sending_enabled' => 'true',
        ];

        foreach ($defaults as $key => $value) {
            SmsSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->command->info('✓ SMS settings seeded');
    }
}
