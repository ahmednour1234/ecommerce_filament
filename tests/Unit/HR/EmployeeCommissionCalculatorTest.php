<?php

namespace Tests\Unit\HR;

use Tests\TestCase;
use App\Models\HR\Employee;
use App\Models\HR\CommissionType;
use App\Models\HR\Commission;
use App\Models\HR\EmployeeCommissionTier;
use App\Models\HR\EmployeeCommissionAssignment;
use App\Models\Recruitment\RecruitmentContract;
use App\Models\MainCore\Branch;
use App\Models\HR\Department;
use App\Models\HR\Position;
use App\Services\HR\EmployeeCommissionCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeCommissionCalculatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_calculates_commission_correctly(): void
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

        EmployeeCommissionTier::create([
            'commission_id' => $commission->id,
            'contracts_from' => 1,
            'contracts_to' => 10,
            'amount_per_contract' => 50.00,
            'is_active' => true,
        ]);

        EmployeeCommissionAssignment::create([
            'employee_id' => $employee->id,
            'commission_id' => $commission->id,
            'is_active' => true,
        ]);

        $dateFrom = now()->subDays(30)->format('Y-m-d');
        $dateTo = now()->format('Y-m-d');

        $calculator = new EmployeeCommissionCalculator();
        $result = $calculator->calculate($employee->id, $dateFrom, $dateTo);

        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('total_contracts', $result);
        $this->assertArrayHasKey('total_commission', $result);
    }
}
