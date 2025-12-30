@php
    $statePath = $getStatePath();
    $rows = $getState() ?? [];
    if (is_object($rows) || !is_array($rows)) {
        $rows = [];
    }
    $columns = $getGridColumns();
    $isRTL = app()->getLocale() === 'ar';
    $allowAddRows = $getAllowAddRows();
    $allowDeleteRows = $getAllowDeleteRows();
    $allowQuickAdd = $getAllowQuickAdd();
    $quickAddCount = $getQuickAddCount();
    $totalDebitColumn = $getTotalDebitColumn();
    $totalCreditColumn = $getTotalCreditColumn();
    $differenceColumn = $getDifferenceColumn();

    // Get account options
    $accountColumn = collect($columns)->firstWhere('name', 'account_id');
    $accountOptions = $accountColumn['options'] ?? [];

    // Get currency options
    $currencyColumn = collect($columns)->firstWhere('name', 'currency_id');
    $currencyOptions = $currencyColumn['options'] ?? [];

    // Get cost center options
    $costCenterColumn = collect($columns)->firstWhere('name', 'cost_center_id');
    $costCenterOptions = $costCenterColumn['options'] ?? [];

    // Get project options
    $projectColumn = collect($columns)->firstWhere('name', 'project_id');
    $projectOptions = $projectColumn['options'] ?? [];

    $defaultCurrency = app(\App\Services\MainCore\CurrencyService::class)->defaultCurrency();
    $defaultCurrencyId = $defaultCurrency?->id;
    $defaultCurrencyCode = $defaultCurrency?->code ?? 'USD';
@endphp

<div
    x-data="journalEntryCards({
        statePath: @js($statePath),
        rows: @js($rows ?? []),
        allowAddRows: @js($allowAddRows),
        allowDeleteRows: @js($allowDeleteRows),
        allowQuickAdd: @js($allowQuickAdd),
        quickAddCount: @js($quickAddCount),
        accountOptions: @js($accountOptions),
        currencyOptions: @js($currencyOptions),
        costCenterOptions: @js($costCenterOptions),
        projectOptions: @js($projectOptions),
        defaultCurrencyId: @js($defaultCurrencyId),
        defaultCurrencyCode: @js($defaultCurrencyCode),
        isRTL: @js($isRTL),
    })"
    class="journal-entry-cards"
    :dir="$isRTL ? 'rtl' : 'ltr'"
>
    <!-- Global Currency Selector -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
        <div class="flex items-center gap-4">
            <label class="text-sm font-semibold text-gray-700 whitespace-nowrap">
                {{ trans_dash('accounting.global_currency', 'Global Currency for All Entries') }}:
            </label>
            <div class="flex-1 max-w-xs relative">
                <select
                    x-model="globalCurrencyId"
                    @change="applyGlobalCurrency()"
                    :disabled="loadingStates.exchangeRates"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <option value="">{{ trans_dash('accounting.select_currency', 'Select Currency') }}</option>
                    <template x-for="opt in currencyOptions" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                </select>
                <div x-show="loadingStates.exchangeRates" class="absolute right-2 top-1/2 transform -translate-y-1/2">
                    <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-xs text-gray-600">
                <span x-show="globalCurrencyId && !loadingStates.exchangeRates">{{ trans_dash('accounting.applied_to_all', 'Applied to all entries') }}</span>
                <span x-show="loadingStates.exchangeRates" class="text-blue-600">{{ trans_dash('accounting.loading_rates', 'Loading exchange rates...') }}</span>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="flex gap-2 mb-4 flex-wrap">
        @if($allowAddRows)
            <button
                type="button"
                @click="addRow()"
                class="filament-button filament-button-size-sm filament-button-color-primary inline-flex items-center justify-center gap-1"
            >
                <x-heroicon-o-plus class="w-4 h-4" />
                <span>{{ trans_dash('accounting.add_row', 'Add Row') }}</span>
            </button>
        @endif

        @if($allowQuickAdd)
            <button
                type="button"
                @click="addMultipleRows()"
                class="filament-button filament-button-size-sm filament-button-color-success inline-flex items-center justify-center gap-1"
            >
                <x-heroicon-o-plus class="w-4 h-4" />
                <span>{{ trans_dash('accounting.add_rows', 'Add :count Rows', ['count' => $quickAddCount]) }}</span>
            </button>
        @endif
    </div>

    <!-- Cards Container -->
    <div class="space-y-4">
        <template x-for="(row, index) in rows" :key="index">
            <div x-show="row && rows[index] && typeof rows[index] === 'object'" class="bg-white rounded-lg shadow-md border border-gray-200 p-4 hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700" x-text="`{{ trans_dash('accounting.entry_line', 'Entry Line') }} #${index + 1}`"></h3>
                    @if($allowDeleteRows)
                        <button
                            type="button"
                            @click="deleteRow(index)"
                            class="text-red-600 hover:text-red-800 p-1"
                            title="{{ trans_dash('accounting.delete', 'Delete') }}"
                        >
                            <x-heroicon-o-trash class="w-5 h-5" />
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Debit Card -->
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-bold text-green-800 uppercase">
                                {{ trans_dash('accounting.debit', 'Debit') }}
                            </label>
                            <span class="text-xs text-green-600" x-show="row && rows[index] && (rows[index].debit || 0) > 0" x-text="formatMoney((rows[index] && rows[index].base_amount) || (rows[index] && rows[index].debit) || 0)"></span>
                        </div>

                        <div class="space-y-3">
                            <!-- Account -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    {{ trans_dash('accounting.account', 'Account') }} *
                                </label>
                                <select
                                    x-model="rows[index].account_id"
                                    @change="syncToLivewire()"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                    required
                                >
                                    <option value="">{{ trans_dash('accounting.select_account', 'Select Account') }}</option>
                                    <template x-for="opt in accountOptions" :key="opt.value">
                                        <option :value="opt.value" x-text="opt.label"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Debit Amount -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    {{ trans_dash('accounting.amount', 'Amount') }}
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    x-model.number="rows[index].debit"
                                    @input="onDebitChange(index)"
                                    @blur="syncToLivewire()"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm font-mono text-right"
                                    placeholder="0.00"
                                    min="0"
                                />
                            </div>

                            <!-- Currency & Exchange Rate -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        {{ trans_dash('accounting.currency', 'Currency') }}
                                    </label>
                                    <select
                                        x-model="rows[index].currency_id"
                                        @change="onCurrencyChange(index)"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                    >
                                        <option value="">{{ trans_dash('accounting.select_currency', 'Select Currency') }}</option>
                                        <template x-for="opt in currencyOptions" :key="opt.value">
                                            <option :value="opt.value" x-text="opt.label"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        {{ trans_dash('accounting.exchange_rate', 'Exchange Rate') }}
                                    </label>
                                    <input
                                        type="number"
                                        step="0.000001"
                                        x-model.number="rows[index].exchange_rate"
                                        @input="onExchangeRateChange(index)"
                                    @blur="syncToLivewire()"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-mono text-right"
                                        placeholder="1.00"
                                        min="0"
                                    />
                                </div>
                            </div>

                            <!-- Base Amount (Read-only) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    {{ trans_dash('accounting.amount_in_base', 'Amount in Base Currency') }}
                                </label>
                                <input
                                    type="number"
                                    x-model.number="rows[index].base_amount"
                                    readonly
                                    class="w-full rounded-md border-gray-300 bg-gray-100 text-sm font-mono text-right cursor-not-allowed"
                                    placeholder="0.00"
                                />
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    {{ trans_dash('accounting.description', 'Description') }}
                                </label>
                                <input
                                    type="text"
                                    x-model="rows[index].description"
                                    @blur="syncToLivewire()"
                                    @input="validate()"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                    placeholder="{{ trans_dash('accounting.description_placeholder', 'Enter description') }}"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Credit Card -->
                    <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-bold text-red-800 uppercase">
                                {{ trans_dash('accounting.credit', 'Credit') }}
                            </label>
                            <span class="text-xs text-red-600" x-show="row && rows[index] && (rows[index].credit || 0) > 0" x-text="formatMoney((rows[index] && rows[index].credit_base_amount) || (rows[index] && rows[index].credit) || 0)"></span>
                        </div>

                        <div class="space-y-3">
                            <!-- Account -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    {{ trans_dash('accounting.account', 'Account') }} *
                                </label>
                                <select
                                    x-model="rows[index].credit_account_id"
                                    @change="syncToLivewire()"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                >
                                    <option value="">{{ trans_dash('accounting.select_account', 'Select Account') }}</option>
                                    <template x-for="opt in accountOptions" :key="opt.value">
                                        <option :value="opt.value" x-text="opt.label"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Credit Amount -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    {{ trans_dash('accounting.amount', 'Amount') }}
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    x-model.number="rows[index].credit"
                                    @input="onCreditChange(index)"
                                    @blur="syncToLivewire()"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm font-mono text-right"
                                    placeholder="0.00"
                                    min="0"
                                />
                            </div>

                            <!-- Currency & Exchange Rate -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        {{ trans_dash('accounting.currency', 'Currency') }}
                                    </label>
                                    <select
                                        x-model="rows[index].credit_currency_id"
                                        @change="onCreditCurrencyChange(index)"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                    >
                                        <option value="">{{ trans_dash('accounting.select_currency', 'Select Currency') }}</option>
                                        <template x-for="opt in currencyOptions" :key="opt.value">
                                            <option :value="opt.value" x-text="opt.label"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        {{ trans_dash('accounting.exchange_rate', 'Exchange Rate') }}
                                    </label>
                                    <input
                                        type="number"
                                        step="0.000001"
                                        x-model.number="rows[index].credit_exchange_rate"
                                        @input="onCreditExchangeRateChange(index)"
                                    @blur="syncToLivewire()"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm font-mono text-right"
                                        placeholder="1.00"
                                        min="0"
                                    />
                                </div>
                            </div>

                            <!-- Base Amount (Read-only) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    {{ trans_dash('accounting.amount_in_base', 'Amount in Base Currency') }}
                                </label>
                                <input
                                    type="number"
                                    x-model.number="rows[index].credit_base_amount"
                                    readonly
                                    class="w-full rounded-md border-gray-300 bg-gray-100 text-sm font-mono text-right cursor-not-allowed"
                                    placeholder="0.00"
                                />
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    {{ trans_dash('accounting.description', 'Description') }}
                                </label>
                                <input
                                    type="text"
                                    x-model="rows[index].credit_description"
                                    @blur="syncToLivewire()"
                                    @input="validate()"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                    placeholder="{{ trans_dash('accounting.description_placeholder', 'Enter description') }}"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Common Fields Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200">
                    <!-- Cost Center -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            {{ trans_dash('accounting.cost_center', 'Cost Center') }}
                        </label>
                        <select
                            x-model="rows[index].cost_center_id"
                            @change="updateState()"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                        >
                            <option value="">{{ trans_dash('accounting.select_cost_center', 'Select Cost Center') }}</option>
                            <template x-for="opt in costCenterOptions" :key="opt.value">
                                <option :value="opt.value" x-text="opt.label"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Project -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            {{ trans_dash('accounting.project', 'Project') }}
                        </label>
                        <select
                            x-model="rows[index].project_id"
                            @change="updateState()"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                        >
                            <option value="">{{ trans_dash('accounting.select_project', 'Select Project') }}</option>
                            <template x-for="opt in projectOptions" :key="opt.value">
                                <option :value="opt.value" x-text="opt.label"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Reference -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            {{ trans_dash('accounting.reference', 'Reference') }}
                        </label>
                        <input
                            type="text"
                            x-model="rows[index].reference"
                            @blur="syncToLivewire()"
                            @input="validate()"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                            placeholder="{{ trans_dash('accounting.reference_placeholder', 'Reference number') }}"
                        />
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Summary Footer -->
    <div class="mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <div class="text-sm font-medium text-gray-600 mb-1">
                    {{ trans_dash('accounting.total_debit', 'Total Debit') }}
                </div>
                <div class="text-2xl font-bold text-green-700" x-text="formatMoney(totalDebits)"></div>
            </div>
            <div class="text-center">
                <div class="text-sm font-medium text-gray-600 mb-1">
                    {{ trans_dash('accounting.total_credit', 'Total Credit') }}
                </div>
                <div class="text-2xl font-bold text-red-700" x-text="formatMoney(totalCredits)"></div>
            </div>
            <div class="text-center">
                <div class="text-sm font-medium text-gray-600 mb-1">
                    {{ trans_dash('accounting.difference', 'Difference') }}
                </div>
                <div
                    class="text-2xl font-bold transition-colors duration-200"
                    :class="Math.abs(difference) < 0.01 ? 'text-green-600' : 'text-red-600'"
                    x-text="formatMoney(difference)"
                ></div>
                <div x-show="Math.abs(difference) > 0.01" class="text-xs text-red-600 mt-1 font-semibold animate-pulse">
                    {{ trans_dash('accounting.entries_not_balanced', 'Entries are not balanced') }}
                </div>
                <div x-show="Math.abs(difference) < 0.01" class="text-xs text-green-600 mt-1">
                    {{ trans_dash('accounting.entries_balanced', 'Entries are balanced') }}
                </div>
            </div>
        </div>
        <div x-show="loadingStates.syncing" class="mt-4 text-center text-sm text-gray-500">
            <svg class="animate-spin h-4 w-4 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ trans_dash('accounting.syncing', 'Syncing...') }}
        </div>
    </div>

    <!-- Error Messages -->
    <div x-show="errors.length > 0"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         class="mt-4 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-red-800 mb-2">
                    {{ trans_dash('accounting.validation_errors', 'Validation Errors') }}
                </h3>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    <template x-for="(error, index) in errors" :key="index">
                        <li x-text="error"></li>
                    </template>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Load the journal entry cards component if not already loaded
    if (typeof window.journalEntryCards === 'undefined') {
        // Inline the component for now - can be moved to external file later
        {!! file_get_contents(resource_path('js/journal-entry-cards.js')) !!}
    }
</script>
@endpush

@push('styles')
<style>
.journal-entry-cards {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

[dir="rtl"] .journal-entry-cards {
    direction: rtl;
}

[dir="rtl"] .journal-entry-cards input[type="number"] {
    direction: ltr;
    text-align: right;
}
</style>
@endpush

