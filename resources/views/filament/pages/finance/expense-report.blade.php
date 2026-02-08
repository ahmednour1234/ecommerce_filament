<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $totalExpenses = $this->getTotalExpenses();
            $transactionCount = $this->getTransactionCount();
            $groupedByCategory = $this->getGroupedByCategory();
        @endphp

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="relative bg-gradient-to-br from-red-500 via-red-600 to-red-700 dark:from-red-600 dark:via-red-700 dark:to-red-800 rounded-2xl shadow-xl p-6 border-0 overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full -ml-12 -mb-12"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-white/90 uppercase tracking-wider">
                            {{ tr('reports.expense.summary.total_expenses', [], null, 'dashboard') ?: 'Total Expenses' }}
                        </h3>
                        <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-4xl font-bold text-white mb-1">
                        {{ number_format($totalExpenses, 2) }}
                    </p>
                    <p class="text-xs text-white/70">ر.س</p>
                </div>
            </div>

            <div class="relative bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 dark:from-blue-600 dark:via-blue-700 dark:to-blue-800 rounded-2xl shadow-xl p-6 border-0 overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full -ml-12 -mb-12"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-white/90 uppercase tracking-wider">
                            {{ tr('reports.expense.summary.transaction_count', [], null, 'dashboard') ?: 'Transaction Count' }}
                        </h3>
                        <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-4xl font-bold text-white mb-1">
                        {{ number_format($transactionCount, 0) }}
                    </p>
                    <p class="text-xs text-white/70">{{ tr('reports.expense.summary.transactions', [], null, 'dashboard') ?: 'Transactions' }}</p>
                </div>
            </div>

            @if($groupedByCategory->isNotEmpty())
            <div class="relative bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 dark:from-purple-600 dark:via-purple-700 dark:to-purple-800 rounded-2xl shadow-xl p-6 border-0 overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full -ml-12 -mb-12"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-white/90 uppercase tracking-wider">
                            {{ tr('reports.expense.summary.categories', [], null, 'dashboard') ?: 'Categories' }}
                        </h3>
                        <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-4xl font-bold text-white mb-1">
                        {{ $groupedByCategory->count() }}
                    </p>
                    <p class="text-xs text-white/70">{{ tr('reports.expense.summary.active_categories', [], null, 'dashboard') ?: 'Active Categories' }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Category Summary Table --}}
        @if($groupedByCategory->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border-0 overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg p-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            {{ tr('reports.expense.summary.by_category', [], null, 'dashboard') ?: 'Summary by Category' }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                                {{ tr('reports.expense.summary.category', [], null, 'dashboard') ?: 'Category' }}
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                                {{ tr('reports.expense.summary.count', [], null, 'dashboard') ?: 'Count' }}
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                                {{ tr('reports.expense.summary.total_amount', [], null, 'dashboard') ?: 'Total Amount' }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach($groupedByCategory as $index => $item)
                        <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 dark:hover:from-gray-700/50 dark:hover:to-gray-800/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $item['category_name'] ?: tr('reports.expense.summary.uncategorized', [], null, 'dashboard') ?: 'Uncategorized' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ $item['count'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                    {{ number_format($item['total_amount'], 2) }} <span class="text-xs text-gray-500 dark:text-gray-400">ر.س</span>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Main Data Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border-0 overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700">
            {{ $this->table }}
        </div>
    </div>

    <style>
        .fi-ta-table {
            border-radius: 0.75rem;
        }
        
        .fi-section {
            border-radius: 1rem;
        }
    </style>
</x-filament-panels::page>
