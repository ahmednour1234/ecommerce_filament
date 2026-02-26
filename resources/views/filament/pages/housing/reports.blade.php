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
        
        {{-- Main Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-blue-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.total_workers', [], null, 'dashboard') ?: 'إجمالي العمال' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_workers'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-green-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.total_assignments', [], null, 'dashboard') ?: 'إجمالي الإدخالات' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_assignments'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-orange-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.transfer_kafala', [], null, 'dashboard') ?: 'محتاجة نقل كفالة' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['transfer_kafala'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-purple-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.total_requests', [], null, 'dashboard') ?: 'إجمالي الطلبات' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_requests'] ?? 0) }}</p>
            </div>
        </div>

        {{-- Status Cards from Assignments --}}
        @if(count($statusCounts) > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('housing.reports.stats.by_status', [], null, 'dashboard') ?: 'حالات العمال من الإدخالات' }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($statusCounts as $statusKey => $statusData)
                <div class="bg-gray-50 rounded-lg p-4 border-r-4" style="border-color: {{ $statusData['color'] ?? '#6b7280' }};">
                    <p class="text-sm text-gray-600 mb-1">{{ $statusData['name'] }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($statusData['count']) }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Request Status Cards --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('housing.reports.stats.requests_stats', [], null, 'dashboard') ?: 'إحصائيات الطلبات' }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4 border-r-4 border-yellow-500">
                    <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.pending_requests', [], null, 'dashboard') ?: 'طلبات معلقة' }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['pending_requests'] ?? 0) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border-r-4 border-green-500">
                    <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.completed_requests', [], null, 'dashboard') ?: 'طلبات مكتملة' }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['completed_requests'] ?? 0) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border-r-4 border-blue-500">
                    <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.delivery_requests', [], null, 'dashboard') ?: 'طلبات تسليم' }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['delivery_requests'] ?? 0) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border-r-4 border-orange-500">
                    <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.return_requests', [], null, 'dashboard') ?: 'طلبات استرجاع' }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['return_requests'] ?? 0) }}</p>
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
