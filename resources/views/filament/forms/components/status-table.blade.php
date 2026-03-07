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
        updateStatus(status) {
            this.currentStatus = status;
            $wire.set('{{ $statusStatePath }}', status);
            
            // Update status_date if exists in statusDates
            if (this.statusDates[status]) {
                $wire.set('{{ $statusDateStatePath }}', this.statusDates[status]);
            } else {
                const today = new Date().toISOString().split('T')[0];
                $wire.set('{{ $statusDateStatePath }}', today);
            }
        }
    }"
    class="fi-input-wrp"
>
    <div class="fi-input-wrp-label">
        <label class="fi-input-label text-sm font-medium leading-6 text-gray-950 dark:text-white">
            {{ tr('recruitment_contract.fields.status', [], null, 'dashboard') ?: 'الحالة' }}
            <span class="text-danger-600 dark:text-danger-400">*</span>
        </label>
    </div>
    
    <div class="mt-2 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        الحالة
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        التاريخ
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                @foreach($statuses as $status => $label)
                    @php
                        $date = $statusDates[$status] ?? null;
                    @endphp
                    <tr 
                        class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800"
                        x-bind:class="{ 'bg-primary-50 dark:bg-primary-900/20': currentStatus === '{{ $status }}' }"
                    >
                        <td class="px-4 py-3 whitespace-nowrap">
                            <label class="flex items-center cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="{{ $statusStatePath }}" 
                                    value="{{ $status }}"
                                    x-model="currentStatus"
                                    x-on:change="updateStatus('{{ $status }}')"
                                    class="fi-radio-input h-4 w-4 border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:checked:border-primary-500 dark:checked:bg-primary-600 dark:focus:ring-offset-gray-900 dark:focus:ring-primary-600"
                                />
                                <span class="mr-2 text-sm text-gray-900 dark:text-gray-100">{{ $label }}</span>
                            </label>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($date)
                                {{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}
                            @else
                                <span class="text-gray-400 dark:text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
