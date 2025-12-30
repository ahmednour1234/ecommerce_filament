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

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4">
                @if(count($accounts) > 0)
                    <div class="relative space-y-1">
                        @foreach($accounts as $item)
                            @include('filament.pages.accounting.partials.account-tree-item', ['item' => $item, 'level' => 0])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No accounts found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new account.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
