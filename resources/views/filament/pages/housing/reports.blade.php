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
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-blue-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.workers_count', [], null, 'dashboard') ?: 'عدد العمال' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_workers'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-green-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.total_contracts', [], null, 'dashboard') ?: 'إجمالي العقود' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_contracts'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-purple-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.total_amount', [], null, 'dashboard') ?: 'إجمالي المبالغ' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_amount'] ?? 0, 2) }} {{ tr('common.currency', [], null, 'dashboard') ?: 'ريال' }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-orange-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.reports.stats.total_work_days', [], null, 'dashboard') ?: 'إجمالي أيام العمل' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_work_days'] ?? 0) }}</p>
            </div>
        </div>

        {{-- Report Table --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
