@php
    $statePath = $getStatePath();
    $rows = $getState() ?? [];
    $columns = $getColumns();
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
        columns: @js($columns),
        rows: @js($rows),
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
                            <td class="border border-gray-300 p-1">
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

        init() {
            if (this.rows.length === 0) {
                this.addRow();
            }
            this.$watch('rows', () => this.updateState(), { deep: true });
            this.updateState();
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
                return `
                    <select 
                        x-model="rows[${index}].${column.name}"
                        @change="updateState(); ${column.onChange ? column.onChange : ''}"
                        class="w-full border-0 focus:ring-0 p-1 text-sm bg-transparent"
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
                return `
                    <input 
                        type="number"
                        step="0.01"
                        x-model.number="rows[${index}].${column.name}"
                        @input="updateState(); calculateBaseAmount(${index})"
                        class="w-full border-0 focus:ring-0 p-1 text-sm text-right bg-transparent"
                        placeholder="0.00"
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
                        class="w-full border-0 focus:ring-0 p-1 text-sm bg-transparent"
                        placeholder="${column.placeholder ?? ''}"
                        ${column.required ? 'required' : ''}
                    />
                `;
            }
            
            return value;
        },

        calculateBaseAmount(index) {
            const row = this.rows[index];
            if (row.amount && row.exchange_rate) {
                row.base_amount = parseFloat((row.amount * row.exchange_rate).toFixed(2));
            }
            this.updateState();
        },

        hasError(row, index) {
            // Check if row has validation errors
            return false;
        },

        get totalDebits() {
            if (!config.totalDebitColumn) return 0;
            return this.rows.reduce((sum, row) => {
                const val = parseFloat(row[config.totalDebitColumn] || row.debit || row.base_amount || 0);
                return sum + (isNaN(val) ? 0 : val);
            }, 0);
        },

        get totalCredits() {
            if (!config.totalCreditColumn) return 0;
            return this.rows.reduce((sum, row) => {
                const val = parseFloat(row[config.totalCreditColumn] || row.credit || row.base_amount || 0);
                return sum + (isNaN(val) ? 0 : val);
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

.excel-grid-table td input,
.excel-grid-table td select {
    background: transparent;
    width: 100%;
}

.excel-grid-table td input:focus,
.excel-grid-table td select:focus {
    background: #fff3cd;
    outline: 2px solid #ffc107;
    border-radius: 2px;
}

.excel-grid-table tbody tr:hover {
    background-color: #f9fafb;
}
</style>
@endpush

