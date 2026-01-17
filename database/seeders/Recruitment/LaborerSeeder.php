<?php

namespace Database\Seeders\Recruitment;

use App\Models\MainCore\Currency;
use App\Models\Recruitment\Agent;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Profession;
use Illuminate\Database\Seeder;

class LaborerSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Laborers...');

        $defaultCurrency = Currency::where('is_default', true)->first() 
            ?? Currency::where('is_active', true)->first();

        if (!$defaultCurrency) {
            $this->command->warn('No currency found. Please seed currencies first.');
            return;
        }

        $agent = Agent::first();
        $nationality = Nationality::where('is_active', true)->first();
        $profession = Profession::where('is_active', true)->first();
        $country = \App\Models\MainCore\Country::where('is_active', true)->first();

        if (!$agent || !$nationality || !$profession || !$country) {
            $this->command->warn('Required related records not found. Please seed Agents, Nationalities, Professions, and Countries first.');
            return;
        }

        $laborers = [
            [
                'name_ar' => 'أحمد محمد علي',
                'name_en' => 'Ahmed Mohammed Ali',
                'passport_number' => 'A12345678',
                'passport_issue_place' => 'Cairo',
                'passport_issue_date' => '2020-01-15',
                'passport_expiry_date' => '2025-01-15',
                'birth_date' => '1990-05-20',
                'gender' => 'male',
                'nationality_id' => $nationality->id,
                'profession_id' => $profession->id,
                'experience_level' => 'Senior',
                'social_status' => 'Married',
                'address' => '123 Main Street, Cairo, Egypt',
                'relative_name' => 'Mohammed Ali',
                'phone_1' => '+201234567890',
                'phone_2' => '+201098765432',
                'agent_id' => $agent->id,
                'country_id' => $country->id,
                'height' => 175.50,
                'weight' => 75.00,
                'speaks_arabic' => true,
                'speaks_english' => true,
                'monthly_salary_amount' => 5000.00,
                'monthly_salary_currency_id' => $defaultCurrency->id,
                'is_available' => true,
                'show_on_website' => true,
                'notes' => 'Experienced worker with good communication skills.',
            ],
            [
                'name_ar' => 'فاطمة أحمد حسن',
                'name_en' => 'Fatima Ahmed Hassan',
                'passport_number' => 'B87654321',
                'passport_issue_place' => 'Alexandria',
                'passport_issue_date' => '2019-06-10',
                'passport_expiry_date' => '2024-06-10',
                'birth_date' => '1992-08-15',
                'gender' => 'female',
                'nationality_id' => $nationality->id,
                'profession_id' => $profession->id,
                'experience_level' => 'Intermediate',
                'social_status' => 'Single',
                'address' => '456 Second Street, Alexandria, Egypt',
                'relative_name' => 'Ahmed Hassan',
                'phone_1' => '+201112345678',
                'agent_id' => $agent->id,
                'country_id' => $country->id,
                'height' => 165.00,
                'weight' => 60.00,
                'speaks_arabic' => true,
                'speaks_english' => false,
                'monthly_salary_amount' => 4000.00,
                'monthly_salary_currency_id' => $defaultCurrency->id,
                'is_available' => true,
                'show_on_website' => false,
                'notes' => 'Hardworking and reliable.',
            ],
            [
                'name_ar' => 'محمد خالد إبراهيم',
                'name_en' => 'Mohammed Khaled Ibrahim',
                'passport_number' => 'C11223344',
                'passport_issue_place' => 'Giza',
                'passport_issue_date' => '2021-03-20',
                'passport_expiry_date' => '2026-03-20',
                'birth_date' => '1988-12-10',
                'gender' => 'male',
                'nationality_id' => $nationality->id,
                'profession_id' => $profession->id,
                'experience_level' => 'Expert',
                'social_status' => 'Married',
                'address' => '789 Third Avenue, Giza, Egypt',
                'relative_name' => 'Khaled Ibrahim',
                'phone_1' => '+201223456789',
                'phone_2' => '+201334567890',
                'agent_id' => $agent->id,
                'country_id' => $country->id,
                'height' => 180.00,
                'weight' => 80.00,
                'speaks_arabic' => true,
                'speaks_english' => true,
                'monthly_salary_amount' => 6000.00,
                'monthly_salary_currency_id' => $defaultCurrency->id,
                'is_available' => true,
                'show_on_website' => true,
                'notes' => 'Highly skilled professional with extensive experience.',
            ],
        ];

        $created = 0;
        foreach ($laborers as $laborer) {
            Laborer::updateOrCreate(
                ['passport_number' => $laborer['passport_number']],
                $laborer
            );
            $created++;
        }

        $this->command->info("✓ Laborers seeded: {$created}");
    }
}
