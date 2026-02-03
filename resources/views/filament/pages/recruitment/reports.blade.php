<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters --}}
        {{ $this->form }}

        @php
            $stats = $this->stats;
        @endphp

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                    {{ tr('recruitment_contract.reports.total_contracts', [], null, 'dashboard') ?: 'إجمالي العقود' }}
                </h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                    {{ tr('recruitment_contract.reports.total_cost', [], null, 'dashboard') ?: 'إجمالي التكلفة' }}
                </h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_cost'], 2) }} ر.س</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                    {{ tr('recruitment_contract.reports.paid_total', [], null, 'dashboard') ?: 'المبلغ المدفوع' }}
                </h3>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['paid_total'], 2) }} ر.س</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                    {{ tr('recruitment_contract.reports.remaining_total', [], null, 'dashboard') ?: 'المبلغ المتبقي' }}
                </h3>
                <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['remaining_total'], 2) }} ر.س</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                    {{ tr('recruitment_contract.reports.received_workers', [], null, 'dashboard') ?: 'العمالة المستلمة' }}
                </h3>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['received'] }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                    {{ tr('recruitment_contract.reports.closed_contracts', [], null, 'dashboard') ?: 'العقود المغلقة' }}
                </h3>
                <p class="text-3xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['closed'] }}</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
