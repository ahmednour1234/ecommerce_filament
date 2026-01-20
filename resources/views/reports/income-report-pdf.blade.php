<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ?? (app()->getLocale() === 'ar' ? 'rtl' : 'ltr') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: {{ $isRtl ?? (app()->getLocale() === 'ar') ? "'Tajawal', 'Cairo', 'DejaVu Sans', sans-serif" : "'DejaVu Sans', sans-serif" }};
            font-size: 10px;
            color: #333;
            padding: 20px;
            direction: {{ $isRtl ?? (app()->getLocale() === 'ar' ? 'rtl' : 'ltr') ? 'rtl' : 'ltr' }};
        }
        
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            text-align: {{ $isRtl ?? (app()->getLocale() === 'ar') ? 'right' : 'left' }};
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .metadata {
            font-size: 9px;
            color: #666;
            margin-top: 5px;
        }
        
        .metadata-item {
            margin-{{ $isRtl ?? (app()->getLocale() === 'ar') ? 'left' : 'right' }}: 15px;
            display: inline-block;
        }
        
        .summary-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .summary-section h2 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: {{ $isRtl ?? (app()->getLocale() === 'ar') ? 'right' : 'left' }};
        }
        
        .summary-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .summary-stat {
            text-align: center;
            padding: 10px;
            min-width: 150px;
        }
        
        .summary-stat-label {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .summary-stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            direction: {{ $isRtl ?? (app()->getLocale() === 'ar' ? 'rtl' : 'ltr') ? 'rtl' : 'ltr' }};
        }
        
        thead {
            background-color: #f0f0f0;
        }
        
        th {
            padding: 8px;
            text-align: {{ $isRtl ?? (app()->getLocale() === 'ar') ? 'right' : 'left' }};
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            font-size: 9px;
            text-align: {{ $isRtl ?? (app()->getLocale() === 'ar') ? 'right' : 'left' }};
        }
        
        .text-right {
            text-align: right !important;
        }
        
        .text-left {
            text-align: left !important;
        }
        
        .text-center {
            text-align: center !important;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #666;
            text-align: center;
        }
        
        .totals-row {
            font-weight: bold;
            background-color: #e8e8e8 !important;
        }
        
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        @if(!empty($metadata))
        <div class="metadata">
            @if(isset($metadata['date_from']) && isset($metadata['date_to']))
            <span class="metadata-item">
                <strong>{{ trans_dash('reports.metadata.date_range', 'Date Range') }}:</strong> {{ $metadata['date_from'] }} - {{ $metadata['date_to'] }}
            </span>
            @endif
            @if(isset($metadata['branch']) && $metadata['branch'])
            <span class="metadata-item">
                <strong>{{ trans_dash('reports.metadata.branch', 'Branch') }}:</strong> {{ $metadata['branch'] }}
            </span>
            @endif
            @if(isset($metadata['currency']) && $metadata['currency'])
            <span class="metadata-item">
                <strong>{{ trans_dash('reports.metadata.currency', 'Currency') }}:</strong> {{ $metadata['currency'] }}
            </span>
            @endif
        </div>
        @endif
    </div>
    
    @if(isset($summaryRows) && !empty($summaryRows) && isset($summaryHeaders))
    <div class="summary-section">
        <h2>{{ trans_dash('reports.income.summary.title', 'Summary') }}</h2>
        
        @if(isset($metadata['total_income']) || isset($metadata['transaction_count']))
        <div class="summary-stats">
            @if(isset($metadata['total_income']))
            <div class="summary-stat">
                <div class="summary-stat-label">{{ trans_dash('reports.income.summary.total_income', 'Total Income') }}</div>
                <div class="summary-stat-value">{{ $metadata['total_income'] ?? '0.00' }}</div>
            </div>
            @endif
            @if(isset($metadata['transaction_count']))
            <div class="summary-stat">
                <div class="summary-stat-label">{{ trans_dash('reports.income.summary.transaction_count', 'Transaction Count') }}</div>
                <div class="summary-stat-value">{{ $metadata['transaction_count'] ?? '0' }}</div>
            </div>
            @endif
        </div>
        @endif
        
        @if(!empty($summaryRows))
        <table>
            <thead>
                <tr>
                    @foreach($summaryHeaders as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($summaryRows as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td class="{{ is_numeric($cell) ? 'text-right' : '' }}">{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif
    
    @if(!empty($rows))
    <div style="margin-top: 20px;">
        <h2 style="font-size: 14px; font-weight: bold; margin-bottom: 10px; text-align: {{ $isRtl ?? (app()->getLocale() === 'ar') ? 'right' : 'left' }};">
            {{ trans_dash('reports.income.detailed_transactions', 'Detailed Transactions') }}
        </h2>
        <table>
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr class="{{ isset($row['_is_total']) && $row['_is_total'] ? 'totals-row' : '' }}">
                        @foreach($row as $key => $cell)
                            @if(!str_starts_with($key, '_'))
                            <td class="{{ isset($row['_align_' . $key]) ? 'text-' . $row['_align_' . $key] : (is_numeric($cell) ? 'text-right' : '') }}">
                                {{ $cell }}
                            </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>{{ trans_dash('reports.no_data', 'No data available for export.') }}</p>
    @endif
    
    <div class="footer">
        <p>{{ trans_dash('reports.generated_on', 'Generated on') }} {{ $metadata['exported_at'] ?? now()->format('Y-m-d H:i:s') }} | {{ trans_dash('reports.generated_by', 'Generated by') }}: {{ $metadata['exported_by'] ?? auth()->user()?->name ?? 'System' }}</p>
    </div>
</body>
</html>
