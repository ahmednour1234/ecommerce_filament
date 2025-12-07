<div class="bg-gray-50 dark:bg-gray-700 rounded p-3">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            @if($account->children->count() > 0)
                <button 
                    wire:click="$toggle('expanded_{{ $account->id }}')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <x-heroicon-o-chevron-right 
                        class="w-3 h-3 transition-transform {{ $this->get('expanded_' . $account->id) ? 'rotate-90' : '' }}"
                    />
                </button>
            @endif
            <span class="text-sm font-medium text-gray-800 dark:text-white">
                {{ $account->code }} - {{ $account->name }}
            </span>
            <span class="px-2 py-0.5 text-xs rounded-full bg-{{ getTypeColor($account->type) }}-100 text-{{ getTypeColor($account->type) }}-800">
                {{ ucfirst($account->type) }}
            </span>
        </div>
        <a 
            href="{{ \App\Filament\Resources\Accounting\AccountResource::getUrl('edit', ['record' => $account->id]) }}"
            class="text-primary-600 hover:text-primary-800"
        >
            <x-heroicon-o-pencil class="w-3 h-3" />
        </a>
    </div>
    
    @if($account->children->count() > 0 && $this->get('expanded_' . $account->id))
        <div class="mt-2 ml-6 space-y-1 border-l-2 border-gray-300 pl-3">
            @foreach($account->children as $child)
                @include('filament.pages.accounting.partials.account-item', ['account' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>

