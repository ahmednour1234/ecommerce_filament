<x-filament::page>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .payroll-shell { padding: 0 !important; }
            .payroll-card { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
        }

        .payroll-shell { 
            max-width: 1200px; 
            margin: 0 auto; 
            direction: rtl;
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
        }

        [dir="ltr"] .payroll-shell { direction: ltr; }

        .payroll-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            margin-bottom: 20px;
        }

        .payroll-header {
            padding: 20px;
            background: linear-gradient(135deg, #0f172a, #1f2937);
            color: #fff;
        }

        .payroll-title {
            font-size: 24px;
            font-weight: 800;
            margin: 0 0 8px 0;
        }

        .payroll-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .payroll-body { padding: 20px; }

        .payroll-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .payroll-table th {
            background: #f1f5f9;
            padding: 12px;
            text-align: right;
            font-weight: 700;
            font-size: 13px;
            border: 1px solid #e2e8f0;
        }

        [dir="ltr"] .payroll-table th { text-align: left; }

        .payroll-table td {
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
        }

        .payroll-table tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        .payroll-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
        }

        .summary-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
        }

        .no-print {
            margin-bottom: 20px;
        }
    </style>

    <div class="payroll-shell">
        <div class="no-print">
            <x-filament::button wire:click="$dispatch('print')" onclick="window.print()" color="primary">
                {{ trans_dash('actions.print_payroll') ?: 'Print Payroll Sheet' }}
            </x-filament::button>
        </div>

        <div class="payroll-card">
            <div class="payroll-header">
                <h1 class="payroll-title">{{ trans_dash('sidebar.hr.payroll') ?: 'Payroll Sheet' }}</h1>
                <div class="payroll-subtitle">
                    {{ trans_dash('tables.hr_payroll.period') ?: 'Period' }}: {{ $record->period ?? '' }}
                    @if($record->department)
                        | {{ trans_dash('tables.hr_payroll.department') ?: 'Department' }}: {{ $record->department->name }}
                    @endif
                </div>
            </div>

            <div class="payroll-body">
                @php
                    $items = $record->items ?? collect();
                    $totalBasic = $items->sum('basic_salary');
                    $totalEarnings = $items->sum('total_earnings');
                    $totalDeductions = $items->sum('total_deductions');
                    $totalNet = $items->sum('net_salary');
                @endphp

                <div class="payroll-summary">
                    <div class="summary-card">
                        <div class="summary-label">{{ trans_dash('tables.hr_payroll.basic_salary') ?: 'Total Basic Salary' }}</div>
                        <div class="summary-value">${{ number_format($totalBasic, 2) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">{{ trans_dash('tables.hr_payroll.total_earnings') ?: 'Total Earnings' }}</div>
                        <div class="summary-value">${{ number_format($totalEarnings, 2) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">{{ trans_dash('tables.hr_payroll.net_salary') ?: 'Total Net Salary' }}</div>
                        <div class="summary-value">${{ number_format($totalNet, 2) }}</div>
                    </div>
                </div>

                <table class="payroll-table">
                    <thead>
                        <tr>
                            <th>{{ trans_dash('tables.hr_payroll.employee_number') ?: 'Employee No' }}</th>
                            <th>{{ trans_dash('tables.hr_payroll.name') ?: 'Name' }}</th>
                            <th>{{ trans_dash('tables.hr_payroll.department') ?: 'Department' }}</th>
                            <th>{{ trans_dash('tables.hr_payroll.basic_salary') ?: 'Basic Salary' }}</th>
                            <th>{{ trans_dash('tables.hr_payroll.total_earnings') ?: 'Total Earnings' }}</th>
                            <th>{{ trans_dash('tables.hr_payroll.total_deductions') ?: 'Total Deductions' }}</th>
                            <th>{{ trans_dash('tables.hr_payroll.net_salary') ?: 'Net Salary' }}</th>
                            <th>{{ trans_dash('tables.hr_payroll.status') ?: 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->employee->employee_number ?? '' }}</td>
                                <td>{{ $item->employee->full_name ?? '' }}</td>
                                <td>{{ $item->employee->department->name ?? '' }}</td>
                                <td>${{ number_format($item->basic_salary, 2) }}</td>
                                <td>${{ number_format($item->total_earnings, 2) }}</td>
                                <td>${{ number_format($item->total_deductions, 2) }}</td>
                                <td><strong>${{ number_format($item->net_salary, 2) }}</strong></td>
                                <td>{{ trans_dash("status.{$item->status}") ?: ucfirst($item->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 20px;">
                                    {{ trans_dash('common.no_records') ?: 'No records found' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="background: #f1f5f9; font-weight: 700;">
                            <td colspan="3" style="text-align: right;">
                                <strong>{{ trans_dash('common.total') ?: 'Total' }}</strong>
                            </td>
                            <td><strong>${{ number_format($totalBasic, 2) }}</strong></td>
                            <td><strong>${{ number_format($totalEarnings, 2) }}</strong></td>
                            <td><strong>${{ number_format($totalDeductions, 2) }}</strong></td>
                            <td><strong>${{ number_format($totalNet, 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-filament::page>
