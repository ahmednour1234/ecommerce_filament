<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->salesStatsWidget }}
        {{ $this->catalogStatsWidget }}
        {{ $this->orderStatsWidget }}
        {{ $this->accountingSummaryWidget }}
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{ $this->ordersChartWidget }}
            {{ $this->invoicesChartWidget }}
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{ $this->salesByStatusWidget }}
        </div>
    </div>
</x-filament-panels::page>

