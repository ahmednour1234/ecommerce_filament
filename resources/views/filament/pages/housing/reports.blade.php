<x-filament-panels::page class="rtl-dashboard">
    <div class="space-y-6">
        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('housing.reports.filters', [], null, 'dashboard') ?: 'فلترة التقرير' }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="branch_id">
                        <option value="">{{ tr('filters.branch', [], null, 'dashboard') ?: 'الفرع' }}</option>
                        @foreach(\App\Models\MainCore\Branch::all() as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="from_date" />
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="to_date" />
                </x-filament::input.wrapper>
            </div>

            <div class="flex gap-2 mt-4">
                <x-filament::button wire:click="applyFilters" color="primary">
                    {{ tr('actions.search', [], null, 'dashboard') ?: 'بحث' }}
                </x-filament::button>
                <x-filament::button wire:click="resetFilters" color="gray">
                    {{ tr('actions.reset', [], null, 'dashboard') ?: 'إعادة تعيين' }}
                </x-filament::button>
            </div>
        </div>

        {{-- Stats Cards --}}
        @php
            $stats = $this->getStats();
            $statusCounts = $stats['status_counts'] ?? [];
        @endphp
        
        {{-- Main Stats Cards - Row Layout --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-wrap gap-4 items-center justify-between">
                <div class="flex items-center gap-3 min-w-[200px] flex-1">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.total_workers', [], null, 'dashboard') ?: 'إجمالي العمال' }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_workers'] ?? 0) }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 min-w-[200px] flex-1">
                    <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.total_assignments', [], null, 'dashboard') ?: 'إجمالي الإدخالات' }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_assignments'] ?? 0) }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 min-w-[200px] flex-1">
                    <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.transfer_kafala', [], null, 'dashboard') ?: 'محتاجة نقل كفالة' }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['transfer_kafala'] ?? 0) }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 min-w-[200px] flex-1">
                    <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.total_requests', [], null, 'dashboard') ?: 'إجمالي الطلبات' }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_requests'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Cards from Assignments - Row Layout --}}
        @if(count($statusCounts) > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('housing.reports.stats.by_status', [], null, 'dashboard') ?: 'حالات العمال من الإدخالات' }}</h3>
            <div class="flex flex-wrap gap-4 items-center">
                @foreach($statusCounts as $statusKey => $statusData)
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-4 border-r-4 min-w-[200px] flex-1" style="border-color: {{ $statusData['color'] ?? '#6b7280' }};">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $statusData['color'] ?? '#6b7280' }}20;">
                        <svg class="w-5 h-5" style="color: {{ $statusData['color'] ?? '#6b7280' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ $statusData['name'] }}</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($statusData['count']) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Request Status Cards - Row Layout --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('housing.reports.stats.requests_stats', [], null, 'dashboard') ?: 'إحصائيات الطلبات' }}</h3>
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-4 border-r-4 border-yellow-500 min-w-[200px] flex-1">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.pending_requests', [], null, 'dashboard') ?: 'طلبات معلقة' }}</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($stats['pending_requests'] ?? 0) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-4 border-r-4 border-green-500 min-w-[200px] flex-1">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.completed_requests', [], null, 'dashboard') ?: 'طلبات مكتملة' }}</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($stats['completed_requests'] ?? 0) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-4 border-r-4 border-blue-500 min-w-[200px] flex-1">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.delivery_requests', [], null, 'dashboard') ?: 'طلبات تسليم' }}</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($stats['delivery_requests'] ?? 0) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-4 border-r-4 border-orange-500 min-w-[200px] flex-1">
                    <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.return_requests', [], null, 'dashboard') ?: 'طلبات استرجاع' }}</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($stats['return_requests'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs for Switching Between Tables --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="border-b border-gray-200 mb-4">
                <nav class="-mb-px flex space-x-8 rtl:space-x-reverse" aria-label="Tabs">
                    <button
                        wire:click="switchTab('assignments')"
                        type="button"
                        class="@if($activeTab === 'assignments') border-primary-500 text-primary-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        {{ tr('housing.reports.assignments', [], null, 'dashboard') ?: 'حالات الإيواء' }}
                    </button>
                    <button
                        wire:click="switchTab('requests')"
                        type="button"
                        class="@if($activeTab === 'requests') border-primary-500 text-primary-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        {{ tr('housing.reports.requests', [], null, 'dashboard') ?: 'طلبات الإيواء' }}
                    </button>
                </nav>
            </div>

            {{-- Report Table --}}
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
