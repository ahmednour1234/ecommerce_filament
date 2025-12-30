@php
    $account = $item['account'];
    $children = $item['children'] ?? [];
    $hasChildren = count($children) > 0;
    $isExpanded = $this->isExpanded($account->id);
    $isParent = $hasChildren;
    $isLeaf = !$hasChildren;
    
    $badgeColors = [
        'asset' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
        'liability' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200'],
        'equity' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
        'revenue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
        'expense' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200'],
    ];
    $badgeStyle = $badgeColors[$account->type] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200'];
    
    // Calculate indent for tree structure
    $indent = $level * 24;
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
    <div class="relative flex items-center justify-between py-2.5 px-4 rounded-lg transition-all duration-200 
                {{ $isParent ? 'bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30' }}
                border border-transparent hover:border-gray-200 dark:hover:border-gray-600"
         style="margin-left: {{ $indent }}px;">
        
        <div class="flex items-center gap-3 flex-1 min-w-0">
            {{-- Expand/Collapse button --}}
            <div class="flex-shrink-0">
                @if($hasChildren)
                    <button 
                        wire:click="toggleAccount({{ $account->id }})"
                        @click="expanded = !expanded"
                        class="flex items-center justify-center w-6 h-6 rounded hover:bg-white transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
                        aria-label="{{ $isExpanded ? 'Collapse' : 'Expand' }} account">
                        <svg class="w-4 h-4 text-gray-600 transition-transform duration-200" 
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
                        <div class="w-1.5 h-1.5 rounded-full bg-gray-400"></div>
                    </div>
                @endif
            </div>

            {{-- Account code --}}
            <span class="font-mono text-sm font-semibold text-gray-700 dark:text-gray-300 flex-shrink-0">
                {{ $account->code }}
            </span>

            {{-- Account name --}}
            <span class="text-sm {{ $isParent ? 'font-semibold text-gray-900 dark:text-gray-100' : 'font-medium text-gray-700 dark:text-gray-300' }} truncate">
                {{ $account->name }}
            </span>

            {{-- Account type badge --}}
            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md border 
                         {{ $badgeStyle['bg'] }} {{ $badgeStyle['text'] }} {{ $badgeStyle['border'] }} flex-shrink-0">
                {{ ucfirst($account->type) }}
            </span>

            {{-- Inactive badge --}}
            @if(!$account->is_active)
                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-md 
                             bg-gray-100 text-gray-600 border border-gray-200 flex-shrink-0">
                    Inactive
                </span>
            @endif
        </div>

        {{-- Action buttons --}}
        <div class="flex items-center gap-2 flex-shrink-0 ml-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <a href="{{ \App\Filament\Resources\Accounting\AccountResource::getUrl('edit', ['record' => $account->id]) }}" 
               class="inline-flex items-center justify-center w-8 h-8 rounded-md text-gray-600 hover:text-primary-600 hover:bg-primary-50 border border-transparent hover:border-primary-200 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
               title="Edit account">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </a>

            @if(auth()->user()?->can('accounts.create'))
                <a href="{{ \App\Filament\Resources\Accounting\AccountResource::getUrl('create', ['parent_id' => $account->id]) }}" 
                   class="inline-flex items-center justify-center w-8 h-8 rounded-md text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 border border-transparent hover:border-emerald-200 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1"
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
