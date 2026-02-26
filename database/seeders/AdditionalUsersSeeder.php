<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdditionalUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if (!$superAdminRole) {
            $this->command->warn('Super admin role not found. Please run SuperAdminSeeder first.');
            return;
        }

        $users = [
            [
                'name' => 'محمد مرعي',
                'email' => 'mohammed.marai@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'فيصل العنزي',
                'email' => 'faisal.alanezi@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'سالم الرويلي',
                'email' => 'salem.alruwaili@example.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                ]
            );

            if (!$user->hasRole('super_admin')) {
                $user->assignRole('super_admin');
                $this->command->info("✓ Created/updated user: {$userData['name']} ({$userData['email']}) with super_admin role");
            } else {
                $this->command->info("✓ User {$userData['name']} ({$userData['email']}) already has super_admin role");
            }
        }

        $this->command->info('✓ Additional users seeder completed');
    }
}
