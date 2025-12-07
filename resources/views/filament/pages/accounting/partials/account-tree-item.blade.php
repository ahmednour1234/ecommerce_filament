@php
    $account = $item['account'];
    $children = $item['children'] ?? [];
    $indent = $level * 20;
    $badgeColors = [
        'asset' => 'success',
        'liability' => 'danger',
        'equity' => 'warning',
        'revenue' => 'info',
        'expense' => 'gray',
    ];
    $badgeColor = $badgeColors[$account->type] ?? 'primary';
@endphp

<div class="border-b border-gray-200 py-3" style="padding-left: {{ $indent }}px;">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4 flex-1">
            <div class="flex items-center gap-2">
                @if(count($children) > 0)
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                @else
                    <div class="w-4"></div>
                @endif
                
                <span class="font-mono text-sm text-gray-600">{{ $account->code }}</span>
                <span class="font-medium">{{ $account->name }}</span>
                
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $badgeColor }}-100 text-{{ $badgeColor }}-800">
                    {{ ucfirst($account->type) }}
                </span>

                @if(!$account->is_active)
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                        Inactive
                    </span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ \App\Filament\Resources\Accounting\AccountResource::getUrl('edit', ['record' => $account->id]) }}" 
               class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">
                Edit
            </a>

            @if(auth()->user()?->can('accounts.create'))
                <a href="{{ \App\Filament\Resources\Accounting\AccountResource::getUrl('create', ['parent_id' => $account->id]) }}" 
                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-green-600 border border-green-700 rounded hover:bg-green-700">
                    Add Child
                </a>
            @endif
        </div>
    </div>

    @if(count($children) > 0)
        <div class="mt-2 space-y-1">
            @foreach($children as $child)
                @include('filament.pages.accounting.partials.account-tree-item', ['item' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>

