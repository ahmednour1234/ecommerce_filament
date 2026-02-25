<?php

namespace Tests\Feature\HR;

use Tests\TestCase;
use App\Models\User;
use App\Models\HR\CommissionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CommissionTypeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'HR Manager', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'hr_commission_types.view_any', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'hr_commission_types.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'hr_commission_types.update', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'hr_commission_types.delete', 'guard_name' => 'web']);
        $role->givePermissionTo(['hr_commission_types.view_any', 'hr_commission_types.create', 'hr_commission_types.update', 'hr_commission_types.delete']);
        $this->user->assignRole($role);
        $this->actingAs($this->user);
    }

    public function test_can_create_commission_type(): void
    {
        $data = [
            'name_ar' => 'نوع عمولة تجريبي',
            'name_en' => 'Test Commission Type',
            'is_active' => true,
        ];

        $type = CommissionType::create($data);

        $this->assertDatabaseHas('commission_types', [
            'name_ar' => $data['name_ar'],
            'is_active' => true,
        ]);
    }

    public function test_can_soft_delete_commission_type(): void
    {
        $type = CommissionType::create([
            'name_ar' => 'Test Type',
            'is_active' => true,
        ]);

        $type->delete();

        $this->assertSoftDeleted('commission_types', ['id' => $type->id]);
    }

    public function test_can_restore_commission_type(): void
    {
        $type = CommissionType::create([
            'name_ar' => 'Test Type',
            'is_active' => true,
        ]);

        $type->delete();
        $type->restore();

        $this->assertDatabaseHas('commission_types', [
            'id' => $type->id,
            'deleted_at' => null,
        ]);
    }
}
