<x-filament-panels::page class="rtl-dashboard">
    <div class="space-y-6">
        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('housing.reports.filters', [], null, 'dashboard') ?: 'فلترة التقرير' }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="branch_id">
                        <option value="">{{ tr('filters.branch', [], null, 'dashboard') ?: 'الفرع' }}</option>
                        @foreach(\App\Models\MainCore\Branch::whereIn('name', ['حفر الباطن', 'الرياض', 'عرعر'])->get() as $branch)
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
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.total_workers_in_accommodation', [], null, 'dashboard') ?: 'إجمالي العاملة في الإيواء' }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_workers_in_accommodation'] ?? 0) }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 min-w-[200px] flex-1">
                    <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.total_complaints', [], null, 'dashboard') ?: 'إجمالي الشكاوي' }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_complaints'] ?? 0) }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 min-w-[200px] flex-1">
                    <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.ready_for_travel', [], null, 'dashboard') ?: 'جاهزة للتسفير' }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['ready_for_travel'] ?? 0) }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 min-w-[200px] flex-1">
                    <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.new_arrivals', [], null, 'dashboard') ?: 'جديد' }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['new_arrivals'] ?? 0) }}</p>
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

        {{-- Report Table --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
