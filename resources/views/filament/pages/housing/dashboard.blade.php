<x-filament-panels::page class="rtl-dashboard">
    <div class="space-y-6">
        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Completed Requests --}}
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.dashboard.completed_requests', [], null, 'dashboard') ?: 'طلبات مكتملة' }}</p>
                        <p class="text-3xl font-bold text-gray-800">{{ Number::format($this->getCompletedCount()) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Approved Requests --}}
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-teal-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.dashboard.approved_requests', [], null, 'dashboard') ?: 'طلبات موافق عليها' }}</p>
                        <p class="text-3xl font-bold text-gray-800">{{ Number::format($this->getApprovedCount()) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v3.5m7 0V9a2 2 0 00-2-2h-4a2 2 0 00-2 2v1.5"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Pending Requests --}}
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.dashboard.pending_requests', [], null, 'dashboard') ?: 'طلبات معلقة' }}</p>
                        <p class="text-3xl font-bold text-gray-800">{{ Number::format($this->getPendingCount()) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-row flex-wrap gap-4 justify-center items-center">
            <a href="{{ \App\Filament\Resources\Housing\HousingRequestResource::getUrl() }}?tableFilters[request_type][value]=new_rent" 
               class="flex-1 min-w-[200px] max-w-[300px] inline-flex items-center justify-center px-6 py-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 text-center">
                {{ tr('housing.dashboard.delivery_tasks', [], null, 'dashboard') ?: 'مهام التوصيل' }}
            </a>

            <a href="{{ \App\Filament\Resources\Housing\HousingDriverResource::getUrl() }}" 
               class="flex-1 min-w-[200px] max-w-[300px] inline-flex items-center justify-center px-6 py-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 text-center">
                {{ tr('housing.dashboard.driver_management', [], null, 'dashboard') ?: 'إدارة السائقين' }}
            </a>

            <a href="{{ \App\Filament\Pages\Housing\HousingReportsPage::getUrl() }}" 
               class="flex-1 min-w-[200px] max-w-[300px] inline-flex items-center justify-center px-6 py-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 text-center">
                {{ tr('housing.dashboard.order_reports', [], null, 'dashboard') ?: 'تقارير الطلبات' }}
            </a>

            <a href="{{ \App\Filament\Resources\Housing\HousingDriverResource::getUrl() }}" 
               class="flex-1 min-w-[200px] max-w-[300px] inline-flex items-center justify-center px-6 py-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 text-center">
                {{ tr('housing.dashboard.driver_performance', [], null, 'dashboard') ?: 'أداء السائقين' }}
            </a>
        </div>

        {{-- Filter Section --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('housing.dashboard.filter_requests', [], null, 'dashboard') ?: 'فلترة الطلبات' }}</h3>
            
            {{ $this->form }}
            
            <div class="flex gap-2 mt-4">
                <x-filament::button wire:click="search" color="primary">
                    {{ tr('housing.dashboard.search', [], null, 'dashboard') ?: 'بحث' }}
                </x-filament::button>
                <x-filament::button wire:click="resetFilters" color="gray">
                    {{ tr('housing.dashboard.reset', [], null, 'dashboard') ?: 'إعادة تعيين' }}
                </x-filament::button>
            </div>
        </div>

        {{-- Requests Table --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('housing.dashboard.requests_table', [], null, 'dashboard') ?: 'جدول الطلبات' }}</h3>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
