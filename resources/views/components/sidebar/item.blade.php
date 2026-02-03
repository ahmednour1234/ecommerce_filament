@props(['item', 'level' => 0])

@php
    $hasChildren = !empty($item['children']);
    $url = $item['url'] ?? null;
    $title = tr($item['title'], [], null, 'dashboard') ?: $item['title'];
    $icon = $item['icon'] ?? null;
    $badge = $item['badge'] ?? null;
    $itemId = 'sidebar-item-' . md5($item['title'] . $level);
    
    $currentUrl = request()->url();
    $isActive = false;
    
    if ($url && $currentUrl === $url) {
        $isActive = true;
    }
    
    if ($hasChildren) {
        foreach ($item['children'] as $child) {
            $childUrl = $child['url'] ?? null;
            if ($childUrl && $currentUrl === $childUrl) {
                $isActive = true;
                break;
            }
            if (isset($child['children'])) {
                foreach ($child['children'] as $grandchild) {
                    $grandchildUrl = $grandchild['url'] ?? null;
                    if ($grandchildUrl && $currentUrl === $grandchildUrl) {
                        $isActive = true;
                        break 2;
                    }
                }
            }
        }
    }
    
    $shouldBeOpen = $isActive;
@endphp

<li class="fi-sidebar-item custom-sidebar-item" data-level="{{ $level }}">
    @if($hasChildren)
        <div 
            x-data="{ 
                open: $persist({{ $shouldBeOpen ? 'true' : 'false' }}).as('{{ $itemId }}')
            }"
            class="fi-sidebar-group"
        >
            <button
                type="button"
                @click="open = !open"
                class="fi-sidebar-group-label flex w-full items-center gap-x-3 rounded-lg px-2 py-2 text-sm font-semibold leading-6 transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-800 {{ $isActive ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 dark:text-gray-200' }}"
            >
                @if($icon)
                    <x-filament::icon :icon="$icon" class="h-5 w-5 shrink-0" />
                @endif
                <span class="flex-1 text-start">{{ $title }}</span>
                @if($badge !== null && $badge > 0)
                    <span class="fi-badge flex h-5 min-w-[1.25rem] items-center justify-center gap-x-1 rounded-md bg-primary-500 px-1.5 text-xs font-medium text-white">
                        {{ $badge }}
                    </span>
                @endif
                <x-filament::icon 
                    icon="heroicon-m-chevron-down" 
                    class="h-4 w-4 transition-transform duration-200"
                    x-bind:class="open ? 'rotate-180' : ''"
                />
            </button>
            
            <ul 
                x-show="open"
                x-collapse
                class="fi-sidebar-group-items mt-1 space-y-1 ps-7"
            >
                @foreach($item['children'] as $child)
                    <x-sidebar.item :item="$child" :level="$level + 1" />
                @endforeach
            </ul>
        </div>
    @else
        <a
            href="{{ $url ?? '#' }}"
            class="fi-sidebar-item-link group flex items-center gap-x-3 rounded-lg px-2 py-2 text-sm font-medium leading-6 transition duration-75 {{ $isActive ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800' }}"
        >
            @if($icon)
                <x-filament::icon :icon="$icon" class="h-5 w-5 shrink-0" />
            @endif
            <span class="flex-1">{{ $title }}</span>
            @if($badge !== null && $badge > 0)
                <span class="fi-badge flex h-5 min-w-[1.25rem] items-center justify-center gap-x-1 rounded-md bg-primary-500 px-1.5 text-xs font-medium text-white">
                    {{ $badge }}
                </span>
            @endif
        </a>
    @endif
</li>
