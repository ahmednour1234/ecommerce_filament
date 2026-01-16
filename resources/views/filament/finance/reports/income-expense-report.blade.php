<x-filament::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            {{ $this->form }}
        </div>

        @php
            $kpis = $this->kpis();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-success-50 dark:bg-success-900/20 rounded-lg border border-success-200 dark:border-success-800 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-success-600 dark:text-success-400 mb-1">
                            {{ tr('reports.columns.income', [], null, 'dashboard') ?: 'Income' }}
                        </p>
                        <p class="text-3xl font-bold text-success-900 dark:text-success-100">
                            {{ number_format($kpis['income'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-success-100 dark:bg-success-800/50 rounded-lg">
                        <svg class="w-8 h-8 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-danger-50 dark:bg-danger-900/20 rounded-lg border border-danger-200 dark:border-danger-800 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-danger-600 dark:text-danger-400 mb-1">
                            {{ tr('reports.columns.expense', [], null, 'dashboard') ?: 'Expense' }}
                        </p>
                        <p class="text-3xl font-bold text-danger-900 dark:text-danger-100">
                            {{ number_format($kpis['expense'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-danger-100 dark:bg-danger-800/50 rounded-lg">
                        <svg class="w-8 h-8 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-primary-600 dark:text-primary-400 mb-1">
                            {{ tr('reports.columns.net', [], null, 'dashboard') ?: 'Net' }}
                        </p>
                        <p class="text-3xl font-bold text-primary-900 dark:text-primary-100">
                            {{ number_format($kpis['net'], 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-primary-100 dark:bg-primary-800/50 rounded-lg">
                        <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                            {{ tr('reports.columns.transactions', [], null, 'dashboard') ?: 'Transactions' }}
                        </p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                            {{ number_format($kpis['count'], 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-8 h-8 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
