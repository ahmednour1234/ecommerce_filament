<x-filament-panels::page class="rtl-dashboard">
    <div class="space-y-6">
        {{-- Filter Section --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('housing.dashboard.filter_entries', [], null, 'dashboard') ?: 'فلترة الإدخالات' }}</h3>
            
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

        {{-- Entries Table --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('housing.dashboard.entries_table', [], null, 'dashboard') ?: 'جدول الإدخالات' }}</h3>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
