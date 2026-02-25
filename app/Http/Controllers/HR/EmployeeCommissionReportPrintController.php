<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Services\HR\EmployeeCommissionCalculator;
use Illuminate\Http\Request;

class EmployeeCommissionReportPrintController extends Controller
{
    public function __invoke(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if (!$employeeId || !$dateFrom || !$dateTo) {
            abort(400, 'Missing required parameters');
        }

        $employee = Employee::findOrFail($employeeId);

        $calculator = app(EmployeeCommissionCalculator::class);
        $result = $calculator->calculate($employeeId, $dateFrom, $dateTo);

        return view('filament.pages.hr.employee-commission-report-print', [
            'employee' => $result['employee'],
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'results' => $result['results'],
            'total_contracts' => $result['total_contracts'],
            'total_commission' => $result['total_commission'],
        ]);
    }
}
