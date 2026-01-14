<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ tr('tables.branch_tx.document_no', [], null, 'dashboard') }}: {{ $record->document_no }}</title>

    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #111; }
        .row { display: flex; gap: 16px; margin-bottom: 10px; }
        .col { flex: 1; }
        .box { border: 1px solid #e5e7eb; padding: 14px; border-radius: 10px; }
        .title { font-size: 18px; font-weight: 700; margin-bottom: 12px; }
        .kv { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #eee; }
        .kv:last-child { border-bottom: 0; }
        .muted { color: #6b7280; font-size: 12px; }
        .actions { margin-bottom: 14px; display: flex; gap: 10px; }
        @media print { .actions { display:none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="actions">
        <button onclick="window.print()">{{ tr('actions.print', [], null, 'dashboard') }}</button>
        <button onclick="window.close()">{{ tr('actions.cancel', [], null, 'dashboard') }}</button>
    </div>

    <div class="box">
        <div class="title">
            {{ tr('sidebar.finance.branch_transactions', [], null, 'dashboard') }}
            <div class="muted">
                {{ tr('tables.branch_tx.document_no', [], null, 'dashboard') }}: {{ $record->document_no }}
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="kv">
                    <span>{{ tr('tables.branch_tx.branch', [], null, 'dashboard') }}</span>
                    <strong>{{ $record->branch?->name ?? '-' }}</strong>
                </div>
                <div class="kv">
                    <span>{{ tr('tables.branch_tx.country', [], null, 'dashboard') }}</span>
                    <strong>{{ $record->country?->name ?? '-' }}</strong>
                </div>
                <div class="kv">
                    <span>{{ tr('tables.branch_tx.transaction_date', [], null, 'dashboard') }}</span>
                    <strong>{{ optional($record->transaction_date)->format('Y-m-d') ?? '-' }}</strong>
                </div>
            </div>

            <div class="col">
                <div class="kv">
                    <span>{{ tr('tables.branch_tx.type', [], null, 'dashboard') }}</span>
                    <strong>
                        {{ $record->type === 'income'
                            ? tr('forms.branch_tx.type_income', [], null, 'dashboard')
                            : tr('forms.branch_tx.type_expense', [], null, 'dashboard') }}
                    </strong>
                </div>

                <div class="kv">
                    <span>{{ tr('tables.branch_tx.amount', [], null, 'dashboard') }}</span>
                    <strong>{{ number_format((float)$record->amount, 2) }} {{ $record->currency?->code ?? '' }}</strong>
                </div>

                <div class="kv">
                    <span>{{ tr('tables.branch_tx.status', [], null, 'dashboard') }}</span>
                    <strong>{{ tr('tables.branch_tx.status_'.$record->status, [], null, 'dashboard') }}</strong>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="kv">
                    <span>{{ tr('tables.branch_tx.receiver_name', [], null, 'dashboard') }}</span>
                    <strong>{{ $record->receiver_name ?? '-' }}</strong>
                </div>
                <div class="kv">
                    <span>{{ tr('forms.branch_tx.payment_method', [], null, 'dashboard') }}</span>
                    <strong>{{ $record->payment_method ?? '-' }}</strong>
                </div>
                <div class="kv">
                    <span>{{ tr('forms.branch_tx.reference_no', [], null, 'dashboard') }}</span>
                    <strong>{{ $record->reference_no ?? '-' }}</strong>
                </div>
            </div>
        </div>

        @if($record->notes)
            <div style="margin-top: 12px">
                <div class="muted">{{ tr('forms.branch_tx.notes', [], null, 'dashboard') }}</div>
                <div>{{ $record->notes }}</div>
            </div>
        @endif
    </div>
</body>
</html>
