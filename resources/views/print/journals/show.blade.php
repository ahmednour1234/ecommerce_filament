<!doctype html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <title>Journal {{ $journal->code }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ public_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ public_path('fonts/Tajawal-Regular.ttf') }}') format('truetype');
            font-weight: normal;
        }
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ public_path('fonts/Tajawal-Bold.ttf') }}') format('truetype');
            font-weight: bold;
        }
        body { 
            font-family: 'Tajawal', 'DejaVu Sans', sans-serif; 
            direction: rtl; 
            font-size: 12px;
            line-height: 1.6;
        }
        .row { display:flex; justify-content:space-between; }
        .box { border:1px solid #ddd; padding:12px; border-radius:8px; margin-bottom:12px; }
        table { width:100%; border-collapse:collapse; margin-top:10px; font-family: 'Tajawal', 'DejaVu Sans', sans-serif; }
        th,td { border:1px solid #ddd; padding:8px; text-align:right; font-family: 'Tajawal', 'DejaVu Sans', sans-serif; }
        th { font-weight: bold; background-color: #f0f0f0; }
        .signatures { margin-top:30px; display:flex; gap:20px; flex-wrap:wrap; }
        .sig { width:220px; border:1px dashed #aaa; padding:10px; border-radius:10px; text-align:center; }
        .sig img { max-width:180px; max-height:90px; display:block; margin:8px auto; }
        .muted { color:#666; font-size:12px; }
    </style>
</head>
<body>

<div class="box">
    <div class="row">
        <div><b>{{ trans_dash('accounting.journal', 'Journal') }}:</b> {{ $journal->name }}</div>
        <div><b>{{ trans_dash('accounting.code', 'Code') }}:</b> {{ $journal->code }}</div>
    </div>
    <div class="row" style="margin-top:8px;">
        <div><b>{{ trans_dash('accounting.type', 'Type') }}:</b> {{ $journal->type }}</div>
        <div><b>{{ trans_dash('accounting.status', 'Status') }}:</b> {{ $journal->is_active ? trans_dash('accounting.active', 'Active') : trans_dash('accounting.inactive', 'Inactive') }}</div>
    </div>
</div>

{{-- Display journal entries --}}
@if(method_exists($journal, 'journalEntries'))
    @php
        $entries = $journal->journalEntries()->with('lines.account')->get();
    @endphp

    @foreach($entries as $entry)
        <div class="box">
            <div><b>{{ trans_dash('accounting.entry_number', 'Entry Number') }}:</b> {{ $entry->entry_number ?? $entry->id }} - <b>{{ trans_dash('accounting.date', 'Date') }}:</b> {{ optional($entry->entry_date ?? $entry->date)->format('Y-m-d') }}</div>

            <table>
                <thead>
                    <tr>
                        <th>{{ trans_dash('accounting.account', 'Account') }}</th>
                        <th>{{ trans_dash('accounting.debit', 'Debit') }}</th>
                        <th>{{ trans_dash('accounting.credit', 'Credit') }}</th>
                        <th>{{ trans_dash('accounting.description', 'Description') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entry->lines as $line)
                        <tr>
                            <td>{{ $line->account?->code ?? '' }} - {{ $line->account?->name ?? '' }}</td>
                            <td>{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                            <td>{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                            <td>{{ $line->description ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
@endif

@if($signatures->count())
    <div class="signatures">
        @foreach($signatures as $sig)
            <div class="sig">
                <div><b>{{ $sig->name }}</b></div>
                @if($sig->title)
                    <div class="muted">{{ $sig->title }}</div>
                @endif

                @if($sig->image_path)
                    <img src="{{ public_path('storage/' . $sig->image_path) }}" alt="signature">
                @else
                    <div class="muted">—</div>
                @endif
            </div>
        @endforeach
    </div>
@endif

<script>
    // لو فتحتها Print action (HTML) هتطبع
    if (window.location.search.includes('autoprint=1')) {
        window.print();
    }
</script>

</body>
</html>
