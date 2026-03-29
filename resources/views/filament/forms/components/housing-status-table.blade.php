@php
    $statuses = $statuses ?? [];
    $statusDates = $statusDates ?? [];
    $statusDurations = $statusDurations ?? [];
    $currentStatus = $currentStatus ?? '';
    $selectedStatuses = $selectedStatuses ?? [];
    $statusStatePath = $statusStatePath ?? 'data.status_key';
    $statusDateStatePath = $statusDateStatePath ?? 'data.status_date';
    $statusKeysStatePath = $statusKeysStatePath ?? 'data.status_keys';
    $existingAttachments = $existingAttachments ?? [];
    $readonly = $readonly ?? false;
@endphp

<div
    x-data="{
        currentStatus: @js($currentStatus),
        selectedStatuses: @js($selectedStatuses),
        statusDates: @js($statusDates),
        existingAttachments: @js($existingAttachments),
        newAttachments: {},
        uploading: {},
        init() {
            if (!Array.isArray(this.selectedStatuses)) {
                this.selectedStatuses = [];
            }

            if (this.currentStatus && !this.selectedStatuses.includes(this.currentStatus)) {
                this.selectedStatuses.push(this.currentStatus);
            }

            @foreach($statuses as $status => $label)
                if (!this.statusDates['{{ $status }}']) {
                    this.statusDates['{{ $status }}'] = '';
                }
            @endforeach

            if (!this.currentStatus && this.selectedStatuses.length > 0) {
                this.currentStatus = this.selectedStatuses[this.selectedStatuses.length - 1];
            }

            this.syncAllDates();
        },
        toggleStatus(status, isChecked) {
            if (isChecked) {
                if (!this.selectedStatuses.includes(status)) {
                    this.selectedStatuses.push(status);
                }

                this.currentStatus = status;

                if (!this.statusDates[status]) {
                    this.statusDates[status] = new Date().toISOString().split('T')[0];
                }
            } else {
                this.selectedStatuses = this.selectedStatuses.filter((key) => key !== status);

                if (this.currentStatus === status) {
                    this.currentStatus = this.selectedStatuses.length
                        ? this.selectedStatuses[this.selectedStatuses.length - 1]
                        : '';
                }
            }

            this.syncAllDates();
        },
        updateDate(status, date) {
            this.statusDates[status] = date;
            if (this.currentStatus === status) {
                $wire.set('{{ $statusDateStatePath }}', date);
            }
            this.syncAllDates();
        },
        syncAllDates() {
            const selectedSet = new Set(this.selectedStatuses);
            const filteredDates = {};

            Object.entries(this.statusDates).forEach(([status, date]) => {
                if (selectedSet.has(status) && date) {
                    filteredDates[status] = date;
                }
            });

            if (this.currentStatus && !selectedSet.has(this.currentStatus)) {
                this.currentStatus = this.selectedStatuses.length
                    ? this.selectedStatuses[this.selectedStatuses.length - 1]
                    : '';
            }

            $wire.set('{{ $statusStatePath }}', this.currentStatus || null);
            $wire.set('{{ $statusDateStatePath }}', this.currentStatus ? (this.statusDates[this.currentStatus] || null) : null);
            $wire.set('{{ $statusKeysStatePath }}', [...this.selectedStatuses]);
            $wire.set('data.all_status_dates', JSON.stringify(this.statusDates));
        }
    }"
    class="fi-input-wrp"
    x-on:submit.window="syncAllDates()"
>
    <div class="fi-input-wrp-label">
        <label class="fi-input-label text-sm font-medium leading-6 text-gray-950 dark:text-white">
            الحالة
        </label>
    </div>

    <div class="mt-1.5 overflow-hidden rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm">
        <div class="flex items-center gap-2 px-2 py-1.5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-xs font-medium text-gray-600 dark:text-gray-400">
            <div class="w-5 shrink-0"></div>
            <span class="flex-1">الحالة</span>
            <span class="w-36 shrink-0">التاريخ</span>
            <span class="w-24 shrink-0 text-center">مرفق PDF</span>
        </div>
        @foreach($statuses as $status => $label)
            <div
                class="flex items-center gap-2 px-2 py-1.5 border-b border-gray-200 last:border-b-0 dark:border-gray-700 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                x-bind:class="{ 'bg-primary-50 dark:bg-primary-900/20': selectedStatuses.includes('{{ $status }}') }"
            >
                <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border text-xs font-medium transition-colors"
                    :class="selectedStatuses.includes('{{ $status }}') ? 'border-primary-600 bg-primary-600 text-white dark:border-primary-500 dark:bg-primary-500' : 'border-gray-300 bg-white text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400'"
                >{{ $loop->iteration }}</div>
                <label class="flex min-w-0 flex-1 items-center gap-1.5 cursor-pointer">
                    <input
                        type="checkbox"
                        value="{{ $status }}"
                        :disabled="@js($readonly)"
                        :checked="selectedStatuses.includes('{{ $status }}')"
                        x-on:change="toggleStatus('{{ $status }}', $event.target.checked)"
                        class="fi-radio-input h-3.5 w-3.5 shrink-0 border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:checked:border-primary-500 dark:checked:bg-primary-600"
                    />
                    <span class="truncate text-gray-900 dark:text-gray-100">{{ $label }}</span>
                </label>
                <input
                    type="date"
                    x-model="statusDates['{{ $status }}']"
                    x-on:change="updateDate('{{ $status }}', $event.target.value)"
                    :disabled="@js($readonly) || !selectedStatuses.includes('{{ $status }}')"
                    class="fi-input w-36 shrink-0 rounded border border-gray-300 bg-white px-2 py-1 text-gray-950 outline-none transition placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:border-gray-600 dark:bg-white/5 dark:text-white dark:placeholder:text-gray-500 dark:focus:ring-primary-500 text-xs"
                />
                {{-- PDF attachment upload per status --}}
                <div class="w-24 shrink-0 flex items-center justify-center gap-1"
                    x-show="selectedStatuses.includes('{{ $status }}')"
                >
                    <template x-if="uploading['{{ $status }}']">
                        <span class="text-xs text-gray-400">جاري الرفع...</span>
                    </template>
                    <template x-if="!uploading['{{ $status }}'] && newAttachments['{{ $status }}']">
                        <span class="text-xs text-green-600 font-medium">✓ تم الرفع</span>
                    </template>
                    <template x-if="!uploading['{{ $status }}'] && !newAttachments['{{ $status }}'] && existingAttachments['{{ $status }}']">
                        <a :href="'/storage/' + existingAttachments['{{ $status }}']" target="_blank"
                           class="text-xs text-primary-600 underline hover:text-primary-800">عرض</a>
                    </template>
                    @if(!$readonly)
                    <label class="cursor-pointer" :class="{ 'opacity-40 pointer-events-none': uploading['{{ $status }}'] }">
                        <span class="text-xs border border-gray-300 rounded px-1.5 py-0.5 hover:border-primary-500 hover:text-primary-600 transition-colors select-none">رفع</span>
                        <input
                            type="file"
                            accept="application/pdf,image/*"
                            class="hidden"
                            :disabled="uploading['{{ $status }}']"
                            x-on:change="
                                const file = $event.target.files[0];
                                if (file) {
                                    uploading['{{ $status }}'] = true;
                                    $wire.upload(
                                        'statusPdfs.{{ $status }}',
                                        file,
                                        () => { uploading['{{ $status }}'] = false; newAttachments['{{ $status }}'] = true; },
                                        () => { uploading['{{ $status }}'] = false; }
                                    );
                                }
                            "
                        />
                    </label>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
