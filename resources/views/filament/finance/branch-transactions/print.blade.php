<x-filament::page>
    <div class="space-y-4">
        <div class="flex gap-2 print:hidden">
            <x-filament::button type="button" onclick="window.print()">
                {{ tr('actions.print', [], null, 'dashboard') }}
            </x-filament::button>

            <x-filament::button color="gray" type="button" onclick="window.close()">
                {{ tr('actions.cancel', [], null, 'dashboard') }}
            </x-filament::button>
        </div>

        <div class="rounded-xl border p-4">
            <div class="text-lg font-bold">
                {{ tr('sidebar.finance.branch_transactions', [], null, 'dashboard') }}
            </div>

            <div class="text-sm text-gray-500">
                {{ tr('tables.branch_tx.document_no', [], null, 'dashboard') }}: {{ $record->document_no }}
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <div><b>{{ tr('tables.branch_tx.branch', [], null, 'dashboard') }}:</b> {{ $record->branch?->name ?? '-' }}</div>
                    <div><b>{{ tr('tables.branch_tx.country', [], null, 'dashboard') }}:</b> {{ $record->country?->name ?? '-' }}</div>
                    <div><b>{{ tr('tables.branch_tx.transaction_date', [], null, 'dashboard') }}:</b> {{ optional($record->transaction_date)->format('Y-m-d') ?? '-' }}</div>
                </div>

                <div>
                    <div><b>{{ tr('tables.branch_tx.type', [], null, 'dashboard') }}:</b>
                        {{ $record->type === 'income'
                            ? tr('forms.branch_tx.type_income', [], null, 'dashboard')
                            : tr('forms.branch_tx.type_expense', [], null, 'dashboard') }}
                    </div>

                    <div><b>{{ tr('tables.branch_tx.amount', [], null, 'dashboard') }}:</b>
                        {{ number_format((float)$record->amount, 2) }} {{ $record->currency?->code ?? '' }}
                    </div>

                    <div><b>{{ tr('tables.branch_tx.status', [], null, 'dashboard') }}:</b>
                        {{ tr('tables.branch_tx.status_'.$record->status, [], null, 'dashboard') }}
                    </div>
                </div>
            </div>

            @if($record->notes)
                <div class="mt-4">
                    <div class="text-sm text-gray-500">{{ tr('forms.branch_tx.notes', [], null, 'dashboard') }}</div>
                    <div>{{ $record->notes }}</div>
                </div>
            @endif
        </div>
    </div>

    <style>
        @media print {
            .print\:hidden { display: none !important; }
        }
    </style>
</x-filament::page>
