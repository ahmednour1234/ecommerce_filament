<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('reports.branch_statement.opening_balance', [], null, 'dashboard') ?: 'Opening Balance' }}</h3>
            <p class="text-2xl font-bold">
                {{ number_format($this->getOpeningBalance(), 2) }}
                {{ \App\Models\MainCore\Currency::find($this->data['currency_id'] ?? null)?->code ?? '' }}
            </p>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
