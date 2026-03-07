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
        <a href="{{ \App\Filament\Resources\Rental\ReturnedContractsResource::getUrl() }}" class="block">
            <x-filament::card class="cursor-pointer hover:shadow-lg transition-shadow">
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-2">{{ tr('rental.reports.returned_contracts', [], null, 'dashboard') ?: 'العقود المسترجعة' }}</h3>
                    <p class="text-2xl font-bold">{{ \App\Models\Rental\RentalContract::returned()->count() }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ tr('rental.reports.click_to_view', [], null, 'dashboard') ?: 'انقر للعرض' }}</p>
                </div>
            </x-filament::card>
        </a>

        <a href="{{ \App\Filament\Resources\Rental\RentalContractResource::getUrl('index', ['tableFilters' => ['status' => ['value' => 'cancelled']]]) }}" class="block">
            <x-filament::card class="cursor-pointer hover:shadow-lg transition-shadow">
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-2">{{ tr('rental.reports.cancelled_contracts', [], null, 'dashboard') ?: 'العقود الملغاة' }}</h3>
                    <p class="text-2xl font-bold">{{ \App\Models\Rental\RentalContract::cancelled()->count() }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ tr('rental.reports.click_to_view', [], null, 'dashboard') ?: 'انقر للعرض' }}</p>
                </div>
            </x-filament::card>
        </a>

        <a href="{{ \App\Filament\Resources\Rental\ArchivedContractsResource::getUrl() }}" class="block">
            <x-filament::card class="cursor-pointer hover:shadow-lg transition-shadow">
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-2">{{ tr('rental.reports.archived_contracts', [], null, 'dashboard') ?: 'العقود المؤرشفة' }}</h3>
                    <p class="text-2xl font-bold">{{ \App\Models\Rental\RentalContract::archived()->count() }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ tr('rental.reports.click_to_view', [], null, 'dashboard') ?: 'انقر للعرض' }}</p>
                </div>
            </x-filament::card>
        </a>

        <a href="{{ \App\Filament\Resources\Rental\CancelRefundRequestsResource::getUrl() }}" class="block">
            <x-filament::card class="cursor-pointer hover:shadow-lg transition-shadow">
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-2">{{ tr('rental.reports.cancel_refund_requests', [], null, 'dashboard') ?: 'طلبات الإلغاء والاسترجاع' }}</h3>
                    <p class="text-2xl font-bold">{{ \App\Models\Rental\RentalCancelRefundRequest::count() }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ tr('rental.reports.click_to_view', [], null, 'dashboard') ?: 'انقر للعرض' }}</p>
                </div>
            </x-filament::card>
        </a>
    </div>
</x-filament-panels::page>
