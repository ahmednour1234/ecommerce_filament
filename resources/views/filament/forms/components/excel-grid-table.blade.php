@php
    $statePath = $getStatePath();
    $rows = $getState() ?? [];
    $columns = $getGridColumns();
    $isRTL = app()->getLocale() === 'ar';
    $allowAddRows = $getAllowAddRows();
    $allowDeleteRows = $getAllowDeleteRows();
    $allowQuickAdd = $getAllowQuickAdd();
    $quickAddCount = $getQuickAddCount();
    $totalDebitColumn = $getTotalDebitColumn();
    $totalCreditColumn = $getTotalCreditColumn();
    $differenceColumn = $getDifferenceColumn();
@endphp

<div 
    x-data="excelGridTable({
        statePath: @js($statePath),
        columns: @js($columns ?? []),
        rows: @js($rows ?? []),
        allowAddRows: @js($allowAddRows),
        allowDeleteRows: @js($allowDeleteRows),
        allowQuickAdd: @js($allowQuickAdd),
        quickAddCount: @js($quickAddCount),
        totalDebitColumn: @js($totalDebitColumn),
        totalCreditColumn: @js($totalCreditColumn),
        differenceColumn: @js($differenceColumn),
        isRTL: @js($isRTL),
    })"
    class="excel-grid-table"
    :dir="$isRTL ? 'rtl' : 'ltr'"
>
    <div class="excel-grid-toolbar mb-2 flex gap-2 flex-wrap">
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

        @if($allowDeleteRows)
            <button 
                type="button"
                @click="deleteSelectedRows()"
                class="filament-button filament-button-size-sm filament-button-color-danger inline-flex items-center justify-center gap-1"
                :disabled="selectedRows.length === 0"
            >
                <x-heroicon-o-trash class="w-4 h-4" />
                <span>{{ trans_dash('accounting.delete_selected', 'Delete Selected') }}</span>
            </button>
        @endif

        <button 
            type="button"
            @click="duplicateSelectedRows()"
            class="filament-button filament-button-size-sm filament-button-color-info inline-flex items-center justify-center gap-1"
            :disabled="selectedRows.length === 0"
        >
            <x-heroicon-o-document-duplicate class="w-4 h-4" />
            <span>{{ trans_dash('accounting.duplicate', 'Duplicate') }}</span>
        </button>
    </div>

    <div class="excel-grid-wrapper overflow-x-auto border border-gray-300 rounded-lg">
        <table class="excel-grid-table w-full border-collapse">
            <thead class="bg-gray-100 sticky top-0 z-10">
                <tr>
                    <th class="border border-gray-300 p-2 w-12">
                        <input 
                            type="checkbox" 
                            @change="toggleSelectAll($event.target.checked)"
                            class="rounded"
                        />
                    </th>
                    @foreach($columns as $column)
                        <th class="border border-gray-300 p-2 text-left font-semibold text-sm">
                            {{ $column['label'] ?? $column['name'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, index) in rows" :key="index">
                    <tr 
                        class="hover:bg-gray-50"
                        :class="{ 'bg-yellow-50': hasError(row, index) }"
                    >
                        <td class="border border-gray-300 p-1 text-center">
                            <input 
                                type="checkbox" 
                                :value="index"
                                x-model="selectedRows"
                                class="rounded"
                            />
                        </td>
                        <template x-for="column in columns" :key="column.name">
                            <td class="border border-gray-300 p-1" :data-column="column.name">
                                <div x-html="renderCell(column, row, index)"></div>
                            </td>
                        </template>
                    </tr>
                </template>
            </tbody>
            @if($totalDebitColumn || $totalCreditColumn)
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="border border-gray-300 p-2 text-right">
                            <div class="flex justify-end gap-4">
                                @if($totalDebitColumn)
                                    <span>
                                        {{ trans_dash('accounting.total_debit', 'Total Debit') }}: 
                                        <span x-text="formatMoney(totalDebits)"></span>
                                    </span>
                                @endif
                                @if($totalCreditColumn)
                                    <span>
                                        {{ trans_dash('accounting.total_credit', 'Total Credit') }}: 
                                        <span x-text="formatMoney(totalCredits)"></span>
                                    </span>
                                @endif
                                @if($differenceColumn)
                                    <span :class="{ 'text-red-600': Math.abs(difference) > 0.01 }">
                                        {{ trans_dash('accounting.difference', 'Difference') }}: 
                                        <span x-text="formatMoney(difference)"></span>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <div x-show="errors.length > 0" class="mt-2 p-2 bg-red-50 border border-red-200 rounded">
        <ul class="list-disc list-inside text-sm text-red-600">
            <template x-for="error in errors" :key="error">
                <li x-text="error"></li>
            </template>
        </ul>
    </div>
</div>

@push('scripts')
<script>
function excelGridTable(config) {
    return {
        statePath: config.statePath,
        columns: config.columns,
        rows: config.rows || [],
        selectedRows: [],
        errors: [],
        entryDate: null,
        defaultCurrencyId: @js(app(\App\Services\MainCore\CurrencyService::class)->defaultCurrency()?->id),

        init() {
            if (this.rows.length === 0) {
                this.addRow();
            }
            
            // Get entry date from parent form if available
            this.entryDate = this.getEntryDate();
            
            this.$watch('rows', () => {
                this.updateState();
                this.recalculateAll();
            }, { deep: true });
            this.updateState();
        },
        
        getEntryDate() {
            // Try to get entry_date from the form
            try {
                // Try multiple ways to get the entry date
                const formData = this.$wire?.get?.('data');
                if (formData?.entry_date) {
                    return formData.entry_date;
                }
                
                // Try to find date input in the form
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
            const newRow = {};
            this.columns.forEach(col => {
                newRow[col.name] = col.default ?? '';
            });
            this.rows.push(newRow);
            this.updateState();
        },

        addMultipleRows() {
            for (let i = 0; i < config.quickAddCount; i++) {
                this.addRow();
            }
        },

        deleteSelectedRows() {
            if (this.selectedRows.length === 0) return;
            
            // Sort descending to delete from end to start
            const sorted = [...this.selectedRows].sort((a, b) => b - a);
            sorted.forEach(index => {
                this.rows.splice(index, 1);
            });
            this.selectedRows = [];
            this.updateState();
        },

        duplicateSelectedRows() {
            if (this.selectedRows.length === 0) return;
            
            const duplicates = this.selectedRows.map(index => {
                return JSON.parse(JSON.stringify(this.rows[index]));
            });
            this.rows.push(...duplicates);
            this.selectedRows = [];
            this.updateState();
        },

        toggleSelectAll(checked) {
            if (checked) {
                this.selectedRows = this.rows.map((_, index) => index);
            } else {
                this.selectedRows = [];
            }
        },

        renderCell(column, row, index) {
            const value = row[column.name] ?? '';
            
            if (column.type === 'select') {
                const onChangeHandler = column.name === 'currency_id' 
                    ? `onCurrencyChange(${index})` 
                    : (column.name === 'debit' || column.name === 'credit')
                    ? `onDebitCreditChange(${index}, '${column.name}')`
                    : 'updateState()';
                    
                return `
                    <select 
                        x-model="rows[${index}].${column.name}"
                        @change="${onChangeHandler}"
                        class="w-full border-0 focus:ring-2 focus:ring-primary-500 p-1.5 text-sm bg-transparent rounded transition-all"
                        ${column.required ? 'required' : ''}
                    >
                        <option value="">--</option>
                        ${(column.options || []).map(opt => 
                            `<option value="${opt.value}">${opt.label}</option>`
                        ).join('')}
                    </select>
                `;
            }
            
            if (column.type === 'money') {
                const onChangeHandler = column.name === 'exchange_rate'
                    ? `onExchangeRateChange(${index})`
                    : column.name === 'amount'
                    ? `onAmountChange(${index})`
                    : (column.name === 'debit' || column.name === 'credit')
                    ? `onDebitCreditChange(${index}, '${column.name}')`
                    : 'updateState()';
                
                const isReadonly = column.readonly || column.name === 'base_amount';
                    
                return `
                    <input 
                        type="number"
                        step="0.01"
                        x-model.number="rows[${index}].${column.name}"
                        @input="${onChangeHandler}"
                        @keydown.tab="handleTab($event, ${index}, '${column.name}')"
                        @keydown.enter.prevent="handleEnter($event, ${index}, '${column.name}')"
                        class="w-full border-0 focus:ring-2 focus:ring-primary-500 p-1.5 text-sm text-right bg-transparent rounded transition-all font-mono ${isReadonly ? 'bg-gray-50 cursor-not-allowed' : ''}"
                        placeholder="0.00"
                        ${isReadonly ? 'readonly' : ''}
                        ${column.required ? 'required' : ''}
                    />
                `;
            }
            
            if (column.type === 'text' || !column.type) {
                return `
                    <input 
                        type="text"
                        x-model="rows[${index}].${column.name}"
                        @input="updateState()"
                        @keydown.tab="handleTab($event, ${index}, '${column.name}')"
                        @keydown.enter.prevent="handleEnter($event, ${index}, '${column.name}')"
                        class="w-full border-0 focus:ring-2 focus:ring-primary-500 p-1.5 text-sm bg-transparent rounded transition-all"
                        placeholder="${column.placeholder ?? ''}"
                        ${column.required ? 'required' : ''}
                    />
                `;
            }
            
            return value;
        },

        async onCurrencyChange(index) {
            const row = this.rows[index];
            if (!row.currency_id) {
                row.exchange_rate = 1;
                row.base_amount = row.debit || row.credit || 0;
                this.updateState();
                return;
            }
            
            // If currency is default, set rate to 1
            if (row.currency_id == this.defaultCurrencyId) {
                row.exchange_rate = 1;
                this.calculateBaseAmount(index);
                return;
            }
            
            // Fetch exchange rate
            try {
                const response = await fetch(`/api/exchange-rate?currency_id=${row.currency_id}&date=${this.entryDate}`);
                const data = await response.json();
                if (data.success) {
                    row.exchange_rate = parseFloat(data.rate);
                    this.calculateBaseAmount(index);
                }
            } catch (e) {
                console.error('Failed to fetch exchange rate:', e);
                row.exchange_rate = 1;
                this.calculateBaseAmount(index);
            }
        },

        onDebitCreditChange(index, field) {
            const row = this.rows[index];
            // Make debit and credit mutually exclusive
            if (field === 'debit' && row.debit > 0) {
                row.credit = 0;
            } else if (field === 'credit' && row.credit > 0) {
                row.debit = 0;
            }
            this.calculateBaseAmount(index);
        },

        onAmountChange(index) {
            this.calculateBaseAmount(index);
        },

        onExchangeRateChange(index) {
            this.calculateBaseAmount(index);
        },

        calculateBaseAmount(index) {
            const row = this.rows[index];
            const exchangeRate = parseFloat(row.exchange_rate) || 1;
            
            // Get the transaction amount (debit or credit)
            const transactionAmount = parseFloat(row.debit) || parseFloat(row.credit) || 0;
            
            if (transactionAmount > 0) {
                // If currency is not the base currency, calculate base amount
                if (row.currency_id && row.currency_id != this.defaultCurrencyId) {
                    // Store original amount in foreign currency
                    row.amount = transactionAmount;
                    // Convert to base currency
                    row.base_amount = parseFloat((transactionAmount * exchangeRate).toFixed(2));
                } else {
                    // Base currency: amount equals base amount
                    row.amount = transactionAmount;
                    row.base_amount = transactionAmount;
                }
            } else {
                // Clear amounts if no transaction
                row.amount = 0;
                row.base_amount = 0;
            }
            
            this.updateState();
        },

        recalculateAll() {
            this.rows.forEach((row, index) => {
                if (row.debit || row.credit || row.amount) {
                    this.calculateBaseAmount(index);
                }
            });
        },

        handleTab(event, rowIndex, columnName) {
            // Allow default tab behavior, but we can enhance it later
        },

        handleEnter(event, rowIndex, columnName) {
            // Move to next cell or add new row
            event.preventDefault();
            const currentColumnIndex = this.columns.findIndex(col => col.name === columnName);
            if (currentColumnIndex < this.columns.length - 1) {
                // Move to next column
                const nextColumn = this.columns[currentColumnIndex + 1];
                const nextInput = event.target.closest('tr').querySelector(`[data-column="${nextColumn.name}"] input, [data-column="${nextColumn.name}"] select`);
                if (nextInput) {
                    nextInput.focus();
                    nextInput.select();
                }
            } else {
                // Move to next row or add new row
                if (rowIndex < this.rows.length - 1) {
                    const nextRow = event.target.closest('tbody').children[rowIndex + 1];
                    const firstInput = nextRow?.querySelector('input, select');
                    if (firstInput) {
                        firstInput.focus();
                        firstInput.select();
                    }
                } else {
                    this.addRow();
                    this.$nextTick(() => {
                        const newRow = event.target.closest('tbody').lastElementChild;
                        const firstInput = newRow?.querySelector('input, select');
                        if (firstInput) {
                            firstInput.focus();
                            firstInput.select();
                        }
                    });
                }
            }
        },

        hasError(row, index) {
            // Check if row has validation errors
            return false;
        },

        get totalDebits() {
            if (!config.totalDebitColumn) return 0;
            return this.rows.reduce((sum, row) => {
                const debit = parseFloat(row.debit) || 0;
                if (debit > 0) {
                    // Use base_amount if available and currency is not base, otherwise use debit
                    const baseAmount = parseFloat(row.base_amount) || 0;
                    const val = baseAmount > 0 ? baseAmount : debit;
                    return sum + (isNaN(val) ? 0 : val);
                }
                return sum;
            }, 0);
        },

        get totalCredits() {
            if (!config.totalCreditColumn) return 0;
            return this.rows.reduce((sum, row) => {
                const credit = parseFloat(row.credit) || 0;
                if (credit > 0) {
                    // Use base_amount if available and currency is not base, otherwise use credit
                    const baseAmount = parseFloat(row.base_amount) || 0;
                    const val = baseAmount > 0 ? baseAmount : credit;
                    return sum + (isNaN(val) ? 0 : val);
                }
                return sum;
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
            this.$wire.set(config.statePath, this.rows);
            this.validate();
        },

        validate() {
            this.errors = [];
            
            // Check balance
            if (Math.abs(this.difference) > 0.01) {
                this.errors.push('{{ trans_dash("accounting.entries_not_balanced", "Entries are not balanced") }}');
            }
            
            // Validate each row
            this.rows.forEach((row, index) => {
                this.columns.forEach(col => {
                    if (col.required && !row[col.name]) {
                        this.errors.push(`{{ trans_dash("accounting.row_required", "Row :row: :field is required", ["row" => ":row", "field" => ":field"]) }}`
                            .replace(':row', index + 1)
                            .replace(':field', col.label || col.name));
                    }
                });
            });
        }
    }
}
</script>
@endpush

@push('styles')
<style>
.excel-grid-table {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 13px;
}

.excel-grid-wrapper {
    max-height: 600px;
    overflow-y: auto;
}

.excel-grid-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f3f4f6;
    font-weight: 600;
}

.excel-grid-table td input,
.excel-grid-table td select {
    background: transparent;
    width: 100%;
    transition: all 0.2s;
}

.excel-grid-table td input:hover,
.excel-grid-table td select:hover {
    background: #f9fafb;
}

.excel-grid-table td input:focus,
.excel-grid-table td select:focus {
    background: #fff3cd;
    outline: 2px solid #ffc107;
    border-radius: 3px;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
}

.excel-grid-table tbody tr:hover {
    background-color: #f9fafb;
}

.excel-grid-table tbody tr:nth-child(even) {
    background-color: #fafafa;
}

.excel-grid-table tbody tr:nth-child(even):hover {
    background-color: #f3f4f6;
}

.excel-grid-table tfoot {
    position: sticky;
    bottom: 0;
    z-index: 10;
    background: #f3f4f6;
}

.excel-grid-toolbar {
    background: #f9fafb;
    padding: 0.75rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.excel-grid-toolbar button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* RTL Support */
[dir="rtl"] .excel-grid-table {
    direction: rtl;
}

[dir="rtl"] .excel-grid-table td input[type="number"] {
    text-align: right;
    direction: ltr;
}

/* Number input styling */
.excel-grid-table input[type="number"] {
    font-variant-numeric: tabular-nums;
}

/* Error row styling */
.excel-grid-table tbody tr.bg-yellow-50 {
    border-left: 3px solid #f59e0b;
}
</style>
@endpush

