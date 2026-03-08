@php
    $statuses = $statuses ?? [];
    $statusDates = $statusDates ?? [];
    $currentStatus = $currentStatus ?? 'new';
    $statusStatePath = $statusStatePath ?? 'data.status';
    $statusDateStatePath = $statusDateStatePath ?? 'data.status_date';
@endphp

<div 
    x-data="{
        currentStatus: @js($currentStatus),
        statusDates: @js($statusDates),
        init() {
            // Initialize statusDates for all statuses if not set
            @foreach($statuses as $status => $label)
                if (!this.statusDates['{{ $status }}']) {
                    this.statusDates['{{ $status }}'] = '';
                }
            @endforeach
            // Sync initial dates
            this.syncAllDates();
        },
        updateStatus(status) {
            this.currentStatus = status;
            $wire.set('{{ $statusStatePath }}', status);
            
            // Update status_date if exists in statusDates
            if (this.statusDates[status]) {
                $wire.set('{{ $statusDateStatePath }}', this.statusDates[status]);
            } else {
                const today = new Date().toISOString().split('T')[0];
                this.statusDates[status] = today;
                $wire.set('{{ $statusDateStatePath }}', today);
            }
            this.syncAllDates();
        },
        updateDate(status, date) {
            this.statusDates[status] = date;
            if (this.currentStatus === status) {
                $wire.set('{{ $statusDateStatePath }}', date);
            }
            // Update all_status_dates hidden field
            $wire.set('data.all_status_dates', JSON.stringify(this.statusDates));
        },
        syncAllDates() {
            // Sync all dates to hidden field
            $wire.set('data.all_status_dates', JSON.stringify(this.statusDates));
        }
    }"
    class="fi-input-wrp"
    x-on:submit.window="syncAllDates()"
>
    <div class="fi-input-wrp-label">
        <label class="fi-input-label text-sm font-medium leading-6 text-gray-950 dark:text-white">
            {{ tr('recruitment_contract.fields.status', [], null, 'dashboard') ?: 'الحالة' }}
            <span class="text-danger-600 dark:text-danger-400">*</span>
        </label>
    </div>
    
    <div class="mt-2 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
        @foreach($statuses as $status => $label)
            @php $stepNum = $loop->iteration; @endphp
            <div
                class="flex items-start gap-4 px-4 py-3 border-b border-gray-200 last:border-b-0 dark:border-gray-700 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                x-bind:class="{ 'bg-primary-50 dark:bg-primary-900/20': currentStatus === '{{ $status }}' }"
            >
                <div class="flex flex-col items-center shrink-0">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full border-2 text-sm font-medium transition-colors"
                        :class="currentStatus === '{{ $status }}' ? 'border-primary-600 bg-primary-600 text-white dark:border-primary-500 dark:bg-primary-500' : 'border-gray-300 bg-white text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400'"
                    >{{ $stepNum }}</div>
                    @if(!$loop->last)
                        <div class="w-0.5 flex-1 min-h-[24px] bg-gray-200 dark:bg-gray-600 my-0.5"></div>
                    @endif
                </div>
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3 min-w-0 pb-2">
                    <label class="flex items-center cursor-pointer">
                        <input
                            type="radio"
                            name="{{ $statusStatePath }}"
                            value="{{ $status }}"
                            x-model="currentStatus"
                            x-on:change="updateStatus('{{ $status }}')"
                            class="fi-radio-input h-4 w-4 border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:checked:border-primary-500 dark:checked:bg-primary-600"
                        />
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $label }}</span>
                    </label>
                    <div>
                        <input
                            type="date"
                            x-model="statusDates['{{ $status }}']"
                            x-on:change="updateDate('{{ $status }}', $event.target.value)"
                            class="fi-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:border-gray-600 dark:bg-white/5 dark:text-white dark:placeholder:text-gray-500 dark:focus:ring-primary-500 sm:text-sm"
                        />
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
