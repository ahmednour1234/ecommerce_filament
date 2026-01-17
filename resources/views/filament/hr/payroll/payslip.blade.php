<x-filament::page>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .payslip-shell { padding: 0 !important; }
            .payslip-card { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
        }

        .payslip-shell { 
            max-width: 900px; 
            margin: 0 auto; 
            direction: rtl;
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
        }

        [dir="ltr"] .payslip-shell { direction: ltr; }

        .payslip-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .payslip-header {
            padding: 25px;
            background: linear-gradient(135deg, #0f172a, #1f2937);
            color: #fff;
        }

        .payslip-title {
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 5px 0;
        }

        .payslip-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .payslip-body { padding: 25px; }

        .employee-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 12px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .payslip-sections {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .section-card {
            background: #fafbfc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-table {
            width: 100%;
            border-collapse: collapse;
        }

        .section-table th {
            text-align: right;
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        [dir="ltr"] .section-table th { text-align: left; }

        .section-table td {
            padding: 10px 0;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-table tr:last-child td {
            border-bottom: none;
        }

        .total-row {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .total-value {
            font-size: 20px;
            font-weight: 800;
            color: #059669;
        }

        .net-salary {
            background: linear-gradient(135deg, #059669, #10b981);
            color: #fff;
            padding: 20px;
            border-radius: 12px;
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .net-salary-label {
            font-size: 18px;
            font-weight: 700;
        }

        .net-salary-value {
            font-size: 28px;
            font-weight: 800;
        }

        .no-print {
            margin-bottom: 20px;
        }
    </style>

    <div class="payslip-shell">
        <div class="no-print">
            <x-filament::button wire:click="$dispatch('print')" onclick="window.print()" color="primary">
                {{ trans_dash('actions.print_payslip') ?: 'Print Payslip' }}
            </x-filament::button>
        </div>

        <div class="payslip-card">
            <div class="payslip-header">
                <h1 class="payslip-title">{{ trans_dash('actions.print_payslip') ?: 'Payslip' }}</h1>
                <div class="payslip-subtitle">
                    {{ trans_dash('tables.hr_payroll.period') ?: 'Period' }}: {{ $record->payrollRun->period ?? '' }}
                </div>
            </div>

            <div class="payslip-body">
                @php
                    $employee = $record->employee ?? null;
                    $earnings = $record->earningsLines ?? collect();
                    $deductions = $record->deductionsLines ?? collect();
                @endphp

                @if($employee)
                    <div class="employee-info">
                        <div class="info-item">
                            <div class="info-label">{{ trans_dash('tables.hr_payroll.employee_number') ?: 'Employee Number' }}</div>
                            <div class="info-value">{{ $employee->employee_number }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">{{ trans_dash('tables.hr_payroll.name') ?: 'Name' }}</div>
                            <div class="info-value">{{ $employee->full_name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">{{ trans_dash('tables.hr_payroll.department') ?: 'Department' }}</div>
                            <div class="info-value">{{ $employee->department->name ?? '' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">{{ trans_dash('tables.hr_payroll.status') ?: 'Status' }}</div>
                            <div class="info-value">{{ trans_dash("status.{$record->status}") ?: ucfirst($record->status) }}</div>
                        </div>
                    </div>
                @endif

                <div class="payslip-sections">
                    <div class="section-card">
                        <div class="section-title">{{ trans_dash('tables.salary_components.earnings') ?: 'Earnings' }}</div>
                        <table class="section-table">
                            <thead>
                                <tr>
                                    <th>{{ trans_dash('tables.salary_components.name') ?: 'Component' }}</th>
                                    <th style="text-align: left;">{{ trans_dash('forms.employee_financial_profiles.amount') ?: 'Amount' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($earnings as $line)
                                    <tr>
                                        <td>{{ $line->component->name ?? '' }}</td>
                                        <td style="text-align: left; font-weight: 600;">${{ number_format($line->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" style="text-align: center; padding: 10px; color: #94a3b8;">
                                            {{ trans_dash('common.no_records') ?: 'No earnings' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="total-row">
                            <span class="total-label">{{ trans_dash('tables.hr_payroll.total_earnings') ?: 'Total Earnings' }}</span>
                            <span class="total-value">${{ number_format($record->total_earnings ?? 0, 2) }}</span>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-title">{{ trans_dash('tables.salary_components.deductions') ?: 'Deductions' }}</div>
                        <table class="section-table">
                            <thead>
                                <tr>
                                    <th>{{ trans_dash('tables.salary_components.name') ?: 'Component' }}</th>
                                    <th style="text-align: left;">{{ trans_dash('forms.employee_financial_profiles.amount') ?: 'Amount' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deductions as $line)
                                    <tr>
                                        <td>{{ $line->component->name ?? '' }}</td>
                                        <td style="text-align: left; font-weight: 600;">${{ number_format($line->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" style="text-align: center; padding: 10px; color: #94a3b8;">
                                            {{ trans_dash('common.no_records') ?: 'No deductions' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="total-row">
                            <span class="total-label">{{ trans_dash('tables.hr_payroll.total_deductions') ?: 'Total Deductions' }}</span>
                            <span class="total-value" style="color: #dc2626;">${{ number_format($record->total_deductions ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="net-salary">
                    <span class="net-salary-label">{{ trans_dash('tables.hr_payroll.net_salary') ?: 'Net Salary' }}</span>
                    <span class="net-salary-value">${{ number_format($record->net_salary ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
