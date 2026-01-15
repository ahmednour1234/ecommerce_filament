<x-filament::page>
    <style>
        /* مهم: خلي الـ style جوه نفس الـ root */
        @media print {
            .no-print { display: none !important; }
        }

        .print-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
        }
        .row { display:flex; justify-content:space-between; gap:12px; margin-bottom:10px; }
        .label { font-weight:600; opacity:.8; }
        .value { font-weight:700; }
    </style>

    <div class="space-y-4">
        <div class="no-print flex justify-end gap-2">
            <x-filament::button icon="heroicon-o-printer" onclick="window.print()">
                {{ tr('actions.print', [], null, 'dashboard') }}
            </x-filament::button>

            <x-filament::button color="gray" icon="heroicon-o-arrow-left" tag="a"
                href="{{ \App\Filament\Resources\Finance\BranchTransactionResource::getUrl('index') }}">
                {{ tr('actions.back', [], null, 'dashboard') }}
            </x-filament::button>
        </div>

        <div class="print-card">
            <div class="row">
                <div>
                    <div class="label">{{ tr('tables.branch_tx.document_no', [], null, 'dashboard') }}</div>
                    <div class="value">{{ $this->record->document_no ?? '-' }}</div>
                </div>

                <div>
                    <div class="label">{{ tr('tables.branch_tx.transaction_date', [], null, 'dashboard') }}</div>
                    <div class="value">{{ optional($this->record->transaction_date)->format('Y-m-d') ?? '-' }}</div>
                </div>
            </div>

            <div class="row">
                <div>
                    <div class="label">{{ tr('tables.branch_tx.branch', [], null, 'dashboard') }}</div>
                    <div class="value">{{ $this->record->branch?->name ?? '-' }}</div>
                </div>

                <div>
                    <div class="label">{{ tr('tables.branch_tx.country', [], null, 'dashboard') }}</div>
                    <div class="value">{{ $this->record->country?->name ?? '-' }}</div>
                </div>
            </div>

            <div class="row">
                <div>
                    <div class="label">{{ tr('tables.branch_tx.type', [], null, 'dashboard') }}</div>
                    <div class="value">
                        {{ $this->record->type === 'income'
                            ? tr('forms.branch_tx.type_income', [], null, 'dashboard')
                            : tr('forms.branch_tx.type_expense', [], null, 'dashboard') }}
                    </div>
                </div>

                <div>
                    <div class="label">{{ tr('tables.branch_tx.status', [], null, 'dashboard') }}</div>
                    <div class="value">{{ tr('tables.branch_tx.status_' . $this->record->status, [], null, 'dashboard') }}</div>
                </div>
            </div>

            <div class="row">
                <div>
                    <div class="label">{{ tr('tables.branch_tx.amount', [], null, 'dashboard') }}</div>
                    <div class="value">
                        {{ number_format((float) $this->record->amount, 2) }}
                        {{ $this->record->currency?->code ?? '' }}
                    </div>
                </div>

                <div>
                    <div class="label">{{ tr('tables.branch_tx.receiver_name', [], null, 'dashboard') }}</div>
                    <div class="value">{{ $this->record->receiver_name ?? '-' }}</div>
                </div>
            </div>

            <div class="row">
                <div>
                    <div class="label">{{ tr('forms.branch_tx.payment_method', [], null, 'dashboard') }}</div>
                    <div class="value">{{ $this->record->payment_method ?? '-' }}</div>
                </div>

                <div>
                    <div class="label">{{ tr('forms.branch_tx.reference_no', [], null, 'dashboard') }}</div>
                    <div class="value">{{ $this->record->reference_no ?? '-' }}</div>
                </div>
            </div>

            <div class="mt-4">
                <div class="label">{{ tr('forms.branch_tx.notes', [], null, 'dashboard') }}</div>
                <div class="value">{{ $this->record->notes ?? '-' }}</div>
            </div>
        </div>
    </div>
</x-filament::page>
