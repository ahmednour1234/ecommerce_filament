<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ tr('reports.income_statement.total_income', [], null, 'dashboard') ?: 'Total Income' }}</h3>
                <p class="text-2xl font-bold text-green-600">
                    {{ number_format($this->getTotalIncome(), 2) }}
                    {{ \App\Models\MainCore\Currency::find($this->data['currency_id'] ?? null)?->code ?? '' }}
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ tr('reports.income_statement.total_expense', [], null, 'dashboard') ?: 'Total Expense' }}</h3>
                <p class="text-2xl font-bold text-red-600">
                    {{ number_format($this->getTotalExpense(), 2) }}
                    {{ \App\Models\MainCore\Currency::find($this->data['currency_id'] ?? null)?->code ?? '' }}
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ tr('reports.income_statement.net_profit', [], null, 'dashboard') ?: 'Net Profit' }}</h3>
                <p class="text-2xl font-bold {{ $this->getNetProfit() >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($this->getNetProfit(), 2) }}
                    {{ \App\Models\MainCore\Currency::find($this->data['currency_id'] ?? null)?->code ?? '' }}
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('reports.income_statement.income_section', [], null, 'dashboard') ?: 'INCOME' }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ tr('reports.income_statement.type', [], null, 'dashboard') ?: 'Type' }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ tr('reports.income_statement.total', [], null, 'dashboard') ?: 'Total' }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getIncomeTypes() as $type)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $type['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($type['total'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="font-bold bg-gray-50 dark:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ tr('reports.income_statement.total_income', [], null, 'dashboard') ?: 'Total Income' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">{{ number_format($this->getTotalIncome(), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('reports.income_statement.expense_section', [], null, 'dashboard') ?: 'EXPENSE' }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ tr('reports.income_statement.type', [], null, 'dashboard') ?: 'Type' }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ tr('reports.income_statement.total', [], null, 'dashboard') ?: 'Total' }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getExpenseTypes() as $type)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $type['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($type['total'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="font-bold bg-gray-50 dark:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ tr('reports.income_statement.total_expense', [], null, 'dashboard') ?: 'Total Expense' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600">{{ number_format($this->getTotalExpense(), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('reports.income_statement.net_profit', [], null, 'dashboard') ?: 'Net Profit' }}</h3>
            <p class="text-3xl font-bold {{ $this->getNetProfit() >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ number_format($this->getNetProfit(), 2) }}
                {{ \App\Models\MainCore\Currency::find($this->data['currency_id'] ?? null)?->code ?? '' }}
            </p>
        </div>
    </div>
</x-filament-panels::page>
