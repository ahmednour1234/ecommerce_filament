<x-filament-panels::page>
    <form wire:submit="import">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-4">
            {{ tr('pages.finance.import.submit', [], null, 'dashboard') ?: 'Import' }}
        </x-filament::button>
    </form>
</x-filament-panels::page>
