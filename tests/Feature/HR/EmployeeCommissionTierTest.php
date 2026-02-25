<?php

namespace Tests\Feature\HR;

use Tests\TestCase;
use App\Models\User;
use App\Models\HR\CommissionType;
use App\Models\HR\Commission;
use App\Models\HR\EmployeeCommissionTier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeCommissionTierTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'HR Manager', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'hr_employee_commission_tiers.view_any', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'hr_employee_commission_tiers.create', 'guard_name' => 'web']);
        $role->givePermissionTo(['hr_employee_commission_tiers.view_any', 'hr_employee_commission_tiers.create']);
        $this->user->assignRole($role);
        $this->actingAs($this->user);
    }

    public function test_can_create_tier(): void
    {
        $type = CommissionType::create(['name_ar' => 'نوع', 'is_active' => true]);
        $commission = Commission::create([
            'name_ar' => 'عمولة',
            'commission_type_id' => $type->id,
            'value' => 100.00,
            'is_active' => true,
        ]);

        $tier = EmployeeCommissionTier::create([
            'commission_id' => $commission->id,
            'contracts_from' => 1,
            'contracts_to' => 10,
            'amount_per_contract' => 50.00,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('employee_commission_tiers', [
            'commission_id' => $commission->id,
            'contracts_from' => 1,
            'contracts_to' => 10,
        ]);
    }

    public function test_tier_matches_contract_count(): void
    {
        $type = CommissionType::create(['name_ar' => 'نوع', 'is_active' => true]);
        $commission = Commission::create([
            'name_ar' => 'عمولة',
            'commission_type_id' => $type->id,
            'value' => 100.00,
            'is_active' => true,
        ]);

        $tier = EmployeeCommissionTier::create([
            'commission_id' => $commission->id,
            'contracts_from' => 5,
            'contracts_to' => 15,
            'amount_per_contract' => 50.00,
            'is_active' => true,
        ]);

        $this->assertTrue($tier->matchesContractCount(10));
        $this->assertFalse($tier->matchesContractCount(20));
    }
}
