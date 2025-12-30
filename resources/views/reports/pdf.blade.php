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
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72px;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            font-weight: bold;
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
    @if(isset($metadata['is_draft']) && $metadata['is_draft'])
    <div class="watermark">DRAFT</div>
    @endif
    
    <div class="header">
        <h1>{{ $title }}</h1>
        @if(!empty($metadata))
        <div class="metadata">
            @foreach($metadata as $key => $value)
                @if($key !== 'is_draft' && $value !== null && $value !== '')
                <span class="metadata-item">
                    <strong>{{ trans_dash('reports.metadata.' . $key, ucfirst(str_replace('_', ' ', $key))) }}:</strong> {{ $value }}
                </span>
                @endif
            @endforeach
        </div>
        @endif
    </div>
    
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
    <p>{{ trans_dash('reports.no_data', 'No data available for export.') }}</p>
    @endif
    
    <div class="footer">
        <p>{{ trans_dash('reports.generated_on', 'Generated on') }} {{ now()->format('Y-m-d H:i:s') }} | {{ trans_dash('reports.generated_by', 'Generated by') }}: {{ $metadata['generated_by'] ?? auth()->user()?->name ?? 'System' }}</p>
    </div>
</body>
</html>

