<x-filament-widgets::widget>
    <div class="fi-section rounded-lg bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-4">
        <form wire:submit.prevent="applyFilters">
            <div class="flex items-end gap-4 flex-wrap">
                <div class="flex-1 min-w-[200px]">
                    {{ $this->filterForm }}
                </div>
                <div class="flex items-end">
                    <x-filament::button type="submit" color="primary">
                        Apply
                    </x-filament::button>
                </div>
            </div>
        </form>
    </div>
</x-filament-widgets::widget>
