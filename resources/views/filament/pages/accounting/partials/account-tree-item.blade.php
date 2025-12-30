@php
    $account = $item['account'];
    $children = $item['children'] ?? [];
    $hasChildren = count($children) > 0;
    $isExpanded = $this->isExpanded($account->id);
    $isParent = $hasChildren;
    $isLeaf = !$hasChildren;
    $isSelected = $this->selectedAccountId == $account->id;
    
    $badgeColors = [
        'asset' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'border' => 'border-emerald-200 dark:border-emerald-800'],
        'liability' => ['bg' => 'bg-rose-50 dark:bg-rose-900/30', 'text' => 'text-rose-700 dark:text-rose-400', 'border' => 'border-rose-200 dark:border-rose-800'],
        'equity' => ['bg' => 'bg-amber-50 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'border' => 'border-amber-200 dark:border-amber-800'],
        'revenue' => ['bg' => 'bg-blue-50 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400', 'border' => 'border-blue-200 dark:border-blue-800'],
        'expense' => ['bg' => 'bg-gray-50 dark:bg-gray-700/50', 'text' => 'text-gray-700 dark:text-gray-300', 'border' => 'border-gray-200 dark:border-gray-600'],
    ];
    $badgeStyle = $badgeColors[$account->type] ?? $badgeColors['expense'];
    
    // Calculate indent for tree structure
    $indent = $level * 24;
    
    // Icon based on account type
    $accountIcons = [
        'asset' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        'liability' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'equity' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'revenue' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'expense' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
    ];
    $accountIcon = $accountIcons[$account->type] ?? 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
@endphp

<div class="account-tree-item group relative" 
     wire:key="account-{{ $account->id }}-{{ $level }}"
     x-data="{ expanded: @js($isExpanded) }">
    
    {{-- Tree connector lines using CSS --}}
    @if($level > 0)
        <div class="absolute left-0 top-0 bottom-0 w-px bg-gray-300 dark:bg-gray-600" 
             style="left: {{ ($level - 1) * 24 + 12 }}px;"></div>
        <div class="absolute top-4 h-px bg-gray-300 dark:bg-gray-600" 
             style="left: {{ ($level - 1) * 24 + 12 }}px; width: 12px;"></div>
    @endif

    {{-- Account row --}}
    <div 
        wire:click="selectAccount({{ $account->id }})"
        class="relative flex items-center justify-between py-3 px-4 rounded-lg transition-all duration-200 cursor-pointer mb-1
               {{ $isSelected 
                   ? 'bg-white dark:bg-gray-800 border-2 border-primary-500 dark:border-primary-600 shadow-lg' 
                   : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 shadow-md hover:shadow-lg' }}"
        style="margin-left: {{ $indent }}px;">
        
        <div class="flex items-center gap-3 flex-1 min-w-0">
            {{-- Expand/Collapse button --}}
            <div class="flex-shrink-0">
                @if($hasChildren)
                    <button 
                        wire:click.stop="toggleAccount({{ $account->id }})"
                        @click.stop="expanded = !expanded"
                        class="flex items-center justify-center w-6 h-6 rounded hover:bg-white dark:hover:bg-gray-600 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
                        aria-label="{{ $isExpanded ? 'Collapse' : 'Expand' }} account">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400 transition-transform duration-200" 
                             :class="expanded ? 'rotate-90' : ''"
                             fill="none" 
                             stroke="currentColor" 
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" 
                                  stroke-linejoin="round" 
                                  stroke-width="2" 
                                  d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                @else
                    <div class="w-6 h-6 flex items-center justify-center">
                        <div class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-500"></div>
                    </div>
                @endif
            </div>

            {{-- Account Type Icon --}}
            <div class="flex-shrink-0">
                @if($isParent)
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                @else
                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $accountIcon }}"></path>
                    </svg>
                @endif
            </div>

            {{-- Account code --}}
            <span class="font-mono text-sm font-semibold text-gray-700 dark:text-gray-300 flex-shrink-0">
                {{ $account->code }}
            </span>

            {{-- Account name --}}
            <span class="text-sm {{ $isParent 
                ? 'font-semibold text-gray-900 dark:text-gray-100' 
                : 'font-medium text-gray-700 dark:text-gray-300' }} truncate">
                {{ $account->name }}
            </span>

            {{-- Account type badge --}}
            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md border 
                         bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600 flex-shrink-0">
                {{ ucfirst($account->type) }}
            </span>

            {{-- Inactive badge --}}
            @if(!$account->is_active)
                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-md 
                             bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-600 flex-shrink-0">
                    Inactive
                </span>
            @endif
        </div>

        {{-- Action buttons (shown on hover or when selected) --}}
        <div class="flex items-center gap-2 flex-shrink-0 ml-4 {{ $isSelected ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }} transition-opacity duration-200">
            <a href="{{ \App\Filament\Resources\Accounting\AccountResource::getUrl('edit', ['record' => $account->id]) }}" 
               wire:click.stop
               class="inline-flex items-center justify-center w-8 h-8 rounded-md text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 border border-transparent hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
               title="Edit account">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </a>

            @if(auth()->user()?->can('accounts.create'))
                <a href="{{ \App\Filament\Resources\Accounting\AccountResource::getUrl('create', ['parent_id' => $account->id]) }}" 
                   wire:click.stop
                   class="inline-flex items-center justify-center w-8 h-8 rounded-md text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 border border-transparent hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1"
                   title="Add child account">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>
            @endif
        </div>
    </div>

    {{-- Children container with collapse animation --}}
    @if($hasChildren)
        <div x-show="expanded"
             x-collapse
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="relative mt-1">
            @foreach($children as $child)
                @include('filament.pages.accounting.partials.account-tree-item', ['item' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
