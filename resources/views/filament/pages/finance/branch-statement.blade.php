<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $currency = \App\Models\MainCore\Currency::find($this->data['currency_id'] ?? null);
            $currencyCode = $currency?->code ?? '';
            $openingBalance = $this->getOpeningBalance();
            $totalIncome = $this->getTotalIncome();
            $totalExpense = $this->getTotalExpense();
            $netChange = $this->getNetChange();
            $closingBalance = $this->getClosingBalance();
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
