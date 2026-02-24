<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ModuleRolesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating module roles...');

        $modules = [
            'hr' => [
                'name' => 'HR',
                'permissions' => $this->getHrPermissions(),
                'roles' => [
                    'HR Admin' => 'all',
                    'HR Officer' => 'view_create_update',
                    'HR Viewer' => 'view_only',
                ],
            ],
            'finance' => [
                'name' => 'Finance',
                'permissions' => $this->getFinancePermissions(),
                'roles' => [
                    'Finance Admin' => 'all',
                    'Finance Officer' => 'view_create_update',
                    'Finance Viewer' => 'view_only',
                ],
            ],
            'recruitment' => [
                'name' => 'Recruitment',
                'permissions' => $this->getRecruitmentPermissions(),
                'roles' => [
                    'Recruitment Admin' => 'all',
                    'Recruitment Officer' => 'view_create_update',
                    'Recruitment Viewer' => 'view_only',
                ],
            ],
            'housing' => [
                'name' => 'Housing',
                'permissions' => $this->getHousingPermissions(),
                'roles' => [
                    'Housing Admin' => 'all',
                    'Housing Officer' => 'view_create_update',
                    'Housing Viewer' => 'view_only',
                ],
            ],
            'rental' => [
                'name' => 'Rental',
                'permissions' => $this->getRentalPermissions(),
                'roles' => [
                    'Rental Admin' => 'all',
                    'Rental Officer' => 'view_create_update',
                    'Rental Viewer' => 'view_only',
                ],
            ],
            'service_transfer' => [
                'name' => 'Service Transfer',
                'permissions' => $this->getServiceTransferPermissions(),
                'roles' => [
                    'Service Transfer Admin' => 'all',
                    'Service Transfer Officer' => 'view_create_update',
                    'Service Transfer Viewer' => 'view_only',
                ],
            ],
            'clients' => [
                'name' => 'Clients',
                'permissions' => $this->getClientsPermissions(),
                'roles' => [
                    'Clients Admin' => 'all',
                    'Clients Officer' => 'view_create_update',
                    'Clients Viewer' => 'view_only',
                ],
            ],
            'company_visas' => [
                'name' => 'Company Visas',
                'permissions' => $this->getCompanyVisasPermissions(),
                'roles' => [
                    'Company Visas Admin' => 'all',
                    'Company Visas Officer' => 'view_create_update',
                    'Company Visas Viewer' => 'view_only',
                ],
            ],
            'follow_up' => [
                'name' => 'Follow-up',
                'permissions' => $this->getFollowUpPermissions(),
                'roles' => [
                    'Follow-up Admin' => 'all',
                    'Follow-up Officer' => 'view_create_update',
                    'Follow-up Viewer' => 'view_only',
                ],
            ],
            'settings' => [
                'name' => 'Settings',
                'permissions' => $this->getSettingsPermissions(),
                'roles' => [
                    'Settings Admin' => 'all',
                    'Settings Officer' => 'view_create_update',
                    'Settings Viewer' => 'view_only',
                ],
            ],
            'system_movement' => [
                'name' => 'System Movement',
                'permissions' => $this->getSystemMovementPermissions(),
                'roles' => [
                    'System Movement Admin' => 'all',
                    'System Movement Officer' => 'view_create_update',
                    'System Movement Viewer' => 'view_only',
                ],
            ],
        ];

        foreach ($modules as $moduleKey => $module) {
            $this->command->info("Creating roles for {$module['name']} module...");
            
            $permissions = $this->getOrCreatePermissions($module['permissions']);
            
            foreach ($module['roles'] as $roleName => $permissionLevel) {
                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);

                $rolePermissions = $this->filterPermissionsByLevel($permissions, $permissionLevel);
                $role->syncPermissions($rolePermissions);
                
                $this->command->info("  ✓ {$roleName} role created/updated with " . count($rolePermissions) . " permissions");
            }
        }

        $this->command->info('✓ Module roles created successfully!');
    }

    protected function getOrCreatePermissions(array $permissionNames): array
    {
        $permissions = [];
        foreach ($permissionNames as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
            $permissions[] = $permission;
        }
        return $permissions;
    }

    protected function filterPermissionsByLevel(array $permissions, string $level): array
    {
        return array_filter($permissions, function ($permission) use ($level) {
            $name = $permission->name;
            
            if ($level === 'all') {
                return true;
            }
            
            if ($level === 'view_create_update') {
                return str_contains($name, '.view') ||
                       str_contains($name, '.create') ||
                       str_contains($name, '.update') ||
                       str_contains($name, '.export') ||
                       str_contains($name, '.approve') ||
                       str_contains($name, '.reject');
            }
            
            if ($level === 'view_only') {
                return str_contains($name, '.view') ||
                       str_contains($name, '.export');
            }
            
            return false;
        });
    }

    protected function getHrPermissions(): array
    {
        return [
            'hr_departments.view_any',
            'hr_departments.view',
            'hr_departments.create',
            'hr_departments.update',
            'hr_departments.delete',
            'hr_positions.view_any',
            'hr_positions.view',
            'hr_positions.create',
            'hr_positions.update',
            'hr_positions.delete',
            'hr_employees.view_any',
            'hr_employees.view',
            'hr_employees.create',
            'hr_employees.update',
            'hr_employees.delete',
            'hr_work_places.view_any',
            'hr_work_places.view',
            'hr_work_places.create',
            'hr_work_places.update',
            'hr_work_places.delete',
            'hr_work_schedules.view_any',
            'hr_work_schedules.view',
            'hr_work_schedules.create',
            'hr_work_schedules.update',
            'hr_work_schedules.delete',
            'hr_attendance_daily.view',
            'hr_attendance_monthly.view',
            'hr_leave_requests.view_any',
            'hr_leave_requests.view',
            'hr_leave_requests.create',
            'hr_leave_requests.update',
            'hr_leave_requests.approve',
            'hr_leave_requests.reject',
            'hr_leave_requests.delete',
            'hr_loans.view_any',
            'hr_loans.view',
            'hr_loans.create',
            'hr_loans.update',
            'hr_loans.delete',
            'hr_payroll.view_any',
            'hr_payroll.view',
            'hr_payroll.create',
            'hr_payroll.update',
            'hr_payroll.delete',
        ];
    }

    protected function getFinancePermissions(): array
    {
        return [
            'finance_types.view_any',
            'finance_types.view',
            'finance_types.create',
            'finance_types.update',
            'finance_types.delete',
            'finance.view_any',
            'finance.view',
            'finance.create',
            'finance.update',
            'finance.delete',
            'finance_reports.view',
            'finance_reports.export',
        ];
    }

    protected function getRecruitmentPermissions(): array
    {
        return [
            'recruitment_contracts.view_any',
            'recruitment_contracts.view',
            'recruitment_contracts.create',
            'recruitment_contracts.update',
            'recruitment_contracts.delete',
            'agents.view_any',
            'agents.view',
            'agents.create',
            'agents.update',
            'agents.delete',
            'laborers.view_any',
            'laborers.view',
            'laborers.create',
            'laborers.update',
            'laborers.delete',
            'packages.view_any',
            'packages.view',
            'packages.create',
            'packages.update',
            'packages.delete',
        ];
    }

    protected function getHousingPermissions(): array
    {
        return [
            'housing.statuses.view_any',
            'housing.statuses.view',
            'housing.statuses.create',
            'housing.statuses.update',
            'housing.statuses.delete',
            'housing.buildings.view_any',
            'housing.buildings.view',
            'housing.buildings.create',
            'housing.buildings.update',
            'housing.buildings.delete',
            'housing.requests.view_any',
            'housing.requests.view',
            'housing.requests.create',
            'housing.requests.update',
            'housing.requests.delete',
            'housing.accommodation_entries.create',
        ];
    }

    protected function getRentalPermissions(): array
    {
        return [
            'rental_contracts.view_any',
            'rental_contracts.view',
            'rental_contracts.create',
            'rental_contracts.update',
            'rental_contracts.delete',
            'rental_requests.view_any',
            'rental_requests.view',
            'rental_requests.create',
            'rental_requests.update',
            'rental_requests.delete',
            'rental_reports.view',
            'rental_reports.export',
        ];
    }

    protected function getServiceTransferPermissions(): array
    {
        return [
            'service_transfer.view_any',
            'service_transfer.view',
            'service_transfer.create',
            'service_transfer.update',
            'service_transfer.delete',
            'service_transfer_reports.view',
            'service_transfer_reports.export',
        ];
    }

    protected function getClientsPermissions(): array
    {
        return [
            'clients.view_any',
            'clients.view',
            'clients.create',
            'clients.update',
            'clients.delete',
        ];
    }

    protected function getCompanyVisasPermissions(): array
    {
        return [
            'company_visa_requests.view_any',
            'company_visa_requests.view',
            'company_visa_requests.create',
            'company_visa_requests.update',
            'company_visa_requests.delete',
            'company_visa_contracts.view_any',
            'company_visa_contracts.view',
            'company_visa_contracts.create',
            'company_visa_contracts.update',
            'company_visa_contracts.delete',
        ];
    }

    protected function getFollowUpPermissions(): array
    {
        return [
            'complaints.view_any',
            'complaints.view',
            'complaints.create',
            'complaints.update',
            'complaints.delete',
        ];
    }

    protected function getSettingsPermissions(): array
    {
        return [
            'settings.view',
            'settings.update',
            'languages.view_any',
            'languages.view',
            'languages.create',
            'languages.update',
            'languages.delete',
            'currencies.view_any',
            'currencies.view',
            'currencies.create',
            'currencies.update',
            'currencies.delete',
            'translations.view_any',
            'translations.view',
            'translations.create',
            'translations.update',
            'translations.delete',
            'branches.view_any',
            'branches.view',
            'branches.create',
            'branches.update',
            'branches.delete',
        ];
    }

    protected function getSystemMovementPermissions(): array
    {
        return [
            'users.view_any',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.view_any',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'permissions.view_any',
            'permissions.view',
        ];
    }
}
