@php
    $currentLang = app()->getLocale() ?? 'ar';
    $isRTL = true;
    $direction = 'rtl';
    $fontFamily = "'Cairo', 'DejaVu Sans', Arial, sans-serif";
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tr('pdf.title', [], null, 'packages') }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ public_path('fonts/DejaVuSans.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ public_path('fonts/DejaVuSans-Bold.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path('fonts/Cairo-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path('fonts/Cairo-Bold.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: {{ $fontFamily }};
            font-size: 12px;
            color: #333;
            padding: 20px;
            direction: rtl;
            unicode-bidi: embed;
        }
        
        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            font-family: {{ $fontFamily }};
            direction: rtl;
            unicode-bidi: embed;
            text-align: right;
        }
        
        .package-info {
            margin-bottom: 30px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
            direction: rtl;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
            text-align: right;
            padding-right: 10px;
        }
        
        .info-value {
            flex: 1;
            text-align: right;
        }
        
        .cost-summary {
            margin-bottom: 30px;
        }
        
        .cost-summary h2 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: right;
            direction: rtl;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            direction: rtl;
        }
        
        thead {
            background-color: #f0f0f0;
        }
        
        th {
            padding: 10px;
            text-align: right;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 11px;
            font-family: {{ $fontFamily }};
            direction: rtl;
            unicode-bidi: embed;
        }
        
        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 11px;
            text-align: right;
            font-family: {{ $fontFamily }};
            direction: rtl;
            unicode-bidi: embed;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #e8e8e8;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ tr('pdf.title', [], null, 'packages') }}</h1>
    </div>
    
    <div class="package-info">
        <div class="info-row">
            <div class="info-label">{{ tr('fields.name', [], null, 'packages') }}:</div>
            <div class="info-value">{{ $package->name }}</div>
        </div>
        @if($package->description)
        <div class="info-row">
            <div class="info-label">{{ tr('fields.description', [], null, 'packages') }}:</div>
            <div class="info-value">{{ $package->description }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">{{ tr('fields.type', [], null, 'packages') ?: tr('common.type', [], null, 'dashboard') }}:</div>
            <div class="info-value">{{ tr("types.{$package->type}", [], null, 'packages') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">{{ tr('fields.status', [], null, 'packages') }}:</div>
            <div class="info-value">{{ tr("status.{$package->status}", [], null, 'packages') }}</div>
        </div>
        @if($package->country)
        <div class="info-row">
            <div class="info-label">{{ tr('fields.country', [], null, 'packages') }}:</div>
            <div class="info-value">{{ $package->country->name_text }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">{{ tr('fields.duration', [], null, 'packages') }}:</div>
            <div class="info-value">{{ $package->duration }} {{ tr("duration_types.{$package->duration_type}", [], null, 'packages') }}</div>
        </div>
    </div>
    
    <div class="cost-summary">
        <h2>{{ tr('common.pricing', [], null, 'dashboard') ?: 'Pricing Summary' }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ tr('fields.base_price', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.external_costs', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.worker_salary', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.gov_fees', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.tax_percent', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.tax_value', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.total', [], null, 'packages') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ number_format($package->base_price, 2) }} {{ 'SAR' }}</td>
                    <td>{{ number_format($package->external_costs, 2) }} {{ 'SAR' }}</td>
                    <td>{{ number_format($package->worker_salary, 2) }} {{ 'SAR' }}</td>
                    <td>{{ number_format($package->gov_fees, 2) }} {{ 'SAR' }}</td>
                    <td>{{ number_format($package->tax_percent, 2) }}%</td>
                    <td>{{ number_format($package->tax_value, 2) }} {{ 'SAR' }}</td>
                    <td class="total-row">{{ number_format($package->total, 2) }} {{ 'SAR' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    @if($package->type === 'recruitment' && $package->packageDetails->count() > 0)
    <div class="cost-summary">
        <h2>{{ tr('pdf.title', [], null, 'packages') }} - {{ tr('fields.profession', [], null, 'packages') }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ tr('fields.code', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.title', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.country', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.profession', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.direct_cost', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.gov_cost', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.external_cost', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.tax_percent', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.tax_value', [], null, 'packages') }}</th>
                    <th>{{ tr('fields.total_with_tax', [], null, 'packages') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($package->packageDetails as $detail)
                <tr>
                    <td>{{ $detail->code }}</td>
                    <td>{{ $detail->title }}</td>
                    <td>{{ $detail->country ? $detail->country->name_text : '-' }}</td>
                    <td>{{ $detail->profession ? (app()->getLocale() === 'ar' ? $detail->profession->name_ar : $detail->profession->name_en) : '-' }}</td>
                    <td>{{ number_format($detail->direct_cost, 2) }} {{ 'SAR' }}</td>
                    <td>{{ number_format($detail->gov_cost, 2) }} {{ 'SAR' }}</td>
                    <td>{{ number_format($detail->external_cost, 2) }} {{ 'SAR' }}</td>
                    <td>{{ number_format($detail->tax_percent, 2) }}%</td>
                    <td>{{ number_format($detail->tax_value, 2) }} {{ 'SAR' }}</td>
                    <td class="total-row">{{ number_format($detail->total_with_tax, 2) }} {{ 'SAR' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <div class="footer">
        <p>{{ tr('common.generated_on', [], null, 'dashboard') ?: 'Generated on' }} {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
