<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\NotificationChannel;
use App\Models\MainCore\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $emailChannel = NotificationChannel::updateOrCreate(
            ['type' => 'email', 'name' => 'Default SMTP'],
            [
                'config' => [
                    'mailer' => 'smtp',
                    // ممكن تربطها بإعدادات .env
                ],
                'is_active' => true,
            ]
        );

        NotificationTemplate::updateOrCreate(
            ['key' => 'user.welcome', 'channel_id' => $emailChannel->id, 'language_id' => null],
            [
                'subject'   => 'Welcome to MainCore',
                'body_text' => 'Hello {user_name}, welcome to our platform.',
                'body_html' => '<p>Hello {user_name}, welcome to our platform.</p>',
                'variables' => ['{user_name}'],
                'is_active' => true,
            ]
        );
    }
}
