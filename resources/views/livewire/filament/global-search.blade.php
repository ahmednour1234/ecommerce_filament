<div
    x-data="{
        isOpen: @entangle('isOpen'),
        selectedIndex: @entangle('selectedIndex'),
        query: @entangle('query'),
        results: @entangle('results'),
        init() {
            this.$watch('isOpen', (value) => {
                if (value) {
                    this.$nextTick(() => {
                        this.scrollToSelected();
                    });
                }
            });
            this.$watch('selectedIndex', () => {
                this.scrollToSelected();
            });
        },
        scrollToSelected() {
            if (this.selectedIndex >= 0) {
                const element = this.$refs[`result-${this.selectedIndex}`];
                if (element) {
                    element.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                }
            }
        },
        handleKeydown(event) {
            if (!this.isOpen && this.results.length > 0 && event.key !== 'Escape') {
                this.isOpen = true;
            }
            
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                @this.call('moveSelection', 1);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                @this.call('moveSelection', -1);
            } else if (event.key === 'Enter' && this.selectedIndex >= 0) {
                event.preventDefault();
                @this.call('selectResult', this.selectedIndex);
            } else if (event.key === 'Escape') {
                @this.call('close');
            }
        }
    }"
    class="relative"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
>
    <div class="relative w-full max-w-md">
        <div class="relative flex items-center">
            <div class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="query"
                @keydown="handleKeydown($event)"
                @focus="isOpen = results.length > 0"
                @click.away="isOpen = false"
                placeholder="{{ tr('search.placeholder', [], null, 'dashboard') ?: (app()->getLocale() === 'ar' ? 'بحث...' : 'Search...') }}"
                class="block w-full {{ app()->getLocale() === 'ar' ? 'pr-10' : 'pl-10' }} {{ $query ? (app()->getLocale() === 'ar' ? 'pl-10' : 'pr-10') : '' }} py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:border-primary-400 sm:text-sm"
            />
            @if($query)
                <button
                    type="button"
                    wire:click="clear"
                    class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center hover:opacity-70 transition-opacity"
                >
                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        @if($isOpen && !empty($results))
            <div
                x-show="isOpen"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute z-50 mt-1 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} w-[480px] rounded-lg shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden"
                style="max-height: 400px; overflow-y: auto;"
            >
                @php
                    $groupedResults = collect($results)->groupBy('group');
                @endphp
                @foreach($groupedResults as $group => $groupResults)
                    <div>
                        <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                            {{ $group }}
                        </div>
                        @foreach($groupResults as $index => $result)
                            @php
                                $globalIndex = collect($results)->search(function($r) use ($result) {
                                    return $r === $result;
                                });
                            @endphp
                            <a
                                href="{{ $result['url'] }}"
                                wire:key="result-{{ $globalIndex }}"
                                x-ref="result-{{ $globalIndex }}"
                                @click.prevent="$wire.selectResult({{ $globalIndex }})"
                                class="flex items-start gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-gray-700 transition-colors {{ $selectedIndex === $globalIndex ? 'bg-primary-50 dark:bg-gray-700' : '' }} {{ app()->getLocale() === 'ar' ? 'flex-row-reverse text-right' : 'text-left' }}"
                            >
                                @if($result['icon'])
                                    <x-filament::icon
                                        :icon="$result['icon']"
                                        class="h-5 w-5 flex-shrink-0 mt-0.5 text-gray-400 dark:text-gray-500"
                                    />
                                @else
                                    <svg class="h-5 w-5 flex-shrink-0 mt-0.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium truncate">{{ $result['title'] }}</div>
                                    @if($result['subtitle'])
                                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $result['subtitle'] }}</div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @elseif($isOpen && empty($results) && $query)
            <div
                x-show="isOpen"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute z-50 mt-1 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} w-[480px] rounded-lg shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ app()->getLocale() === 'ar' ? 'لا توجد نتائج' : (tr('search.no_results', [], null, 'dashboard') ?: 'No results found') }}
                </div>
            </div>
        @endif
    </div>
</div>
