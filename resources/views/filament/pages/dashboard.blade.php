<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        <div class="space-y-6">
            <div>
                <h2 class="text-xl font-semibold mb-4">إحصائيات المالية</h2>
                {{ $this->financeStatsWidget }}
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4">إحصائيات الموارد البشرية</h2>
                {{ $this->hrStatsWidget }}
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4">إحصائيات المبيعات والتشغيل</h2>
                {{ $this->salesOperationsStatsWidget }}
            </div>

            <div class="grid grid-cols-1 gap-6">
                {{ $this->financeTopTypesWidget }}
                {{ $this->ordersByStatusChartWidget }}
            </div>
        </div>
    </div>
</x-filament-panels::page>

