<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <x-filament::card>
        <div class="text-right">
            <div class="text-sm text-gray-500">الرصيد الافتتاحي</div>
            <div class="text-2xl font-bold">{{ number_format($this->opening_balance, 2) }}</div>
        </div>
    </x-filament::card>

    <x-filament::card>
        <div class="text-right">
            <div class="text-sm text-gray-500">إجمالي الإيرادات</div>
            <div class="text-2xl font-bold">{{ number_format($this->total_income, 2) }}</div>
        </div>
    </x-filament::card>

    <x-filament::card>
        <div class="text-right">
            <div class="text-sm text-gray-500">إجمالي المصروفات</div>
            <div class="text-2xl font-bold">{{ number_format($this->total_expense, 2) }}</div>
        </div>
    </x-filament::card>

    <x-filament::card>
        <div class="text-right">
            <div class="text-sm text-gray-500">صافي التغيير</div>
            <div class="text-2xl font-bold">{{ number_format($this->net_change, 2) }}</div>
        </div>
    </x-filament::card>

    <x-filament::card>
        <div class="text-right">
            <div class="text-sm text-gray-500">الرصيد الختامي</div>
            <div class="text-2xl font-bold">{{ number_format($this->closing_balance, 2) }}</div>
        </div>
    </x-filament::card>
</div>

<div class="mt-6">
    {{ $this->table }}
</div>
