<?php

namespace Database\Seeders\Recruitment;

use App\Models\MainCore\Country;
use App\Models\Recruitment\Agent;
use App\Models\Recruitment\Nationality;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Agents...');

        // Map nationality name_ar => list of agents
        $agentsByNationality = [
            'سريلانكا' => [
                ['code' => 'AGT-LK-001', 'name_en' => 'R.K.N.FOREIGN EMPLOYMENT AGENCY', 'name_ar' => 'R.K.N.FOREIGN EMPLOYMENT AGENCY'],
                ['code' => 'AGT-LK-002', 'name_en' => 'EMPIRE RECRUITMENT', 'name_ar' => 'EMPIRE RECRUITMENT'],
                ['code' => 'AGT-LK-003', 'name_en' => 'MUTHALIB ENTERPRISES', 'name_ar' => 'MUTHALIB ENTERPRISES'],
                ['code' => 'AGT-LK-004', 'name_en' => 'WORLD AIR FOREIGN EMPLOYMENT AGENCY', 'name_ar' => 'WORLD AIR FOREIGN EMPLOYMENT AGENCY'],
                ['code' => 'AGT-LK-005', 'name_en' => 'THE NATION RECRUITMENTS', 'name_ar' => 'THE NATION RECRUITMENTS'],
            ],
            'اثيوبيا' => [
                ['code' => 'AGT-ET-001', 'name_en' => 'ABI MIFTAH PRIVATE FORIEGN EMPLOYMENT AGENCY', 'name_ar' => 'ABI MIFTAH PRIVATE FORIEGN EMPLOYMENT AGENCY'],
                ['code' => 'AGT-ET-002', 'name_en' => 'ALMAMORA FOREIGN EMPLOYMENT AGNT PLC', 'name_ar' => 'ALMAMORA FOREIGN EMPLOYMENT AGNT PLC'],
                ['code' => 'AGT-ET-003', 'name_en' => 'ABURIJAL PLC', 'name_ar' => 'ABURIJAL PLC'],
                ['code' => 'AGT-ET-004', 'name_en' => 'GOLDEN SEASON FORIGN EMPLOYMENT PLC', 'name_ar' => 'GOLDEN SEASON FORIGN EMPLOYMENT PLC'],
                ['code' => 'AGT-ET-005', 'name_en' => 'SABOLA FOREIGN EMPLOYMENT AGENCY', 'name_ar' => 'SABOLA FOREIGN EMPLOYMENT AGENCY'],
            ],
            'الفلبين' => [
                ['code' => 'AGT-PH-001', 'name_en' => 'INCORPORATED SERVICES DEVELOPMENT', 'name_ar' => 'INCORPORATED SERVICES DEVELOPMENT'],
                ['code' => 'AGT-PH-002', 'name_en' => 'LEILA INTERNATIONAL SERVICES INC', 'name_ar' => 'LEILA INTERNATIONAL SERVICES INC'],
            ],
            'بنغلادش' => [
                ['code' => 'AGT-BD-001', 'name_en' => 'M/s National Recruiting Agency', 'name_ar' => 'M/s National Recruiting Agency'],
                ['code' => 'AGT-BD-002', 'name_en' => 'Rafiq and sons International', 'name_ar' => 'Rafiq and sons International'],
                ['code' => 'AGT-BD-003', 'name_en' => 'Ranger International', 'name_ar' => 'Ranger International'],
                ['code' => 'AGT-BD-004', 'name_en' => '112A/CENTRAL OVERSEASE', 'name_ar' => '112A/CENTRAL OVERSEASE'],
            ],
            'اوغندا' => [
                ['code' => 'AGT-UG-001', 'name_en' => 'TERRYSOME INVESTMENTS LIMITED', 'name_ar' => 'TERRYSOME INVESTMENTS LIMITED'],
                ['code' => 'AGT-UG-002', 'name_en' => 'KRYSTAL RECRUITERS (U) LTD', 'name_ar' => 'KRYSTAL RECRUITERS (U) LTD'],
            ],
            'كينيا' => [
                ['code' => 'AGT-KE-001', 'name_en' => 'GLOBAL DREAM AGENCY LIMITED', 'name_ar' => 'GLOBAL DREAM AGENCY LIMITED'],
                ['code' => 'AGT-KE-002', 'name_en' => 'JUEFELENT AGENCY LIMITED', 'name_ar' => 'JUEFELENT AGENCY LIMITED'],
                ['code' => 'AGT-KE-003', 'name_en' => 'MAJORDOMO AGENCIES LIMITED', 'name_ar' => 'MAJORDOMO AGENCIES LIMITED'],
            ],
            'بورندي' => [
                ['code' => 'AGT-BI-001', 'name_en' => 'TARGET MANPOWER COMPANY S.U.R.L', 'name_ar' => 'TARGET MANPOWER COMPANY S.U.R.L'],
            ],
        ];

        $created = 0;

        foreach ($agentsByNationality as $nationalityName => $agents) {
            $nationality = Nationality::where('name_ar', $nationalityName)->first();

            if (!$nationality) {
                $this->command->warn("Nationality '{$nationalityName}' not found. Skipping its agents.");
                continue;
            }

            foreach ($agents as $agentData) {
                Agent::updateOrCreate(
                    ['code' => $agentData['code']],
                    [
                        'code' => $agentData['code'],
                        'name_ar' => $agentData['name_ar'],
                        'name_en' => $agentData['name_en'],
                        'nationality_id' => $nationality->id,
                        'country_id' => $nationality->id,
                        'phone_1' => '-',
                    ]
                );
                $created++;
            }
        }

        $this->command->info("✓ Agents seeded: {$created}");
    }
}
