<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-filament::card>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">{{ tr('rental.reports.active_contracts', [], null, 'dashboard') ?: 'Active Contracts' }}</h3>
                <p class="text-2xl font-bold">{{ \App\Models\Rental\RentalContract::active()->count() }}</p>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">{{ tr('rental.reports.dues_receivables', [], null, 'dashboard') ?: 'Dues/Receivables' }}</h3>
                <p class="text-2xl font-bold">{{ number_format(\App\Models\Rental\RentalContract::sum('remaining_total'), 2) }} SAR</p>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">{{ tr('rental.reports.revenue', [], null, 'dashboard') ?: 'Revenue' }}</h3>
                <p class="text-2xl font-bold">{{ number_format(\App\Models\Rental\RentalContract::sum('paid_total'), 2) }} SAR</p>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">{{ tr('rental.reports.contracts', [], null, 'dashboard') ?: 'Total Contracts' }}</h3>
                <p class="text-2xl font-bold">{{ \App\Models\Rental\RentalContract::count() }}</p>
            </div>
        </x-filament::card>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-filament::card>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">{{ tr('rental.reports.worker_performance', [], null, 'dashboard') ?: 'Worker Performance' }}</h3>
                <p class="text-sm text-gray-600">{{ tr('rental.reports.view_report', [], null, 'dashboard') ?: 'View report' }}</p>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">{{ tr('rental.reports.customers', [], null, 'dashboard') ?: 'Customers Report' }}</h3>
                <p class="text-sm text-gray-600">{{ tr('rental.reports.view_report', [], null, 'dashboard') ?: 'View report' }}</p>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">{{ tr('rental.reports.cancellation_refund', [], null, 'dashboard') ?: 'Cancellation/Refund' }}</h3>
                <p class="text-sm text-gray-600">{{ \App\Models\Rental\RentalCancelRefundRequest::count() }} {{ tr('rental.reports.requests', [], null, 'dashboard') ?: 'requests' }}</p>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">{{ tr('rental.reports.payments', [], null, 'dashboard') ?: 'Payments Report' }}</h3>
                <p class="text-sm text-gray-600">{{ tr('rental.reports.view_report', [], null, 'dashboard') ?: 'View report' }}</p>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>
