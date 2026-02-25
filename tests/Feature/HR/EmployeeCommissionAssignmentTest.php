<?php

namespace Tests\Feature\HR;

use Tests\TestCase;
use App\Models\User;
use App\Models\HR\Employee;
use App\Models\HR\CommissionType;
use App\Models\HR\Commission;
use App\Models\HR\EmployeeCommissionAssignment;
use App\Models\MainCore\Branch;
use App\Models\HR\Department;
use App\Models\HR\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeCommissionAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'HR Manager', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'hr_employee_commission_assignments.view_any', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'hr_employee_commission_assignments.create', 'guard_name' => 'web']);
        $role->givePermissionTo(['hr_employee_commission_assignments.view_any', 'hr_employee_commission_assignments.create']);
        $this->user->assignRole($role);
        $this->actingAs($this->user);
    }

    public function test_can_create_assignment(): void
    {
        $branch = Branch::factory()->create();
        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);

        $employee = Employee::create([
            'employee_number' => 'EMP001',
            'first_name' => 'Test',
            'last_name' => 'Employee',
            'gender' => 'male',
            'hire_date' => now(),
            'branch_id' => $branch->id,
            'department_id' => $department->id,
            'position_id' => $position->id,
            'basic_salary' => 5000.00,
            'status' => 'active',
        ]);

        $type = CommissionType::create(['name_ar' => 'نوع', 'is_active' => true]);
        $commission = Commission::create([
            'name_ar' => 'عمولة',
            'commission_type_id' => $type->id,
            'value' => 100.00,
            'is_active' => true,
        ]);

        $assignment = EmployeeCommissionAssignment::create([
            'employee_id' => $employee->id,
            'commission_id' => $commission->id,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('employee_commission_assignments', [
            'employee_id' => $employee->id,
            'commission_id' => $commission->id,
        ]);
    }
}
