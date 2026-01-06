<x-filament-panels::page>
    <form wire:submit="copySchedules">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit" color="success">
                {{ tr('actions.copy_schedules', [], null, 'dashboard') ?: 'Copy Schedules' }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

