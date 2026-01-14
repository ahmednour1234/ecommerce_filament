<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $record->document_no }}</title>
    <style>
        body{font-family: Arial, sans-serif; direction: rtl;}
        .box{border:1px solid #ddd;padding:16px;border-radius:10px}
        .row{display:flex;gap:16px;flex-wrap:wrap}
        .col{flex:1;min-width:250px}
        .muted{color:#666;font-size:12px}
    </style>
</head>
<body onload="window.print()">
    <div class="box">
        <h2>{{ tr('print.branch_tx.title', [], null, 'dashboard') }} #{{ $record->document_no }}</h2>
        <p class="muted">{{ $record->created_at?->format('Y-m-d H:i') }}</p>

        <div class="row">
            <div class="col">
                <strong>{{ tr('tables.branch_tx.branch', [], null, 'dashboard') }}:</strong> {{ $record->branch?->name }}
            </div>
            <div class="col">
                <strong>{{ tr('tables.branch_tx.type', [], null, 'dashboard') }}:</strong> {{ $record->type }}
            </div>
            <div class="col">
                <strong>{{ tr('tables.branch_tx.amount', [], null, 'dashboard') }}:</strong>
                {{ number_format((float)$record->amount,2) }} {{ $record->currency?->code }}
            </div>
            <div class="col">
                <strong>{{ tr('tables.branch_tx.transaction_date', [], null, 'dashboard') }}:</strong> {{ $record->transaction_date?->format('Y-m-d') }}
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col">
                <strong>{{ tr('tables.branch_tx.receiver_name', [], null, 'dashboard') }}:</strong> {{ $record->receiver_name ?? '-' }}
            </div>
            <div class="col">
                <strong>{{ tr('forms.branch_tx.payment_method', [], null, 'dashboard') }}:</strong> {{ $record->payment_method ?? '-' }}
            </div>
            <div class="col">
                <strong>{{ tr('forms.branch_tx.reference_no', [], null, 'dashboard') }}:</strong> {{ $record->reference_no ?? '-' }}
            </div>
        </div>

        <p><strong>{{ tr('forms.branch_tx.notes', [], null, 'dashboard') }}:</strong> {{ $record->notes ?? '-' }}</p>

        <hr>
        <p>
            <strong>{{ tr('tables.branch_tx.status', [], null, 'dashboard') }}:</strong> {{ $record->status }}
            <br>
            @if($record->status === 'approved')
                <strong>{{ tr('forms.branch_tx.approval_note', [], null, 'dashboard') }}:</strong> {{ $record->approval_note ?? '-' }}
            @elseif($record->status === 'rejected')
                <strong>{{ tr('forms.branch_tx.rejection_note', [], null, 'dashboard') }}:</strong> {{ $record->rejection_note ?? '-' }}
            @endif
        </p>

        @if($record->attachment_path)
            <hr>
            <p><strong>{{ tr('forms.branch_tx.attachment', [], null, 'dashboard') }}:</strong></p>
            @php $url = \Illuminate\Support\Facades\Storage::disk('public')->url($record->attachment_path); @endphp
            <p>{{ $url }}</p>
        @endif
    </div>
</body>
</html>
