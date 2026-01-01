<!doctype html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <title>Journal {{ $journal->code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; }
        .row { display:flex; justify-content:space-between; }
        .box { border:1px solid #ddd; padding:12px; border-radius:8px; margin-bottom:12px; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th,td { border:1px solid #ddd; padding:8px; text-align:right; }
        .signatures { margin-top:30px; display:flex; gap:20px; flex-wrap:wrap; }
        .sig { width:220px; border:1px dashed #aaa; padding:10px; border-radius:10px; text-align:center; }
        .sig img { max-width:180px; max-height:90px; display:block; margin:8px auto; }
        .muted { color:#666; font-size:12px; }
    </style>
</head>
<body>

<div class="box">
    <div class="row">
        <div><b>دفتر اليومية:</b> {{ $journal->name }}</div>
        <div><b>الكود:</b> {{ $journal->code }}</div>
    </div>
    <div class="row" style="margin-top:8px;">
        <div><b>النوع:</b> {{ $journal->type }}</div>
        <div><b>الحالة:</b> {{ $journal->is_active ? 'نشط' : 'غير نشط' }}</div>
    </div>
</div>

{{-- مثال: لو عايز تعرض القيود/الأسطر --}}
@if(method_exists($journal, 'journalEntries'))
    @php
        $entries = $journal->journalEntries()->with('lines.account')->get();
    @endphp

    @foreach($entries as $entry)
        <div class="box">
            <div><b>قيد رقم:</b> {{ $entry->id }} - <b>التاريخ:</b> {{ optional($entry->date)->format('Y-m-d') }}</div>

            <table>
                <thead>
                    <tr>
                        <th>الحساب</th>
                        <th>مدين</th>
                        <th>دائن</th>
                        <th>البيان</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entry->lines as $line)
                        <tr>
                            <td>{{ $line->account?->name }}</td>
                            <td>{{ $line->debit }}</td>
                            <td>{{ $line->credit }}</td>
                            <td>{{ $line->description }}</td>
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
