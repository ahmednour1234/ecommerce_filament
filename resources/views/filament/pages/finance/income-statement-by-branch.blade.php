<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $currency = \App\Models\MainCore\Currency::find($this->data['currency_id'] ?? null);
            $currencyCode = $currency?->code ?? '';
            $totalIncome = $this->getTotalIncome();
            $totalExpense = $this->getTotalExpense();
            $netProfit = $this->getNetProfit();
            $incomeTypes = $this->getIncomeTypes();
            $expenseTypes = $this->getExpenseTypes();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50 dark:from-emerald-900/30 dark:via-green-900/30 dark:to-teal-900/30 rounded-2xl shadow-xl p-6 border-2 border-emerald-200 dark:border-emerald-700 transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">
                        {{ tr('reports.income_statement.total_income', [], null, 'dashboard') ?: 'Total Income' }}
                    </h3>
                    <div class="bg-emerald-100 dark:bg-emerald-800/50 rounded-full p-3">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-emerald-900 dark:text-emerald-100 mb-1">
                    {{ number_format($totalIncome, 2) }}
                </p>
                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $currencyCode }}</p>
            </div>

            <div class="bg-gradient-to-br from-rose-50 via-red-50 to-pink-50 dark:from-rose-900/30 dark:via-red-900/30 dark:to-pink-900/30 rounded-2xl shadow-xl p-6 border-2 border-rose-200 dark:border-rose-700 transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-rose-700 dark:text-rose-300 uppercase tracking-wider">
                        {{ tr('reports.income_statement.total_expense', [], null, 'dashboard') ?: 'Total Expense' }}
                    </h3>
                    <div class="bg-rose-100 dark:bg-rose-800/50 rounded-full p-3">
                        <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-rose-900 dark:text-rose-100 mb-1">
                    {{ number_format($totalExpense, 2) }}
                </p>
                <p class="text-sm font-semibold text-rose-600 dark:text-rose-400">{{ $currencyCode }}</p>
            </div>

            @if($netProfit >= 0)
            <div class="bg-gradient-to-br from-violet-50 via-purple-50 to-indigo-50 dark:from-violet-900/30 dark:via-purple-900/30 dark:to-indigo-900/30 rounded-2xl shadow-xl p-6 border-2 border-violet-200 dark:border-violet-700 transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-violet-700 dark:text-violet-300 uppercase tracking-wider">
                        {{ tr('reports.income_statement.net_profit', [], null, 'dashboard') ?: 'Net Profit' }}
                    </h3>
                    <div class="bg-violet-100 dark:bg-violet-800/50 rounded-full p-3">
                        <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-violet-900 dark:text-violet-100 mb-1">
                    {{ number_format($netProfit, 2) }}
                </p>
                <p class="text-sm font-semibold text-violet-600 dark:text-violet-400">{{ $currencyCode }}</p>
            </div>
            @else
            <div class="bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 dark:from-orange-900/30 dark:via-amber-900/30 dark:to-yellow-900/30 rounded-2xl shadow-xl p-6 border-2 border-orange-200 dark:border-orange-700 transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-orange-700 dark:text-orange-300 uppercase tracking-wider">
                        {{ tr('reports.income_statement.net_profit', [], null, 'dashboard') ?: 'Net Profit' }}
                    </h3>
                    <div class="bg-orange-100 dark:bg-orange-800/50 rounded-full p-3">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-orange-900 dark:text-orange-100 mb-1">
                    {{ number_format($netProfit, 2) }}
                </p>
                <p class="text-sm font-semibold text-orange-600 dark:text-orange-400">{{ $currencyCode }}</p>
            </div>
            @endif
        </div>

        @if(!empty($incomeTypes))
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4">
                <h3 class="text-lg font-bold text-white uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ tr('reports.income_statement.income_section', [], null, 'dashboard') ?: 'INCOME' }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                {{ tr('reports.income_statement.type', [], null, 'dashboard') ?: 'Type' }}
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                {{ tr('reports.income_statement.total', [], null, 'dashboard') ?: 'Total' }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($incomeTypes as $type)
                        <tr class="hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $type['name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right text-emerald-600 dark:text-emerald-400">
                                {{ number_format($type['total'], 2) }} {{ $currencyCode }}
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-emerald-50 dark:bg-emerald-900/20 font-bold">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ tr('reports.income_statement.total_income', [], null, 'dashboard') ?: 'Total Income' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-emerald-700 dark:text-emerald-300">
                                {{ number_format($totalIncome, 2) }} {{ $currencyCode }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if(!empty($expenseTypes))
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-rose-500 to-pink-500 px-6 py-4">
                <h3 class="text-lg font-bold text-white uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ tr('reports.income_statement.expense_section', [], null, 'dashboard') ?: 'EXPENSE' }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                {{ tr('reports.income_statement.type', [], null, 'dashboard') ?: 'Type' }}
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                {{ tr('reports.income_statement.total', [], null, 'dashboard') ?: 'Total' }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($expenseTypes as $type)
                        <tr class="hover:bg-rose-50 dark:hover:bg-rose-900/10 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $type['name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right text-rose-600 dark:text-rose-400">
                                {{ number_format($type['total'], 2) }} {{ $currencyCode }}
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-rose-50 dark:bg-rose-900/20 font-bold">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ tr('reports.income_statement.total_expense', [], null, 'dashboard') ?: 'Total Expense' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-rose-700 dark:text-rose-300">
                                {{ number_format($totalExpense, 2) }} {{ $currencyCode }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($netProfit >= 0)
        <div class="bg-gradient-to-r from-violet-500 to-indigo-500 rounded-2xl shadow-xl p-8 border-2 border-violet-300 dark:border-violet-600">
        @else
        <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-2xl shadow-xl p-8 border-2 border-orange-300 dark:border-orange-600">
        @endif
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-white/90 uppercase tracking-wider mb-2">
                    {{ tr('reports.income_statement.net_profit', [], null, 'dashboard') ?: 'Net Profit' }}
                </h3>
                <p class="text-4xl font-black text-white">
                    {{ number_format($netProfit, 2) }} <span class="text-xl font-semibold">{{ $currencyCode }}</span>
                </p>
            </div>
            <div class="bg-white/20 rounded-full p-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
        </div>
    </div>
</x-filament-panels::page>
