<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            {{ $this->form }}
        </div>

        @php
            $currency = \App\Models\MainCore\Currency::find($this->data['currency_id'] ?? null);
            $currencyCode = $currency?->code ?? '';
            $totalIncome = $this->getTotalIncome();
            $totalExpense = $this->getTotalExpense();
            $netProfit = $this->getNetProfit();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-success-50 dark:bg-success-900/20 rounded-lg border border-success-200 dark:border-success-800 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-success-600 dark:text-success-400 mb-1">
                            {{ tr('reports.income_statement.total_income', [], null, 'dashboard') ?: 'Total Income' }}
                        </p>
                        <p class="text-3xl font-bold text-success-900 dark:text-success-100">
                            {{ number_format($totalIncome, 2) }}
                        </p>
                        <p class="text-xs text-success-600 dark:text-success-400 mt-1">{{ $currencyCode }}</p>
                    </div>
                    <div class="p-3 bg-success-100 dark:bg-success-800/50 rounded-lg">
                        <svg class="w-8 h-8 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-danger-50 dark:bg-danger-900/20 rounded-lg border border-danger-200 dark:border-danger-800 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-danger-600 dark:text-danger-400 mb-1">
                            {{ tr('reports.income_statement.total_expense', [], null, 'dashboard') ?: 'Total Expense' }}
                        </p>
                        <p class="text-3xl font-bold text-danger-900 dark:text-danger-100">
                            {{ number_format($totalExpense, 2) }}
                        </p>
                        <p class="text-xs text-danger-600 dark:text-danger-400 mt-1">{{ $currencyCode }}</p>
                    </div>
                    <div class="p-3 bg-danger-100 dark:bg-danger-800/50 rounded-lg">
                        <svg class="w-8 h-8 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-primary-600 dark:text-primary-400 mb-1">
                            {{ tr('reports.income_statement.net_profit', [], null, 'dashboard') ?: 'Net Profit' }}
                        </p>
                        <p class="text-3xl font-bold text-primary-900 dark:text-primary-100">
                            {{ number_format($netProfit, 2) }}
                        </p>
                        <p class="text-xs text-primary-600 dark:text-primary-400 mt-1">{{ $currencyCode }}</p>
                    </div>
                    <div class="p-3 bg-primary-100 dark:bg-primary-800/50 rounded-lg">
                        <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
