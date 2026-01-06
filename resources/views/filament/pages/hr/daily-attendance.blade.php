<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <label class="block text-sm font-medium mb-2">
                {{ tr('fields.date', [], null, 'dashboard') ?: 'Date' }}
            </label>
            <input 
                type="date" 
                wire:model.live="selectedDate"
                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
            />
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>

