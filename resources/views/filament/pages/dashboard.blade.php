<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters Section --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="display: block !important; visibility: visible !important;">
            <div class="fi-section-content-ctn">
                <div class="fi-section-content p-6" style="display: block !important; visibility: visible !important;">
                    <div wire:ignore.self>
                        {{ $this->form }}
                    </div>
                </div>
            </div>
        </div>

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
