<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
            {{ $this->form }}
        </div>

        @if (!empty($this->results))
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold">
                        {{ tr('pages.hr.commission_report.title', [], null, 'dashboard') ?: 'Employee Commission Report' }}
                    </h3>
                    @if ($this->employee)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ tr('pages.hr.commission_report.filters.employee', [], null, 'dashboard') ?: 'Employee' }}: 
                            <strong>{{ $this->employee->full_name }}</strong>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ tr('pages.hr.commission_report.filters.date_from', [], null, 'dashboard') ?: 'From Date' }}: 
                            <strong>{{ \Carbon\Carbon::parse($this->date_from)->format('d/m/Y') }}</strong>
                            - 
                            {{ tr('pages.hr.commission_report.filters.date_to', [], null, 'dashboard') ?: 'To Date' }}: 
                            <strong>{{ \Carbon\Carbon::parse($this->date_to)->format('d/m/Y') }}</strong>
                        </p>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">{{ tr('pages.hr.commission_report.table.commission', [], null, 'dashboard') ?: 'Commission' }}</th>
                                <th class="px-4 py-3">{{ tr('pages.hr.commission_report.table.commission_type', [], null, 'dashboard') ?: 'Type' }}</th>
                                <th class="px-4 py-3">{{ tr('pages.hr.commission_report.table.contract_count', [], null, 'dashboard') ?: 'Contract Count' }}</th>
                                <th class="px-4 py-3">{{ tr('pages.hr.commission_report.table.tier_range', [], null, 'dashboard') ?: 'Tier Range' }}</th>
                                <th class="px-4 py-3">{{ tr('pages.hr.commission_report.table.amount_per_contract', [], null, 'dashboard') ?: 'Amount Per Contract' }}</th>
                                <th class="px-4 py-3">{{ tr('pages.hr.commission_report.table.total', [], null, 'dashboard') ?: 'Total' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->results as $row)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-4 py-3">{{ $row['commission_name'] }}</td>
                                    <td class="px-4 py-3">{{ $row['commission_type'] }}</td>
                                    <td class="px-4 py-3">{{ $row['contract_count'] }}</td>
                                    <td class="px-4 py-3">{{ $row['tier_from'] }}-{{ $row['tier_to'] }}</td>
                                    <td class="px-4 py-3">{{ number_format($row['amount_per_contract'], 2) }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ number_format($row['total'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                                <td colspan="2" class="px-4 py-3">
                                    {{ tr('pages.hr.commission_report.table.grand_total', [], null, 'dashboard') ?: 'Grand Total' }}
                                </td>
                                <td class="px-4 py-3">{{ $this->total_contracts }}</td>
                                <td class="px-4 py-3" colspan="2"></td>
                                <td class="px-4 py-3">{{ number_format($this->total_commission, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif ($this->employee_id)
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-center text-gray-500 dark:text-gray-400">
                    {{ tr('pages.hr.commission_report.no_results', [], null, 'dashboard') ?: 'No results found' }}
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
