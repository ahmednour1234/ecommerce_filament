/**
 * Journal Entry Cards Component
 * Alpine.js component for managing journal entry lines
 */

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
        updateTimer: null,
        loadingStates: {
            exchangeRates: false,
            syncing: false,
        },
        accountOptions: config.accountOptions || [],
        currencyOptions: config.currencyOptions || [],
        costCenterOptions: config.costCenterOptions || [],
        projectOptions: config.projectOptions || [],
        defaultCurrencyId: config.defaultCurrencyId,
        defaultCurrencyCode: config.defaultCurrencyCode || 'USD',
        isRTL: config.isRTL || false,

        init() {
            this.ensureRowsIsArray();

            // Start with 0 cards - don't auto-add row
            // User must click "Add Row" button

            this.entryDate = this.getEntryDate();
            this.globalCurrencyId = this.defaultCurrencyId || '';

            // Debounce timer for Livewire updates
            this.updateTimer = null;

            // Sync on form submit (before form is submitted)
            const form = this.$el.closest('form');
            if (form) {
                form.addEventListener('submit', () => {
                    // Clear any pending debounced updates
                    if (this.updateTimer) {
                        clearTimeout(this.updateTimer);
                    }
                    // Immediate sync before submit
                    this.syncToLivewire();
                });
            }

            // Initial state sync (only once)
            this.syncToLivewire();
        },

        createEmptyRow() {
            return {
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
        },

        ensureRowsIsArray() {
            if (!Array.isArray(this.rows)) {
                if (this.rows && typeof this.rows === 'object') {
                    this.rows = Object.values(this.rows);
                } else {
                    this.rows = [];
                }
            }

            // Ensure all rows are valid objects
            const emptyRow = this.createEmptyRow();
            this.rows = this.rows.map((row, idx) => {
                if (!row || typeof row !== 'object') {
                    return { ...emptyRow };
                }
                // Merge with empty row to ensure all properties exist
                return { ...emptyRow, ...row };
            });
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
            const newRow = this.createEmptyRow();
            this.rows.push(newRow);

            // Apply global currency if set
            if (this.globalCurrencyId) {
                this.applyCurrencyToRow(this.rows.length - 1, this.globalCurrencyId);
            } else {
                // Only sync to Livewire, no debounce needed for add
                this.syncToLivewire();
            }
        },

        async applyGlobalCurrency() {
            if (!this.globalCurrencyId) return;

            this.loadingStates.exchangeRates = true;
            try {
                // Collect all unique currencies that need rates
                const currenciesToFetch = new Set();
                for (let i = 0; i < this.rows.length; i++) {
                    if (this.rows[i] && this.globalCurrencyId != this.defaultCurrencyId) {
                        currenciesToFetch.add(this.globalCurrencyId);
                    }
                }

                // Batch fetch exchange rates
                if (currenciesToFetch.size > 0) {
                    await this.fetchBatchExchangeRates(Array.from(currenciesToFetch));
                }

                // Apply to all existing rows
                for (let i = 0; i < this.rows.length; i++) {
                    await this.applyCurrencyToRow(i, this.globalCurrencyId);
                }
            } finally {
                this.loadingStates.exchangeRates = false;
            }
        },

        async fetchBatchExchangeRates(currencyIds) {
            if (currencyIds.length === 0) return {};

            try {
                const response = await fetch('/api/exchange-rates/batch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        currencies: currencyIds.map(id => ({ currency_id: id })),
                        date: this.entryDate,
                    }),
                });

                const data = await response.json();
                if (data.success && data.rates) {
                    const ratesMap = {};
                    data.rates.forEach(rate => {
                        ratesMap[rate.currency_id] = rate.rate;
                    });
                    return ratesMap;
                }
            } catch (e) {
                console.error('Failed to fetch batch exchange rates:', e);
            }
            return {};
        },

        async applyCurrencyToRow(index, currencyId) {
            if (!this.rows[index]) {
                return;
            }
            const row = this.rows[index];

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

            // Debounced update for currency changes
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
                // Immediate sync for delete
                this.syncToLivewire();
            }
        },

        async onCurrencyChange(index) {
            if (!this.rows[index]) {
                return;
            }
            const row = this.rows[index];
            if (!row.currency_id) {
                row.exchange_rate = 1;
                row.base_amount = row.debit || 0;
                this.updateState(); // Debounced
                return;
            }

            if (row.currency_id == this.defaultCurrencyId) {
                row.exchange_rate = 1;
                this.calculateBaseAmount(index, 'debit');
                return;
            }

            this.loadingStates.exchangeRates = true;
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
            } finally {
                this.loadingStates.exchangeRates = false;
            }
        },

        async onCreditCurrencyChange(index) {
            if (!this.rows[index]) {
                return;
            }
            const row = this.rows[index];
            if (!row.credit_currency_id) {
                row.credit_exchange_rate = 1;
                row.credit_base_amount = row.credit || 0;
                this.updateState(); // Debounced
                return;
            }

            if (row.credit_currency_id == this.defaultCurrencyId) {
                row.credit_exchange_rate = 1;
                this.calculateBaseAmount(index, 'credit');
                return;
            }

            this.loadingStates.exchangeRates = true;
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
            } finally {
                this.loadingStates.exchangeRates = false;
            }
        },

        onDebitChange(index) {
            if (!this.rows[index]) {
                return;
            }
            const row = this.rows[index];
            if (row.debit > 0) {
                row.credit = 0;
            }
            this.calculateBaseAmount(index, 'debit');
        },

        onCreditChange(index) {
            if (!this.rows[index]) {
                return;
            }
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
            if (!this.rows[index]) {
                return;
            }
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

            // Only validate locally, don't sync to Livewire on every calculation
            this.validate();
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
            const locale = this.isRTL ? 'ar' : 'en';
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: this.defaultCurrencyCode,
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(amount || 0);
        },

        // Debounced update - only syncs to Livewire after user stops typing
        updateState() {
            this.ensureRowsIsArray();
            this.validate(); // Local validation only

            // Clear existing timer
            if (this.updateTimer) {
                clearTimeout(this.updateTimer);
            }

            // Debounce Livewire sync (500ms delay)
            this.updateTimer = setTimeout(() => {
                this.syncToLivewire();
            }, 500);
        },

        // Immediate sync to Livewire (for critical events)
        syncToLivewire() {
            this.ensureRowsIsArray();
            this.loadingStates.syncing = true;
            
            try {
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
            } finally {
                this.loadingStates.syncing = false;
            }
        },

        validate() {
            this.ensureRowsIsArray();
            this.errors = [];

            if (!Array.isArray(this.rows)) {
                return;
            }

            // Check balance
            if (Math.abs(this.difference) > 0.01) {
                this.errors.push(this.translate('accounting.entries_not_balanced', 'Entries are not balanced. Total Debit must equal Total Credit.'));
            }

            // Validate each row
            this.rows.forEach((row, index) => {
                if (!row) return;

                if (row.debit > 0 && !row.account_id) {
                    this.errors.push(this.translate('accounting.row_required', `Row ${index + 1}: Debit account is required`));
                }

                if (row.credit > 0 && !row.credit_account_id) {
                    this.errors.push(this.translate('accounting.row_required', `Row ${index + 1}: Credit account is required`));
                }

                if (row.debit > 0 && row.credit > 0) {
                    this.errors.push(this.translate('accounting.row_error', `Row ${index + 1}: Cannot have both debit and credit`));
                }
            });
        },

        translate(key, fallback) {
            // Simple translation helper - can be enhanced with actual translation system
            return window.translations?.[key] || fallback || key;
        },
    };
}

// Make function globally available
window.journalEntryCards = journalEntryCards;

