<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>{{ $record->document_no }} - Voucher</title>

    <style>
        /* ===== Print setup ===== */
        @page { size: A4; margin: 12mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #111;
            background: #fff;
        }
        .no-print { display: flex; gap: 8px; margin-bottom: 10px; }
        @media print { .no-print { display: none !important; } }

        /* ===== Card ===== */
        .paper {
            border: 2px solid #111;
            border-radius: 14px;
            padding: 16px;
            position: relative;
            min-height: 270mm;
        }
        .watermark {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 90px;
            opacity: 0.04;
            transform: rotate(-18deg);
            pointer-events: none;
            font-weight: 800;
            letter-spacing: 2px;
        }

        /* ===== Header ===== */
        .header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            align-items: center;
            border-bottom: 2px dashed #111;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
        .brand {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .logo {
            width: 64px;
            height: 64px;
            border: 1px solid #111;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .logo img { width: 100%; height: 100%; object-fit: contain; }
        .brand h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
        }
        .brand .sub {
            margin-top: 4px;
            font-size: 12px;
            opacity: .8;
            line-height: 1.6;
        }

        .meta {
            text-align: left;
            font-size: 12px;
            line-height: 1.9;
        }
        .badge {
            display: inline-block;
            border: 2px solid #111;
            border-radius: 999px;
            padding: 4px 10px;
            font-weight: 800;
            font-size: 12px;
        }

        /* ===== Big amount box ===== */
        .amount-box {
            margin: 14px 0;
            border: 2px solid #111;
            border-radius: 14px;
            padding: 14px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            align-items: center;
        }
        .amount-box .title {
            font-size: 14px;
            font-weight: 800;
        }
        .amount-box .amount {
            text-align: left;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: .5px;
        }
        .amount-box .small {
            font-size: 12px;
            opacity: .8;
            margin-top: 4px;
        }

        /* ===== Fields grid ===== */
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 12px;
            margin-top: 12px;
        }
        .field {
            border: 1px solid #111;
            border-radius: 12px;
            padding: 10px 12px;
            min-height: 54px;
        }
        .field .label {
            font-size: 11px;
            opacity: .8;
            margin-bottom: 6px;
            font-weight: 700;
        }
        .field .value {
            font-size: 13px;
            font-weight: 800;
        }
        .field.wide { grid-column: 1 / -1; min-height: 70px; }

        /* ===== Notes box ===== */
        .notes {
            border: 1px dashed #111;
            border-radius: 12px;
            padding: 12px;
            min-height: 80px;
            margin-top: 10px;
        }
        .notes .label { font-size: 11px; opacity: .8; font-weight: 700; margin-bottom: 6px; }
        .notes .value { font-size: 13px; line-height: 1.8; }

        /* ===== Signature area ===== */
        .sign {
            margin-top: 18px;
            border-top: 2px dashed #111;
            padding-top: 12px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
        }
        .sig-box {
            border: 1px solid #111;
            border-radius: 12px;
            padding: 10px 12px;
            min-height: 90px;
            position: relative;
        }
        .sig-box .label {
            font-size: 11px;
            opacity: .8;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .sig-line {
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: 14px;
            border-bottom: 1px solid #111;
            height: 1px;
        }

        /* ===== Footer ===== */
        .footer {
            margin-top: 10px;
            font-size: 11px;
            opacity: .8;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ طباعة</button>
        <button onclick="window.close()">إغلاق</button>
    </div>

    <div class="paper">
        <div class="watermark">
            {{ $record->type === 'income' ? 'RECEIPT' : 'PAYMENT' }}
        </div>

        {{-- Header --}}
        <div class="header">
            <div class="brand">
                <div class="logo">
                    @php $logo = setting('app.logo_print_url'); @endphp
                    @if($logo)
                        <img src="{{ $logo }}" alt="logo">
                    @else
                        <div style="font-weight:900;">LOGO</div>
                    @endif
                </div>

                <div>
                    <h1>{{ setting('app.name', 'MainCore Dashboard') }}</h1>
                    <div class="sub">
                        {{ $record->branch?->name ?? '-' }}<br>
                        {{ $record->country?->name ?? '' }}
                    </div>
                </div>
            </div>

            <div class="meta">
                <div class="badge">
                    {{ $record->type === 'income' ? 'سند قبض' : 'سند صرف' }}
                </div>
                <div><b>رقم السند:</b> {{ $record->document_no ?? '-' }}</div>
                <div><b>التاريخ:</b> {{ optional($record->transaction_date)->format('Y-m-d') ?? '-' }}</div>
                <div><b>الحالة:</b> {{ strtoupper($record->status ?? '-') }}</div>
            </div>
        </div>

        {{-- Amount big --}}
        <div class="amount-box">
            <div>
                <div class="title">المبلغ</div>
                <div class="small">
                    {{ $record->type === 'income' ? 'استلام مبلغ' : 'صرف مبلغ' }}
                </div>
            </div>

            <div class="amount">
                {{ number_format((float) $record->amount, 2) }}
                <span style="font-size:14px; font-weight:900;">
                    {{ $record->currency?->code ?? '' }}
                </span>
                @if(!empty($record->amount_base))
                    <div class="small">
                        (Base: {{ number_format((float) $record->amount_base, 2) }})
                    </div>
                @endif
            </div>
        </div>

        {{-- Fields --}}
        <div class="grid">
            <div class="field">
                <div class="label">الفرع</div>
                <div class="value">{{ $record->branch?->name ?? '-' }}</div>
            </div>

            <div class="field">
                <div class="label">الدولة</div>
                <div class="value">{{ $record->country?->name ?? '-' }}</div>
            </div>

            <div class="field">
                <div class="label">المستلم / المستفيد</div>
                <div class="value">{{ $record->receiver_name ?? '-' }}</div>
            </div>

            <div class="field">
                <div class="label">طريقة الدفع</div>
                <div class="value">{{ $record->payment_method ?? '-' }}</div>
            </div>

            <div class="field">
                <div class="label">رقم المرجع</div>
                <div class="value">{{ $record->reference_no ?? '-' }}</div>
            </div>

            <div class="field">
                <div class="label">العملة</div>
                <div class="value">{{ $record->currency?->code ?? '-' }}</div>
            </div>

            <div class="field wide">
                <div class="label">مرفق</div>
                <div class="value">
                    @if($record->attachment_path)
                        يوجد مرفق ({{ basename($record->attachment_path) }})
                    @else
                        لا يوجد
                    @endif
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="notes">
            <div class="label">ملاحظات</div>
            <div class="value">
                {{ $record->notes ?: '—' }}
            </div>
        </div>

        {{-- Approval notes (لو موجودة عندك في الأعمدة) --}}
        @if(!empty($record->approval_note) || !empty($record->rejection_note))
            <div class="notes" style="margin-top:10px;">
                <div class="label">ملاحظات الموافقة / الرفض</div>
                <div class="value">
                    @if(!empty($record->approval_note))
                        <b>موافقة:</b> {{ $record->approval_note }}<br>
                    @endif
                    @if(!empty($record->rejection_note))
                        <b>رفض:</b> {{ $record->rejection_note }}
                    @endif
                </div>
            </div>
        @endif

        {{-- Signatures --}}
        <div class="sign">
            <div class="sig-box">
                <div class="label">توقيع المستلم</div>
                <div class="sig-line"></div>
            </div>

            <div class="sig-box">
                <div class="label">المحاسب / أمين الصندوق</div>
                <div class="sig-line"></div>
            </div>

            <div class="sig-box">
                <div class="label">الختم</div>
                <div style="position:absolute; inset:32px 12px 28px 12px; border:1px dashed #111; border-radius:12px;"></div>
            </div>
        </div>

        <div class="footer">
            <div>تم الإنشاء بواسطة: {{ $record->creator?->name ?? '-' }}</div>
            <div>{{ now()->format('Y-m-d H:i') }}</div>
        </div>
    </div>
</body>
</html>
