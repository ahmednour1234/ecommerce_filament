<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $currency = \App\Models\MainCore\Currency::find($this->data['currency_id'] ?? null);
            $currencyCode = $currency?->code ?? '';
            $openingBalance = $this->getOpeningBalance();
            $totalIncome = $this->getTotalIncome();
            $totalExpense = $this->getTotalExpense();
            $netChange = $this->getNetChange();
            $closingBalance = $this->getClosingBalance();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl shadow-lg p-6 border border-blue-200 dark:border-blue-700">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wide">
                        {{ tr('reports.branch_statement.opening_balance', [], null, 'dashboard') ?: 'Opening Balance' }}
                    </h3>
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                    {{ number_format($openingBalance, 2) }} <span class="text-sm font-medium">{{ $currencyCode }}</span>
                </p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl shadow-lg p-6 border border-green-200 dark:border-green-700">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-green-700 dark:text-green-300 uppercase tracking-wide">
                        {{ tr('reports.branch_statement.total_income', [], null, 'dashboard') ?: 'Total Income' }}
                    </h3>
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                    {{ number_format($totalIncome, 2) }} <span class="text-sm font-medium">{{ $currencyCode }}</span>
                </p>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-xl shadow-lg p-6 border border-red-200 dark:border-red-700">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-red-700 dark:text-red-300 uppercase tracking-wide">
                        {{ tr('reports.branch_statement.total_expense', [], null, 'dashboard') ?: 'Total Expense' }}
                    </h3>
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-red-900 dark:text-red-100">
                    {{ number_format($totalExpense, 2) }} <span class="text-sm font-medium">{{ $currencyCode }}</span>
                </p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl shadow-lg p-6 border border-purple-200 dark:border-purple-700">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-purple-700 dark:text-purple-300 uppercase tracking-wide">
                        {{ tr('reports.branch_statement.net_change', [], null, 'dashboard') ?: 'Net Change' }}
                    </h3>
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold {{ $netChange >= 0 ? 'text-purple-900 dark:text-purple-100' : 'text-red-600 dark:text-red-400' }}">
                    {{ number_format($netChange, 2) }} <span class="text-sm font-medium">{{ $currencyCode }}</span>
                </p>
            </div>

            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-xl shadow-lg p-6 border border-indigo-200 dark:border-indigo-700">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-indigo-700 dark:text-indigo-300 uppercase tracking-wide">
                        {{ tr('reports.branch_statement.closing_balance', [], null, 'dashboard') ?: 'Closing Balance' }}
                    </h3>
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-indigo-900 dark:text-indigo-100">
                    {{ number_format($closingBalance, 2) }} <span class="text-sm font-medium">{{ $currencyCode }}</span>
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
