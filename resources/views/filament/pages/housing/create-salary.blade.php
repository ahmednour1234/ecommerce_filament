<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="flex justify-end gap-2 mt-6">
            <x-filament::button type="submit" color="primary">
                {{ tr('housing.actions.save', [], null, 'dashboard') ?: 'حفظ' }}
            </x-filament::button>
            <x-filament::button type="button" wire:click="$dispatch('close-modal')" color="gray">
                {{ tr('housing.actions.cancel', [], null, 'dashboard') ?: 'إلغاء' }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
