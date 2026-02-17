<x-filament-panels::page class="rtl-dashboard">
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-sm text-gray-600 mb-2">{{ tr('housing.reports.returns_this_month', [], null, 'dashboard') ?: 'استرجاع هذا الشهر' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ Number::format($this->getReturnsThisMonth()) }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-sm text-gray-600 mb-2">{{ tr('housing.reports.exits_this_month', [], null, 'dashboard') ?: 'خروج هذا الشهر' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ Number::format($this->getExitsThisMonth()) }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-sm text-gray-600 mb-2">{{ tr('housing.reports.entries_this_month', [], null, 'dashboard') ?: 'دخول هذا الشهر' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ Number::format($this->getEntriesThisMonth()) }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-sm text-gray-600 mb-2">{{ tr('housing.reports.current_residents', [], null, 'dashboard') ?: 'المقيمين الحاليين' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ Number::format($this->getCurrentResidents()) }}</p>
            </div>
        </div>

        {{-- Report Tiles Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $reports = [
                    [
                        'key' => 'return_report',
                        'icon' => 'heroicon-o-arrow-uturn-left',
                        'title' => tr('housing.reports.return_report', [], null, 'dashboard') ?: 'تقرير الاسترجاع',
                        'subtitle' => tr('housing.reports.return_report', [], null, 'dashboard') ?: 'تقرير الاسترجاع',
                    ],
                    [
                        'key' => 'status_report',
                        'icon' => 'heroicon-o-tag',
                        'title' => tr('housing.reports.status_report', [], null, 'dashboard') ?: 'تقرير الحالات',
                        'subtitle' => tr('housing.reports.status_report', [], null, 'dashboard') ?: 'تقرير الحالات',
                    ],
                    [
                        'key' => 'movements_report',
                        'icon' => 'heroicon-o-arrow-right-left',
                        'title' => tr('housing.reports.movements_report', [], null, 'dashboard') ?: 'تقرير الحركات',
                        'subtitle' => tr('housing.reports.movements_report', [], null, 'dashboard') ?: 'تقرير الحركات',
                    ],
                    [
                        'key' => 'occupancy_report',
                        'icon' => 'heroicon-o-building-office',
                        'title' => tr('housing.reports.occupancy_report', [], null, 'dashboard') ?: 'تقرير الإشغال',
                        'subtitle' => tr('housing.reports.occupancy_report', [], null, 'dashboard') ?: 'تقرير الإشغال',
                    ],
                    [
                        'key' => 'events_report',
                        'icon' => 'heroicon-o-calendar',
                        'title' => tr('housing.reports.events_report', [], null, 'dashboard') ?: 'تقرير الأحداث',
                        'subtitle' => tr('housing.reports.events_report', [], null, 'dashboard') ?: 'تقرير الأحداث',
                    ],
                    [
                        'key' => 'branches_report',
                        'icon' => 'heroicon-o-map-pin',
                        'title' => tr('housing.reports.branches_report', [], null, 'dashboard') ?: 'تقرير الفروع',
                        'subtitle' => tr('housing.reports.branches_report', [], null, 'dashboard') ?: 'تقرير الفروع',
                    ],
                    [
                        'key' => 'accommodation_duration_report',
                        'icon' => 'heroicon-o-clock',
                        'title' => tr('housing.reports.accommodation_duration_report', [], null, 'dashboard') ?: 'تقرير مدة الإيواء',
                        'subtitle' => tr('housing.reports.accommodation_duration_report', [], null, 'dashboard') ?: 'تقرير مدة الإيواء',
                    ],
                    [
                        'key' => 'return_frequency_report',
                        'icon' => 'heroicon-o-arrow-path',
                        'title' => tr('housing.reports.return_frequency_report', [], null, 'dashboard') ?: 'تقرير تكرار الاسترجاع',
                        'subtitle' => tr('housing.reports.return_frequency_report', [], null, 'dashboard') ?: 'تقرير تكرار الاسترجاع',
                    ],
                ];
            @endphp

            @foreach($reports as $report)
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <x-filament::icon 
                                :icon="$report['icon']" 
                                class="w-8 h-8 text-blue-600" 
                            />
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-center mb-2">{{ $report['title'] }}</h3>
                    <p class="text-sm text-gray-600 text-center mb-4">{{ $report['subtitle'] }}</p>
                    <div class="flex justify-center">
                        <x-filament::button
                            color="primary"
                            size="sm"
                            class="w-full"
                            wire:click="viewReport('{{ $report['key'] }}')"
                        >
                            {{ tr('housing.reports.view_report', [], null, 'dashboard') ?: 'عرض التقرير' }}
                        </x-filament::button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
