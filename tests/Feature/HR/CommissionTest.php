<?php

namespace Tests\Feature\HR;

use Tests\TestCase;
use App\Models\User;
use App\Models\HR\CommissionType;
use App\Models\HR\Commission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CommissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'HR Manager', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'hr_commissions.view_any', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'hr_commissions.create', 'guard_name' => 'web']);
        $role->givePermissionTo(['hr_commissions.view_any', 'hr_commissions.create']);
        $this->user->assignRole($role);
        $this->actingAs($this->user);
    }

    public function test_can_create_commission(): void
    {
        $type = CommissionType::create([
            'name_ar' => 'نوع تجريبي',
            'is_active' => true,
        ]);

        $commission = Commission::create([
            'name_ar' => 'عمولة تجريبية',
            'commission_type_id' => $type->id,
            'value' => 100.00,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('commissions', [
            'name_ar' => 'عمولة تجريبية',
            'commission_type_id' => $type->id,
        ]);
    }

    public function test_commission_has_commission_type_relationship(): void
    {
        $type = CommissionType::create([
            'name_ar' => 'نوع تجريبي',
            'is_active' => true,
        ]);

        $commission = Commission::create([
            'name_ar' => 'عمولة تجريبية',
            'commission_type_id' => $type->id,
            'value' => 100.00,
            'is_active' => true,
        ]);

        $this->assertEquals($type->id, $commission->commissionType->id);
    }
}
