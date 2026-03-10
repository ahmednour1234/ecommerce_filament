<?php

namespace Database\Seeders;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (! $english || ! $arabic) {
            $this->command->warn('English or Arabic language not found. Skipping translations.');
            return;
        }

        $translations = [
            'recruitment_contract.fields.status_duration' => ['en' => 'Duration from previous', 'ar' => 'المدة من السابقة'],
            'recruitment_contract.fields.status_date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'recruitment_contract.days' => ['en' => 'days', 'ar' => 'أيام'],
            'forms.users.type' => ['en' => 'User type', 'ar' => 'نوع المستخدم'],
            'tables.users.type' => ['en' => 'User type', 'ar' => 'نوع المستخدم'],
        ];

        $group = 'dashboard';

        foreach ($translations as $key => $values) {
            Translation::updateOrCreate(
                ['key' => $key, 'group' => $group, 'language_id' => $english->id],
                ['value' => $values['en']]
            );
            Translation::updateOrCreate(
                ['key' => $key, 'group' => $group, 'language_id' => $arabic->id],
                ['value' => $values['ar']]
            );
        }

        $this->command->info('Translation seeder finished.');
    }
}
