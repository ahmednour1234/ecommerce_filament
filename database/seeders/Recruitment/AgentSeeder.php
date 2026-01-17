<?php

namespace Database\Seeders\Recruitment;

use App\Models\MainCore\Country;
use App\Models\Recruitment\Agent;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Agents...');

        $country = Country::where('is_active', true)->first();

        if (!$country) {
            $this->command->warn('No active country found. Please seed Countries first.');
            return;
        }

        $agents = [
            [
                'code' => 'AGT001',
                'name_ar' => 'وكالة التوظيف الأولى',
                'name_en' => 'First Recruitment Agency',
                'email' => 'agent1@example.com',
                'country_id' => $country->id,
                'city_ar' => 'القاهرة',
                'city_en' => 'Cairo',
                'address_ar' => 'شارع التحرير، القاهرة',
                'address_en' => 'Tahrir Street, Cairo',
                'license_number' => 'LIC-001-2024',
                'phone_1' => '+201234567890',
                'phone_2' => '+201098765432',
                'mobile' => '+201112345678',
                'fax' => '+201223456789',
                'responsible_name' => 'أحمد محمد',
                'passport_number' => 'P123456',
                'passport_issue_date' => '2020-01-15',
                'passport_issue_place' => 'Cairo',
                'bank_sender' => 'National Bank',
                'account_number' => 'ACC-001-123456',
                'username' => 'agent001',
                'notes' => 'Main recruitment agency with good reputation.',
            ],
            [
                'code' => 'AGT002',
                'name_ar' => 'وكالة التوظيف الثانية',
                'name_en' => 'Second Recruitment Agency',
                'email' => 'agent2@example.com',
                'country_id' => $country->id,
                'city_ar' => 'الإسكندرية',
                'city_en' => 'Alexandria',
                'address_ar' => 'شارع البحر، الإسكندرية',
                'address_en' => 'Sea Street, Alexandria',
                'license_number' => 'LIC-002-2024',
                'phone_1' => '+201234567891',
                'phone_2' => '+201098765433',
                'mobile' => '+201112345679',
                'responsible_name' => 'فاطمة أحمد',
                'passport_number' => 'P234567',
                'passport_issue_date' => '2019-06-10',
                'passport_issue_place' => 'Alexandria',
                'bank_sender' => 'Commercial Bank',
                'account_number' => 'ACC-002-234567',
                'username' => 'agent002',
                'notes' => 'Specialized in skilled workers.',
            ],
            [
                'code' => 'AGT003',
                'name_ar' => 'وكالة التوظيف الثالثة',
                'name_en' => 'Third Recruitment Agency',
                'email' => 'agent3@example.com',
                'country_id' => $country->id,
                'city_ar' => 'الجيزة',
                'city_en' => 'Giza',
                'address_ar' => 'شارع الهرم، الجيزة',
                'address_en' => 'Pyramid Street, Giza',
                'license_number' => 'LIC-003-2024',
                'phone_1' => '+201234567892',
                'mobile' => '+201112345680',
                'responsible_name' => 'محمد خالد',
                'passport_number' => 'P345678',
                'passport_issue_date' => '2021-03-20',
                'passport_issue_place' => 'Giza',
                'bank_sender' => 'Investment Bank',
                'account_number' => 'ACC-003-345678',
                'username' => 'agent003',
                'notes' => 'Focus on professional services.',
            ],
        ];

        $created = 0;
        foreach ($agents as $agent) {
            Agent::updateOrCreate(
                ['code' => $agent['code']],
                $agent
            );
            $created++;
        }

        $this->command->info("✓ Agents seeded: {$created}");
    }
}
