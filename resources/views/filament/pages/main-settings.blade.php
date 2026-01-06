<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex items-center justify-end gap-4">
            <x-filament::button type="submit" color="primary">
                {{ tr('pages.settings.system_settings.actions.save_settings', [], null, 'dashboard') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
