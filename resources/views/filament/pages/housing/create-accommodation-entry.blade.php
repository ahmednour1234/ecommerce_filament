<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="flex justify-end gap-2 mt-6">
            <x-filament::button type="submit" color="primary">
                {{ tr('housing.actions.save', [], null, 'dashboard') ?: 'حفظ' }}
            </x-filament::button>
            <x-filament::button type="button" wire:click="$dispatch('close-modal')" color="gray">
                {{ tr('housing.actions.cancel', [], null, 'dashboard') ?: 'إلغاء' }}
            </x-filament::button>
        </div>
    </form>

    {{-- ── Contract Details Modal ─────────────────────────────────── --}}
    @if($this->showContractModal)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-data
        x-init="$el.classList.add('animate-fade-in')"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-black/50"
            wire:click="closeContractModal"
        ></div>

        {{-- Panel --}}
        <div class="relative z-10 w-full max-w-3xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    بيانات العقد — {{ $this->contractDetails['contract_no'] ?? '' }}
                </h2>
                <button
                    wire:click="closeContractModal"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
                >
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto max-h-[70vh] px-6 py-5 space-y-6 text-sm">

                @php $d = $this->contractDetails; @endphp

                {{-- بيانات العميل --}}
                <div>
                    <h3 class="font-semibold text-primary-600 dark:text-primary-400 mb-3 pb-1 border-b border-gray-100 dark:border-gray-700">بيانات العميل</h3>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                        @foreach([
                            'اسم العميل'    => $d['client_name'],
                            'رقم الهوية'    => $d['client_national_id'],
                            'الجوال'        => $d['client_mobile'],
                        ] as $label => $value)
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $value ?? '—' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- بيانات العاملة --}}
                <div>
                    <h3 class="font-semibold text-primary-600 dark:text-primary-400 mb-3 pb-1 border-b border-gray-100 dark:border-gray-700">بيانات العاملة</h3>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                        @foreach([
                            'الاسم'         => $d['worker_name'],
                            'رقم الجواز'    => $d['worker_passport'],
                            'الجنسية'       => $d['worker_nationality'],
                            'المهنة'        => $d['worker_profession'],
                            'الجنس'         => $d['worker_gender'],
                        ] as $label => $value)
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $value ?? '—' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- بيانات العقد --}}
                <div>
                    <h3 class="font-semibold text-primary-600 dark:text-primary-400 mb-3 pb-1 border-b border-gray-100 dark:border-gray-700">بيانات العقد</h3>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                        @foreach([
                            'رقم العقد'                    => $d['contract_no'],
                            'الحالة'                       => $d['status'],
                            'تاريخ الطلب'                 => $d['gregorian_request_date'],
                            'تاريخ الوصول'                => $d['arrival_date'],
                            'تاريخ انتهاء التجربة'         => $d['trial_end_date'],
                            'تاريخ انتهاء العقد'           => $d['contract_end_date'],
                            'الراتب الشهري'               => $d['monthly_salary'],
                            'إجمالي التكلفة'              => $d['total_cost'],
                            'المدفوع'                     => $d['paid_total'],
                            'المتبقي'                     => $d['remaining_total'],
                        ] as $label => $value)
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $value ?? '—' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- بيانات مساند --}}
                <div>
                    <h3 class="font-semibold text-primary-600 dark:text-primary-400 mb-3 pb-1 border-b border-gray-100 dark:border-gray-700">بيانات مساند</h3>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                        @foreach([
                            'رقم عقد مساند'               => $d['musaned_contract_no'],
                            'رقم عقد التوثيق'             => $d['musaned_documentation_contract_no'],
                            'رقم التفويض'                 => $d['musaned_auth_no'],
                            'تاريخ عقد مساند'             => $d['musaned_contract_date'],
                            'رقم التأشيرة'                => $d['visa_no'],
                            'تاريخ التأشيرة'              => $d['visa_date'],
                        ] as $label => $value)
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $value ?? '—' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <x-filament::button color="gray" wire:click="closeContractModal">إغلاق</x-filament::button>
            </div>
        </div>
    </div>
    @endif

</x-filament-panels::page>
