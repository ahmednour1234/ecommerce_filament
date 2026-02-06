<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters --}}
        <form wire:ignore.self>
            {{ $this->form }}
        </form>

        {{-- Header Widgets (FinanceStats + HRStats) --}}
        <x-filament-widgets::widgets
            :widgets="$this->getHeaderWidgets()"
            :columns="$this->getHeaderWidgetsColumns()"
        />

        {{-- Footer Widgets (Top Types + Comparison Chart) --}}
        <x-filament-widgets::widgets
            :widgets="$this->getFooterWidgets()"
            :columns="$this->getFooterWidgetsColumns()"
        />
    </div>
</x-filament-panels::page>
