<x-filament-panels::page>
    <div class="space-y-6" dir="rtl">
        {{-- Header Widgets (OrderStats + FinanceStats + HRStats) --}}
        <div class="grid grid-cols-1 gap-6">
            <x-filament-widgets::widgets
                :widgets="$this->getHeaderWidgets()"
                :columns="$this->getHeaderWidgetsColumns()"
            />
        </div>

        {{-- Footer Widgets (Charts and Table) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-filament-widgets::widgets
                :widgets="[$this->getFooterWidgets()[0]]"
                :columns="1"
            />
            <x-filament-widgets::widgets
                :widgets="[$this->getFooterWidgets()[1]]"
                :columns="1"
            />
        </div>

        {{-- Table Widget (Full Width) --}}
        <div class="grid grid-cols-1 gap-6">
            <x-filament-widgets::widgets
                :widgets="[$this->getFooterWidgets()[2]]"
                :columns="1"
            />
        </div>
    </div>

    <style>
        [dir="rtl"] .fi-section {
            text-align: right;
        }

        [dir="rtl"] .fi-input-wrp input,
        [dir="rtl"] .fi-select-wrp select {
            text-align: right;
        }

        [dir="rtl"] .fi-ta-table {
            direction: rtl;
        }

        [dir="rtl"] .fi-ta-table th {
            text-align: right;
        }

        [dir="rtl"] .fi-ta-table td {
            text-align: right;
        }

        [dir="rtl"] .fi-ta-table td[align="end"] {
            text-align: left;
        }

        .fi-stats-overview-stat {
            border-radius: 0.75rem;
        }

        .fi-widget {
            border-radius: 0.75rem;
        }
    </style>
</x-filament-panels::page>
