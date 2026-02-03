<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $resource = \App\Filament\Resources\Recruitment\RecruitmentContractResource::class;
            $url = $resource::getUrl('index', ['tableFilters' => ['status' => ['value' => 'closed']]]);
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="text-center py-8">
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    {{ tr('recruitment_contract.menu.expired_contracts', [], null, 'dashboard') ?: 'العقود المنتهية' }}
                </p>
                <a href="{{ $url }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    {{ tr('common.view_all', [], null, 'dashboard') ?: 'عرض الكل' }}
                </a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
