<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit">
            حفظ
        </x-filament::button>
    </form>
</x-filament-panels::page>
