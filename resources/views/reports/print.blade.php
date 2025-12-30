<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
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
            font-family: {{ app()->getLocale() === 'ar' ? "'Tajawal', 'Cairo', 'Arial', sans-serif" : "'Arial', sans-serif" }};
            font-size: 12px;
            color: #333;
            padding: 20px;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }
        
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .company-info {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .metadata {
            font-size: 10px;
            color: #666;
            margin-top: 10px;
        }
        
        .metadata-item {
            margin-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 20px;
            display: inline-block;
            margin-bottom: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }
        
        thead {
            background-color: #f0f0f0;
        }
        
        th {
            padding: 10px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 11px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
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
        
        tbody tr:hover {
            background-color: #f5f5f5;
        }
        
        .totals-row {
            font-weight: bold;
            background-color: #e8e8e8 !important;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
            text-align: center;
        }
        
        .summary-cards {
            display: flex;
            flex-direction: {{ app()->getLocale() === 'ar' ? 'row-reverse' : 'row' }};
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .summary-card {
            flex: 1;
            min-width: 150px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        
        .summary-card-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .summary-card-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .no-print {
                display: none;
            }
            
            @page {
                margin: 1cm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <strong>{{ setting('app.name', 'Company Name') }}</strong>
        </div>
        <h1>{{ $title }}</h1>
        @if(!empty($metadata))
        <div class="metadata">
            @foreach($metadata as $key => $value)
                @if($value !== null && $value !== '')
                <span class="metadata-item">
                    <strong>{{ trans('reports.metadata.' . $key, ucfirst(str_replace('_', ' ', $key))) }}:</strong> {{ $value }}
                </span>
                @endif
            @endforeach
        </div>
        @endif
    </div>
    
    @if(isset($summary) && !empty($summary))
    <div class="summary-cards">
        @foreach($summary as $key => $value)
        <div class="summary-card">
            <div class="summary-card-label">{{ trans('reports.summary.' . $key, ucfirst(str_replace('_', ' ', $key))) }}</div>
            <div class="summary-card-value">{{ $value }}</div>
        </div>
        @endforeach
    </div>
    @endif
    
    @if(!empty($rows))
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
                        <td class="{{ isset($row['_align_' . $key]) ? 'text-' . $row['_align_' . $key] : '' }}">
                            {{ $cell }}
                        </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>{{ trans('reports.no_data', 'No data available for export.') }}</p>
    @endif
    
    <div class="footer">
        <p>{{ trans('reports.generated_on', 'Generated on') }} {{ now()->format('Y-m-d H:i:s') }} | {{ trans('reports.generated_by', 'Generated by') }}: {{ $metadata['generated_by'] ?? auth()->user()?->name ?? 'System' }}</p>
    </div>
</body>
</html>

