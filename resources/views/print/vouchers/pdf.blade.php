<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $is_rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $voucher->type === 'payment' ? trans_dash('vouchers.payment_voucher', 'Payment Voucher') : trans_dash('vouchers.receipt_voucher', 'Receipt Voucher') }} - {{ $voucher->voucher_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            direction: {{ $is_rtl ? 'rtl' : 'ltr' }};
            padding: 20px;
        }

        .voucher-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 2px solid #333;
            padding: 30px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .voucher-type {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
            color: #1a1a1a;
        }

        .voucher-type-arabic {
            font-size: 18px;
            margin-top: 5px;
        }

        .voucher-info {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .info-item {
            margin: 5px 0;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            min-width: 120px;
        }

        .details-section {
            margin: 30px 0;
        }

        .details-row {
            display: flex;
            margin: 15px 0;
            padding: 10px;
            border-bottom: 1px dotted #ccc;
        }

        .details-label {
            font-weight: bold;
            min-width: 150px;
        }

        .details-value {
            flex: 1;
        }

        .amount-section {
            background: #f5f5f5;
            padding: 20px;
            margin: 30px 0;
            border: 2px solid #333;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 16px;
        }

        .amount-label {
            font-weight: bold;
        }

        .amount-value {
            font-size: 18px;
            font-weight: bold;
        }

        .amount-in-words {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            font-style: italic;
        }

        .signatures-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #333;
        }

        .signatures-title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .signatures-table td {
            vertical-align: top;
            padding: 15px 10px;
            text-align: center;
            border: 1px solid #ddd;
            width: {{ !empty($signatures) ? (100 / count($signatures)) : 100 }}%;
        }

        .signature-image {
            max-height: 60px;
            max-width: 100%;
            margin-bottom: 10px;
            object-fit: contain;
        }

        .signature-name {
            font-weight: bold;
            font-size: 13px;
            margin: 10px 0 5px 0;
        }

        .signature-title {
            font-size: 11px;
            font-style: italic;
            color: #666;
            margin-bottom: 10px;
        }

        .signature-line {
            border-top: 1px dotted #333;
            margin: 15px 0 5px 0;
            padding-top: 5px;
        }

        .signature-date {
            font-size: 10px;
            color: #999;
            margin-top: 5px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        @media print {
            body {
                padding: 0;
            }
            .voucher-container {
                border: none;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="voucher-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ config('app.name', 'Company Name') }}</div>
            <div class="voucher-type">
                @if($voucher->type === 'payment')
                    {{ trans_dash('vouchers.payment_voucher', 'Payment Voucher') }}
                @else
                    {{ trans_dash('vouchers.receipt_voucher', 'Receipt Voucher') }}
                @endif
            </div>
            <div class="voucher-type-arabic">
                @if($voucher->type === 'payment')
                    سند صرف
                @else
                    سند قبض
                @endif
            </div>
            <div class="voucher-info">
                <div class="info-item">
                    <span class="info-label">{{ trans_dash('vouchers.voucher_number', 'Voucher Number') }}:</span>
                    <span>{{ $voucher->voucher_number }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ trans_dash('vouchers.voucher_date', 'Date') }}:</span>
                    <span>{{ $voucher->voucher_date->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>

        <!-- Voucher Details -->
        <div class="details-section">
            <div class="details-row">
                <span class="details-label">{{ trans_dash('accounting.account', 'Account') }}:</span>
                <span class="details-value">{{ $voucher->account->code ?? '' }} - {{ $voucher->account->name ?? '' }}</span>
            </div>

            @if($voucher->branch)
            <div class="details-row">
                <span class="details-label">{{ trans_dash('accounting.branch', 'Branch') }}:</span>
                <span class="details-value">{{ $voucher->branch->name }}</span>
            </div>
            @endif

            @if($voucher->costCenter)
            <div class="details-row">
                <span class="details-label">{{ trans_dash('accounting.cost_center', 'Cost Center') }}:</span>
                <span class="details-value">{{ $voucher->costCenter->name }}</span>
            </div>
            @endif

            @if($voucher->description)
            <div class="details-row">
                <span class="details-label">{{ trans_dash('vouchers.description', 'Description') }}:</span>
                <span class="details-value">{{ $voucher->description }}</span>
            </div>
            @endif

            @if($voucher->reference)
            <div class="details-row">
                <span class="details-label">{{ trans_dash('vouchers.reference', 'Reference') }}:</span>
                <span class="details-value">{{ $voucher->reference }}</span>
            </div>
            @endif
        </div>

        <!-- Amount Section -->
        <div class="amount-section">
            <div class="amount-row">
                <span class="amount-label">{{ trans_dash('vouchers.amount', 'Amount') }}:</span>
                <span class="amount-value">{{ number_format($voucher->amount, 2) }} {{ \App\Support\Money::defaultCurrencyCode() ?? 'USD' }}</span>
            </div>
            <div class="amount-in-words">
                <strong>{{ trans_dash('vouchers.amount_in_words', 'Amount in Words') }}:</strong> {{ $amount_in_words }}
            </div>
        </div>

        <!-- Signatures Section -->
        @if(!empty($signatures) && count($signatures) > 0)
        <div class="signatures-section">
            <div class="signatures-title">
                {{ trans_dash('vouchers.signatures.section_title', 'Signatures') }} / التوقيعات
            </div>
            <table class="signatures-table">
                <tr>
                    @foreach($signatures as $signature)
                    <td>
                        @if($signature->image_path)
                            <img src="{{ $signature->image_url }}" alt="{{ $signature->name }}" class="signature-image">
                        @endif
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $signature->name }}</div>
                        @if($signature->title)
                        <div class="signature-title">{{ $signature->title }}</div>
                        @endif
                        <div class="signature-date">{{ trans_dash('vouchers.signatures.date', 'Date') }}: ___________</div>
                    </td>
                    @endforeach
                </tr>
            </table>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>{{ trans_dash('vouchers.generated_on', 'Generated on') }} {{ now()->format('Y-m-d H:i:s') }}</p>
            @if($voucher->creator)
            <p>{{ trans_dash('vouchers.created_by', 'Created by') }}: {{ $voucher->creator->name }}</p>
            @endif
        </div>
    </div>
</body>
</html>

