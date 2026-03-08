<x-filament-panels::page class="rtl-dashboard">
    <div class="space-y-6">
        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">{{ tr('complaint.report.filters', [], null, 'dashboard') ?: 'فلترة التقرير' }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="branch_id">
                        <option value="">{{ tr('filters.branch', [], null, 'dashboard') ?: 'الفرع' }}</option>
                        @foreach($this->getBranches() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="from_date" />
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="to_date" />
                </x-filament::input.wrapper>
            </div>
        </div>

        {{-- Stats Cards --}}
        @php
            $stats = $this->getStats();
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Resolved Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                            <x-heroicon-o-check-circle class="w-8 h-8 text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">{{ tr('complaint.status.resolved', [], null, 'dashboard') ?: 'تم الحل' }}</h3>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['resolved']['count'] }}</p>
                        </div>
                    </div>
                </div>
                
                @if($stats['resolved']['details']->count() > 0)
                    <div class="mt-4 space-y-2">
                        @foreach($stats['resolved']['details']->take(5) as $complaint)
                            <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                <p class="text-sm font-medium">{{ $complaint->complaint_no }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $complaint->branch->name ?? '-' }}</p>
                            </div>
                        @endforeach
                        @if($stats['resolved']['details']->count() > 5)
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                                +{{ $stats['resolved']['details']->count() - 5 }} {{ tr('common.more', [], null, 'dashboard') ?: 'أكثر' }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- In Progress Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                            <x-heroicon-o-clock class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">{{ tr('complaint.status.in_progress', [], null, 'dashboard') ?: 'قيد المعالجة' }}</h3>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['in_progress']['count'] }}</p>
                        </div>
                    </div>
                </div>
                
                @if($stats['in_progress']['details']->count() > 0)
                    <div class="mt-4 space-y-2">
                        @foreach($stats['in_progress']['details']->take(5) as $complaint)
                            <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                <p class="text-sm font-medium">{{ $complaint->complaint_no }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $complaint->branch->name ?? '-' }}</p>
                            </div>
                        @endforeach
                        @if($stats['in_progress']['details']->count() > 5)
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                                +{{ $stats['in_progress']['details']->count() - 5 }} {{ tr('common.more', [], null, 'dashboard') ?: 'أكثر' }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
