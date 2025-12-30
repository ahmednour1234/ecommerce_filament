<x-filament-panels::page>
    {{-- Flash Messages --}}
    @if(session()->has('success'))
        <div class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="flex flex-col h-full space-y-4">
        {{-- Top Action Bar --}}
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                {{-- Account Type Filters --}}
                <div class="flex items-center gap-2 flex-wrap">
                    <button 
                        wire:click="$set('selectedAccountType', 'all')"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-150
                               {{ $selectedAccountType === 'all' 
                                   ? 'bg-primary-600 text-white shadow-sm' 
                                   : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        All
                    </button>
                    
                    @foreach(['asset' => 'Assets', 'liability' => 'Liabilities', 'equity' => 'Equity', 'revenue' => 'Revenue', 'expense' => 'Expenses'] as $type => $label)
                        <button 
                            wire:click="$set('selectedAccountType', '{{ $type }}')"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-150
                                   {{ $selectedAccountType === $type 
                                       ? 'bg-primary-600 text-white shadow-sm' 
                                       : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Export Button --}}
                <button 
                    wire:click="exportToExcel"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </button>

                {{-- Reset Button --}}
                <button 
                    wire:click="resetFilters"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset
                </button>

                {{-- Add Account Button --}}
                <a 
                    href="{{ \App\Filament\Resources\Accounting\AccountResource::getUrl('create') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Account
                </a>
            </div>
        </div>

        {{-- Main Content: Split Pane Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 flex-1 min-h-0">
            {{-- Left Panel: Account Details --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 h-full flex flex-col">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Account Details</h3>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4">
                        @if($this->selectedAccount)
                            @php
                                $account = $this->selectedAccount;
                                $badgeColors = [
                                    'asset' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'border' => 'border-emerald-200 dark:border-emerald-800'],
                                    'liability' => ['bg' => 'bg-rose-100 dark:bg-rose-900/30', 'text' => 'text-rose-700 dark:text-rose-400', 'border' => 'border-rose-200 dark:border-rose-800'],
                                    'equity' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'border' => 'border-amber-200 dark:border-amber-800'],
                                    'revenue' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400', 'border' => 'border-blue-200 dark:border-blue-800'],
                                    'expense' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-700 dark:text-gray-300', 'border' => 'border-gray-200 dark:border-gray-600'],
                                ];
                                $badgeStyle = $badgeColors[$account->type] ?? $badgeColors['expense'];
                            @endphp

                            <div class="space-y-4">
                                {{-- Account Code --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Account Code</label>
                                    <div class="text-base font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $account->code }}</div>
                                </div>

                                {{-- Account Name --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Account Name</label>
                                    <div class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $account->name }}</div>
                                </div>

                                {{-- Parent Account --}}
                                @if($account->parent)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Parent Account</label>
                                        <div class="text-base text-gray-900 dark:text-gray-100">{{ $account->parent->code }} - {{ $account->parent->name }}</div>
                                    </div>
                                @endif

                                {{-- Account Type --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Account Type</label>
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-md border {{ $badgeStyle['bg'] }} {{ $badgeStyle['text'] }} {{ $badgeStyle['border'] }}">
                                        {{ ucfirst($account->type) }}
                                    </span>
                                </div>

                                {{-- Account Level --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Account Level</label>
                                    <div class="text-base text-gray-900 dark:text-gray-100">{{ $account->level }}</div>
                                </div>

                                {{-- Status --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-md border
                                               {{ $account->is_active 
                                                   ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' 
                                                   : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-600' }}">
                                        {{ $account->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                {{-- Allow Manual Entry --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Allow Manual Entry</label>
                                    <div class="text-base text-gray-900 dark:text-gray-100">{{ $account->allow_manual_entry ? 'Yes' : 'No' }}</div>
                                </div>

                                {{-- Notes --}}
                                @if($account->notes)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Notes</label>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/50 rounded-md p-3">{{ $account->notes }}</div>
                                    </div>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-2">
                                <a 
                                    href="{{ \App\Filament\Resources\Accounting\AccountResource::getUrl('edit', ['record' => $account->id]) }}"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Account
                                </a>

                                @if(auth()->user()?->can('accounts.create'))
                                    <a 
                                        href="{{ \App\Filament\Resources\Accounting\AccountResource::getUrl('create', ['parent_id' => $account->id]) }}"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Sub Account
                                    </a>
                                @endif

                                @if(auth()->user()?->can('accounts.delete'))
                                    <button 
                                        wire:click="deleteAccount({{ $account->id }})"
                                        wire:confirm="Are you sure you want to delete this account? This action cannot be undone."
                                        class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete Account
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-full text-center py-12">
                                <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Account Selected</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Click on an account from the tree to view its details</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Panel: Account Tree --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 h-full flex flex-col">
                    {{-- Search Bar --}}
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input 
                                    type="text"
                                    wire:model.live.debounce.300ms="searchTerm"
                                    placeholder="Search accounts..."
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    {{-- Tree Container --}}
                    <div class="flex-1 overflow-y-auto p-4">
                        @if(count($accounts) > 0)
                            <div class="relative space-y-1">
                                @foreach($accounts as $item)
                                    @include('filament.pages.accounting.partials.account-tree-item', ['item' => $item, 'level' => 0])
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No accounts found</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new account.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
