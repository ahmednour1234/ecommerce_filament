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
            <select 
                x-model="globalCurrencyId"
                @change="applyGlobalCurrency()"
                class="flex-1 max-w-xs rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
            >
                <option value="">{{ trans_dash('accounting.select_currency', 'Select Currency') }}</option>
                <template x-for="opt in currencyOptions" :key="opt.value">
                    <option :value="opt.value" x-text="opt.label"></option>
                </template>
            </select>
            <div class="text-xs text-gray-600">
                <span x-show="globalCurrencyId">{{ trans_dash('accounting.applied_to_all', 'Applied to all entries') }}</span>
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
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 hover:shadow-lg transition-shadow">
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
                            <span class="text-xs text-green-600" x-show="rows[index].debit > 0" x-text="formatMoney(rows[index].base_amount || rows[index].debit)"></span>
                        </div>
                        
                        <div class="space-y-3">
                            <!-- Account -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    {{ trans_dash('accounting.account', 'Account') }} *
                                </label>
                                <select 
                                    x-model="rows[index].account_id"
                                    @change="updateState()"
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
                                    @input="updateState()"
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
                            <span class="text-xs text-red-600" x-show="rows[index].credit > 0" x-text="formatMoney(rows[index].credit_base_amount || rows[index].credit)"></span>
                        </div>
                        
                        <div class="space-y-3">
                            <!-- Account -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    {{ trans_dash('accounting.account', 'Account') }} *
                                </label>
                                <select 
                                    x-model="rows[index].credit_account_id"
                                    @change="updateState()"
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
                                    @input="updateState()"
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
                            @input="updateState()"
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
                    class="text-2xl font-bold"
                    :class="Math.abs(difference) < 0.01 ? 'text-green-600' : 'text-red-600'"
                    x-text="formatMoney(difference)"
                ></div>
                <div x-show="Math.abs(difference) > 0.01" class="text-xs text-red-600 mt-1">
                    {{ trans_dash('accounting.entries_not_balanced', 'Entries are not balanced') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    <div x-show="errors.length > 0" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            <template x-for="error in errors" :key="error">
                <li x-text="error"></li>
            </template>
        </ul>
    </div>
</div>

@push('scripts')
<script>
function journalEntryCards(config) {
    // Ensure rows is always an array
    let initialRows = config.rows || [];
    if (!Array.isArray(initialRows)) {
        if (initialRows && typeof initialRows === 'object') {
            initialRows = Object.values(initialRows);
        } else {
            initialRows = [];
        }
    }
    
    return {
        statePath: config.statePath,
        rows: initialRows,
        selectedRows: [],
        errors: [],
        entryDate: null,
        globalCurrencyId: null,
        accountOptions: config.accountOptions || [],
        currencyOptions: config.currencyOptions || [],
        costCenterOptions: config.costCenterOptions || [],
        projectOptions: config.projectOptions || [],
        defaultCurrencyId: config.defaultCurrencyId,

        init() {
            this.ensureRowsIsArray();
            
            // Start with 0 cards - don't auto-add row
            // User must click "Add Row" button
            
            this.entryDate = this.getEntryDate();
            this.globalCurrencyId = this.defaultCurrencyId || '';
            
            this.$watch('rows', () => {
                this.ensureRowsIsArray();
                this.updateState();
                this.validate();
            }, { deep: true });
            
            this.updateState();
        },
        
        ensureRowsIsArray() {
            if (!Array.isArray(this.rows)) {
                if (this.rows && typeof this.rows === 'object') {
                    this.rows = Object.values(this.rows);
                } else {
                    this.rows = [];
                }
            }
        },
        
        getEntryDate() {
            try {
                const formData = this.$wire?.get?.('data');
                if (formData?.entry_date) {
                    return formData.entry_date;
                }
                
                const dateInput = document.querySelector('input[name="data.entry_date"]');
                if (dateInput && dateInput.value) {
                    return dateInput.value;
                }
                
                return new Date().toISOString().split('T')[0];
            } catch (e) {
                return new Date().toISOString().split('T')[0];
            }
        },

        addRow() {
            this.ensureRowsIsArray();
            const newRow = {
                account_id: '',
                debit: 0,
                credit: 0,
                currency_id: this.globalCurrencyId || this.defaultCurrencyId || '',
                exchange_rate: 1,
                base_amount: 0,
                credit_account_id: '',
                credit_currency_id: this.globalCurrencyId || this.defaultCurrencyId || '',
                credit_exchange_rate: 1,
                credit_base_amount: 0,
                description: '',
                credit_description: '',
                cost_center_id: '',
                project_id: '',
                reference: '',
            };
            this.rows.push(newRow);
            
            // Apply global currency if set
            if (this.globalCurrencyId) {
                this.applyCurrencyToRow(this.rows.length - 1, this.globalCurrencyId);
            }
            
            this.updateState();
        },
        
        async applyGlobalCurrency() {
            if (!this.globalCurrencyId) return;
            
            // Apply to all existing rows
            for (let i = 0; i < this.rows.length; i++) {
                await this.applyCurrencyToRow(i, this.globalCurrencyId);
            }
        },
        
        async applyCurrencyToRow(index, currencyId) {
            const row = this.rows[index];
            if (!row) return;
            
            // Apply to debit side
            if (currencyId) {
                row.currency_id = currencyId;
                if (currencyId == this.defaultCurrencyId) {
                    row.exchange_rate = 1;
                } else {
                    try {
                        const response = await fetch(`/api/exchange-rate?currency_id=${currencyId}&date=${this.entryDate}`);
                        const data = await response.json();
                        if (data.success) {
                            row.exchange_rate = parseFloat(data.rate);
                        }
                    } catch (e) {
                        row.exchange_rate = 1;
                    }
                }
                this.calculateBaseAmount(index, 'debit');
            }
            
            // Apply to credit side
            if (currencyId) {
                row.credit_currency_id = currencyId;
                if (currencyId == this.defaultCurrencyId) {
                    row.credit_exchange_rate = 1;
                } else {
                    try {
                        const response = await fetch(`/api/exchange-rate?currency_id=${currencyId}&date=${this.entryDate}`);
                        const data = await response.json();
                        if (data.success) {
                            row.credit_exchange_rate = parseFloat(data.rate);
                        }
                    } catch (e) {
                        row.credit_exchange_rate = 1;
                    }
                }
                this.calculateBaseAmount(index, 'credit');
            }
            
            this.updateState();
        },

        addMultipleRows() {
            for (let i = 0; i < config.quickAddCount; i++) {
                this.addRow();
            }
        },

        deleteRow(index) {
            this.ensureRowsIsArray();
            if (index >= 0 && index < this.rows.length) {
                this.rows.splice(index, 1);
                this.updateState();
            }
        },

        async onCurrencyChange(index) {
            const row = this.rows[index];
            if (!row.currency_id) {
                row.exchange_rate = 1;
                row.base_amount = row.debit || 0;
                this.updateState();
                return;
            }
            
            if (row.currency_id == this.defaultCurrencyId) {
                row.exchange_rate = 1;
                this.calculateBaseAmount(index, 'debit');
                return;
            }
            
            try {
                const response = await fetch(`/api/exchange-rate?currency_id=${row.currency_id}&date=${this.entryDate}`);
                const data = await response.json();
                if (data.success) {
                    row.exchange_rate = parseFloat(data.rate);
                    this.calculateBaseAmount(index, 'debit');
                }
            } catch (e) {
                console.error('Failed to fetch exchange rate:', e);
                row.exchange_rate = 1;
                this.calculateBaseAmount(index, 'debit');
            }
        },

        async onCreditCurrencyChange(index) {
            const row = this.rows[index];
            if (!row.credit_currency_id) {
                row.credit_exchange_rate = 1;
                row.credit_base_amount = row.credit || 0;
                this.updateState();
                return;
            }
            
            if (row.credit_currency_id == this.defaultCurrencyId) {
                row.credit_exchange_rate = 1;
                this.calculateBaseAmount(index, 'credit');
                return;
            }
            
            try {
                const response = await fetch(`/api/exchange-rate?currency_id=${row.credit_currency_id}&date=${this.entryDate}`);
                const data = await response.json();
                if (data.success) {
                    row.credit_exchange_rate = parseFloat(data.rate);
                    this.calculateBaseAmount(index, 'credit');
                }
            } catch (e) {
                console.error('Failed to fetch exchange rate:', e);
                row.credit_exchange_rate = 1;
                this.calculateBaseAmount(index, 'credit');
            }
        },

        onDebitChange(index) {
            const row = this.rows[index];
            if (row.debit > 0) {
                row.credit = 0;
            }
            this.calculateBaseAmount(index, 'debit');
        },

        onCreditChange(index) {
            const row = this.rows[index];
            if (row.credit > 0) {
                row.debit = 0;
            }
            this.calculateBaseAmount(index, 'credit');
        },

        onExchangeRateChange(index) {
            this.calculateBaseAmount(index, 'debit');
        },

        onCreditExchangeRateChange(index) {
            this.calculateBaseAmount(index, 'credit');
        },

        calculateBaseAmount(index, type) {
            const row = this.rows[index];
            
            if (type === 'debit') {
                const amount = parseFloat(row.debit) || 0;
                const exchangeRate = parseFloat(row.exchange_rate) || 1;
                
                if (amount > 0) {
                    if (row.currency_id && row.currency_id != this.defaultCurrencyId) {
                        row.base_amount = parseFloat((amount * exchangeRate).toFixed(2));
                    } else {
                        row.base_amount = amount;
                    }
                } else {
                    row.base_amount = 0;
                }
            } else if (type === 'credit') {
                const amount = parseFloat(row.credit) || 0;
                const exchangeRate = parseFloat(row.credit_exchange_rate) || 1;
                
                if (amount > 0) {
                    if (row.credit_currency_id && row.credit_currency_id != this.defaultCurrencyId) {
                        row.credit_base_amount = parseFloat((amount * exchangeRate).toFixed(2));
                    } else {
                        row.credit_base_amount = amount;
                    }
                } else {
                    row.credit_base_amount = 0;
                }
            }
            
            this.updateState();
        },

        get totalDebits() {
            this.ensureRowsIsArray();
            if (!Array.isArray(this.rows)) return 0;
            return this.rows.reduce((sum, row) => {
                if (!row) return sum;
                const baseAmount = parseFloat(row.base_amount) || 0;
                return sum + baseAmount;
            }, 0);
        },

        get totalCredits() {
            this.ensureRowsIsArray();
            if (!Array.isArray(this.rows)) return 0;
            return this.rows.reduce((sum, row) => {
                if (!row) return sum;
                const baseAmount = parseFloat(row.credit_base_amount) || 0;
                return sum + baseAmount;
            }, 0);
        },

        get difference() {
            return this.totalDebits - this.totalCredits;
        },

        formatMoney(amount) {
            const locale = config.isRTL ? 'ar' : 'en';
            const currency = '{{ app(\App\Services\MainCore\CurrencyService::class)->defaultCurrency()?->code ?? "USD" }}';
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: currency,
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(amount || 0);
        },

        updateState() {
            this.ensureRowsIsArray();
            // Convert rows to format expected by backend
            const formattedRows = this.rows.map(row => {
                // Create separate lines for debit and credit
                const lines = [];
                
                if (row.debit > 0 && row.account_id) {
                    lines.push({
                        account_id: parseInt(row.account_id),
                        debit: parseFloat(row.debit) || 0,
                        credit: 0,
                        currency_id: row.currency_id ? parseInt(row.currency_id) : null,
                        exchange_rate: parseFloat(row.exchange_rate) || 1,
                        amount: parseFloat(row.debit) || 0,
                        base_amount: parseFloat(row.base_amount) || parseFloat(row.debit) || 0,
                        description: row.description || '',
                        cost_center_id: row.cost_center_id ? parseInt(row.cost_center_id) : null,
                        project_id: row.project_id ? parseInt(row.project_id) : null,
                        reference: row.reference || '',
                    });
                }
                
                if (row.credit > 0 && row.credit_account_id) {
                    lines.push({
                        account_id: parseInt(row.credit_account_id),
                        debit: 0,
                        credit: parseFloat(row.credit) || 0,
                        currency_id: row.credit_currency_id ? parseInt(row.credit_currency_id) : null,
                        exchange_rate: parseFloat(row.credit_exchange_rate) || 1,
                        amount: parseFloat(row.credit) || 0,
                        base_amount: parseFloat(row.credit_base_amount) || parseFloat(row.credit) || 0,
                        description: row.credit_description || '',
                        cost_center_id: row.cost_center_id ? parseInt(row.cost_center_id) : null,
                        project_id: row.project_id ? parseInt(row.project_id) : null,
                        reference: row.reference || '',
                    });
                }
                
                return lines;
            }).flat().filter(line => line.account_id); // Remove empty lines
            
            this.$wire.set(config.statePath, formattedRows);
            this.validate();
        },

        validate() {
            this.ensureRowsIsArray();
            this.errors = [];
            
            if (!Array.isArray(this.rows)) {
                return;
            }
            
            // Check balance
            if (Math.abs(this.difference) > 0.01) {
                this.errors.push('{{ trans_dash("accounting.entries_not_balanced", "Entries are not balanced. Total Debit must equal Total Credit.") }}');
            }
            
            // Validate each row
            this.rows.forEach((row, index) => {
                if (!row) return;
                
                if (row.debit > 0 && !row.account_id) {
                    this.errors.push(`{{ trans_dash("accounting.row_required", "Row :row: Debit account is required", ["row" => ":row"]) }}`.replace(':row', index + 1));
                }
                
                if (row.credit > 0 && !row.credit_account_id) {
                    this.errors.push(`{{ trans_dash("accounting.row_required", "Row :row: Credit account is required", ["row" => ":row"]) }}`.replace(':row', index + 1));
                }
                
                if (row.debit > 0 && row.credit > 0) {
                    this.errors.push(`{{ trans_dash("accounting.row_error", "Row :row: Cannot have both debit and credit", ["row" => ":row"]) }}`.replace(':row', index + 1));
                }
            });
        }
    }
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

