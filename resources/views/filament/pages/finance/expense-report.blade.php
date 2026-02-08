<x-filament-panels::page>
    @php
        $totalExpenses     = (float) $this->getTotalExpenses();
        $transactionCount  = (int) $this->getTransactionCount();
        $groupedByCategory = $this->getGroupedByCategory();
        $currency          = tr('general.currency.sar', [], null, 'dashboard') ?: 'ر.س';
    @endphp

    <div class="space-y-6" dir="rtl">
        {{-- Top Header --}}
        <div class="relative overflow-hidden rounded-3xl border border-gray-200/60 dark:border-gray-700/60 bg-white/70 dark:bg-gray-900/50 backdrop-blur-xl shadow-sm">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-gradient-to-br from-indigo-500/15 to-purple-500/10 blur-2xl"></div>
                <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-gradient-to-br from-emerald-500/10 to-cyan-500/10 blur-2xl"></div>
            </div>

            <div class="relative px-6 py-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 rounded-2xl p-3 bg-gradient-to-br from-indigo-600 to-purple-700 text-white shadow">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-xl md:text-2xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">
                            {{ tr('reports.expense.summary.title', [], null, 'dashboard') ?: 'Expense Summary' }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            {{ tr('reports.expense.summary.subtitle', [], null, 'dashboard') ?: 'Overview of expenses, transactions, and categories.' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold
                                 bg-gray-900 text-white dark:bg-white dark:text-gray-900 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        {{ tr('reports.expense.summary.live', [], null, 'dashboard') ?: 'Live' }}
                    </span>

                    @if($groupedByCategory->isNotEmpty())
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold
                                     bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200 border border-indigo-200/70 dark:border-indigo-700/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            {{ $groupedByCategory->count() }}
                            <span class="font-normal opacity-80">
                                {{ tr('reports.expense.summary.active_categories', [], null, 'dashboard') ?: 'Active Categories' }}
                            </span>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Total Expenses --}}
            <div class="relative overflow-hidden rounded-3xl border border-red-200/40 dark:border-red-700/30
                        bg-gradient-to-br from-red-500/90 via-red-600/90 to-rose-700/90
                        shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-0.5">
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute -top-20 -right-20 w-56 h-56 bg-white/10 rounded-full blur-xl"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white/5 rounded-full blur-xl"></div>
                </div>

                <div class="relative p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-white/80 uppercase tracking-widest">
                                {{ tr('reports.expense.summary.total_expenses', [], null, 'dashboard') ?: 'Total Expenses' }}
                            </p>
                            <div class="mt-2 flex items-end gap-2">
                                <p class="text-4xl font-extrabold text-white leading-none">
                                    {{ number_format($totalExpenses, 2) }}
                                </p>
                                <span class="text-sm text-white/70 pb-1">{{ $currency }}</span>
                            </div>
                        </div>

                        <div class="rounded-2xl p-3 bg-white/15 backdrop-blur-md border border-white/15">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-between">
                        <span class="text-xs text-white/70">
                            {{ tr('reports.expense.summary.updated_now', [], null, 'dashboard') ?: 'Updated now' }}
                        </span>

                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/15">
                            <span class="w-2 h-2 rounded-full bg-white/70"></span>
                            {{ tr('reports.expense.summary.expenses', [], null, 'dashboard') ?: 'Expenses' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Transaction Count --}}
            <div class="relative overflow-hidden rounded-3xl border border-blue-200/40 dark:border-blue-700/30
                        bg-gradient-to-br from-blue-500/90 via-blue-600/90 to-indigo-700/90
                        shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-0.5">
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute -top-20 -right-20 w-56 h-56 bg-white/10 rounded-full blur-xl"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white/5 rounded-full blur-xl"></div>
                </div>

                <div class="relative p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-white/80 uppercase tracking-widest">
                                {{ tr('reports.expense.summary.transaction_count', [], null, 'dashboard') ?: 'Transaction Count' }}
                            </p>
                            <div class="mt-2 flex items-end gap-2">
                                <p class="text-4xl font-extrabold text-white leading-none">
                                    {{ number_format($transactionCount, 0) }}
                                </p>
                                <span class="text-sm text-white/70 pb-1">
                                    {{ tr('reports.expense.summary.transactions', [], null, 'dashboard') ?: 'Transactions' }}
                                </span>
                            </div>
                        </div>

                        <div class="rounded-2xl p-3 bg-white/15 backdrop-blur-md border border-white/15">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-between">
                        <span class="text-xs text-white/70">
                            {{ tr('reports.expense.summary.updated_now', [], null, 'dashboard') ?: 'Updated now' }}
                        </span>

                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/15">
                            <span class="w-2 h-2 rounded-full bg-white/70"></span>
                            {{ tr('reports.expense.summary.records', [], null, 'dashboard') ?: 'Records' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Categories --}}
            @if($groupedByCategory->isNotEmpty())
                <div class="relative overflow-hidden rounded-3xl border border-purple-200/40 dark:border-purple-700/30
                            bg-gradient-to-br from-purple-500/90 via-purple-600/90 to-fuchsia-700/90
                            shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-0.5">
                    <div class="absolute inset-0 pointer-events-none">
                        <div class="absolute -top-20 -right-20 w-56 h-56 bg-white/10 rounded-full blur-xl"></div>
                        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white/5 rounded-full blur-xl"></div>
                    </div>

                    <div class="relative p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-white/80 uppercase tracking-widest">
                                    {{ tr('reports.expense.summary.categories', [], null, 'dashboard') ?: 'Categories' }}
                                </p>
                                <div class="mt-2 flex items-end gap-2">
                                    <p class="text-4xl font-extrabold text-white leading-none">
                                        {{ $groupedByCategory->count() }}
                                    </p>
                                    <span class="text-sm text-white/70 pb-1">
                                        {{ tr('reports.expense.summary.active_categories', [], null, 'dashboard') ?: 'Active Categories' }}
                                    </span>
                                </div>
                            </div>

                            <div class="rounded-2xl p-3 bg-white/15 backdrop-blur-md border border-white/15">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                        </div>

                        <div class="mt-5 flex items-center justify-between">
                            <span class="text-xs text-white/70">
                                {{ tr('reports.expense.summary.updated_now', [], null, 'dashboard') ?: 'Updated now' }}
                            </span>

                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/15">
                                <span class="w-2 h-2 rounded-full bg-white/70"></span>
                                {{ tr('reports.expense.summary.grouping', [], null, 'dashboard') ?: 'Grouping' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Category Summary Table --}}
        @if($groupedByCategory->isNotEmpty())
            <div class="rounded-3xl overflow-hidden border border-gray-200/70 dark:border-gray-700/70 bg-white dark:bg-gray-900 shadow-sm">
                <div class="px-6 py-5 bg-gradient-to-l from-gray-50 to-white dark:from-gray-900 dark:to-gray-950 border-b border-gray-200/70 dark:border-gray-700/70">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="rounded-2xl p-2 bg-gradient-to-br from-indigo-600 to-purple-700 text-white shadow">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-lg md:text-xl font-extrabold text-gray-900 dark:text-gray-100">
                                    {{ tr('reports.expense.summary.by_category', [], null, 'dashboard') ?: 'Summary by Category' }}
                                </h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                                    {{ tr('reports.expense.summary.by_category_hint', [], null, 'dashboard') ?: 'Breakdown of transactions and totals for each category.' }}
                                </p>
                            </div>
                        </div>

                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold
                                     bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200 border border-gray-200/70 dark:border-gray-700/70">
                            {{ $groupedByCategory->count() }}
                            <span class="ms-1 opacity-75">{{ tr('reports.expense.summary.rows', [], null, 'dashboard') ?: 'Rows' }}</span>
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-extrabold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200/70 dark:border-gray-700/70">
                                    {{ tr('reports.expense.summary.category', [], null, 'dashboard') ?: 'Category' }}
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-extrabold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200/70 dark:border-gray-700/70">
                                    {{ tr('reports.expense.summary.count', [], null, 'dashboard') ?: 'Count' }}
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200/70 dark:border-gray-700/70">
                                    {{ tr('reports.expense.summary.total_amount', [], null, 'dashboard') ?: 'Total Amount' }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($groupedByCategory as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600"></span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $item['category_name'] ?: (tr('reports.expense.summary.uncategorized', [], null, 'dashboard') ?: 'Uncategorized') }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold
                                                     bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200">
                                            {{ (int) ($item['count'] ?? 0) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-left">
                                        <span class="text-sm font-extrabold text-gray-900 dark:text-gray-100">
                                            {{ number_format((float) ($item['total_amount'] ?? 0), 2) }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ms-1">{{ $currency }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Main Data Table --}}
        <div class="rounded-3xl overflow-hidden border border-gray-200/70 dark:border-gray-700/70 bg-white dark:bg-gray-900 shadow-sm">
            <div class="p-3 md:p-4 bg-gradient-to-l from-gray-50 to-white dark:from-gray-900 dark:to-gray-950 border-b border-gray-200/70 dark:border-gray-700/70">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm md:text-base font-bold text-gray-900 dark:text-gray-100">
                        {{ tr('reports.expense.summary.data_table', [], null, 'dashboard') ?: 'Transactions' }}
                    </h3>

                    <span class="text-xs text-gray-600 dark:text-gray-400">
                        {{ tr('reports.expense.summary.table_hint', [], null, 'dashboard') ?: 'Use filters and search to narrow results.' }}
                    </span>
                </div>
            </div>

            <div class="p-2 md:p-3">
                {{ $this->table }}
            </div>
        </div>
    </div>

    {{-- Optional micro-style tune for Filament table --}}
    <style>
        /* Keep overall rounded look */
        .fi-ta,
        .fi-ta-ctn,
        .fi-ta-table {
            border-radius: 1.25rem !important;
        }

        /* Table header stickiness look (if your table supports it) */
        .fi-ta-header-ctn {
            border-top-left-radius: 1.25rem !important;
            border-top-right-radius: 1.25rem !important;
        }

        /* Better row hover */
        .fi-ta-row:hover {
            filter: brightness(0.98);
        }

        /* RTL friendly: prevent icons misalignment */
        [dir="rtl"] .fi-ta-actions,
        [dir="rtl"] .fi-ta-filters {
            justify-content: flex-start;
        }
    </style>
</x-filament-panels::page>
