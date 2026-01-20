<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $totalExpenses = $this->getTotalExpenses();
            $transactionCount = $this->getTransactionCount();
            $groupedByCategory = $this->getGroupedByCategory();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-xl shadow-lg p-6 border border-red-200 dark:border-red-700">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-red-700 dark:text-red-300 uppercase tracking-wide">
                        {{ tr('reports.expense.summary.total_expenses', [], null, 'dashboard') ?: 'Total Expenses' }}
                    </h3>
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-red-900 dark:text-red-100">
                    {{ number_format($totalExpenses, 2) }}
                </p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl shadow-lg p-6 border border-blue-200 dark:border-blue-700">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wide">
                        {{ tr('reports.expense.summary.transaction_count', [], null, 'dashboard') ?: 'Transaction Count' }}
                    </h3>
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                    {{ number_format($transactionCount, 0) }}
                </p>
            </div>
        </div>

        @if($groupedByCategory->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ tr('reports.expense.summary.by_category', [], null, 'dashboard') ?: 'Summary by Category' }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ tr('reports.expense.summary.category', [], null, 'dashboard') ?: 'Category' }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ tr('reports.expense.summary.count', [], null, 'dashboard') ?: 'Count' }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ tr('reports.expense.summary.total_amount', [], null, 'dashboard') ?: 'Total Amount' }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($groupedByCategory as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $item['category_name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item['count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100 text-right">
                                {{ number_format($item['total_amount'], 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
