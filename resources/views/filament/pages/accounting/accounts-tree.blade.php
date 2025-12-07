<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="selectedAccountType">
                    <option value="all">All Types</option>
                    <option value="asset">Assets</option>
                    <option value="liability">Liabilities</option>
                    <option value="equity">Equity</option>
                    <option value="revenue">Revenue</option>
                    <option value="expense">Expenses</option>
                </x-filament::input.select>
            </x-filament::input.wrapper>
            
            <x-filament::button 
                tag="a" 
                href="{{ \App\Filament\Resources\Accounting\AccountResource::getUrl('create') }}"
                icon="heroicon-o-plus"
            >
                Add Account
            </x-filament::button>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="space-y-2">
                @foreach($accounts as $item)
                    @include('filament.pages.accounting.partials.account-tree-item', ['item' => $item, 'level' => 0])
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
