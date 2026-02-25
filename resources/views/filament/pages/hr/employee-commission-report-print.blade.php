<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tr('pages.hr.commission_report.title', [], null, 'dashboard') ?: 'Employee Commission Report' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', 'Tajawal', Arial, sans-serif;
            direction: rtl;
            padding: 20px;
            background: white;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .header .info {
            font-size: 14px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: right;
        }
        
        th {
            background-color: #f5f5f5;
            font-weight: 600;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .total-row {
            background-color: #e8e8e8 !important;
            font-weight: 700;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ tr('pages.hr.commission_report.title', [], null, 'dashboard') ?: 'Employee Commission Report' }}</h1>
        @if(isset($employee))
            <div class="info">
                <p><strong>{{ tr('pages.hr.commission_report.filters.employee', [], null, 'dashboard') ?: 'Employee' }}:</strong> {{ $employee->full_name }}</p>
                <p><strong>{{ tr('pages.hr.commission_report.filters.date_from', [], null, 'dashboard') ?: 'From Date' }}:</strong> {{ \Carbon\Carbon::parse($date_from)->format('d/m/Y') }}</p>
                <p><strong>{{ tr('pages.hr.commission_report.filters.date_to', [], null, 'dashboard') ?: 'To Date' }}:</strong> {{ \Carbon\Carbon::parse($date_to)->format('d/m/Y') }}</p>
            </div>
        @endif
    </div>

    @if(!empty($results))
        <table>
            <thead>
                <tr>
                    <th>{{ tr('pages.hr.commission_report.table.commission', [], null, 'dashboard') ?: 'Commission' }}</th>
                    <th>{{ tr('pages.hr.commission_report.table.commission_type', [], null, 'dashboard') ?: 'Type' }}</th>
                    <th>{{ tr('pages.hr.commission_report.table.contract_count', [], null, 'dashboard') ?: 'Contract Count' }}</th>
                    <th>{{ tr('pages.hr.commission_report.table.tier_range', [], null, 'dashboard') ?: 'Tier Range' }}</th>
                    <th>{{ tr('pages.hr.commission_report.table.amount_per_contract', [], null, 'dashboard') ?: 'Amount Per Contract' }}</th>
                    <th>{{ tr('pages.hr.commission_report.table.total', [], null, 'dashboard') ?: 'Total' }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $row)
                    <tr>
                        <td>{{ $row['commission_name'] }}</td>
                        <td>{{ $row['commission_type'] }}</td>
                        <td>{{ $row['contract_count'] }}</td>
                        <td>{{ $row['tier_from'] }}-{{ $row['tier_to'] }}</td>
                        <td>{{ number_format($row['amount_per_contract'], 2) }}</td>
                        <td>{{ number_format($row['total'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2"><strong>{{ tr('pages.hr.commission_report.table.grand_total', [], null, 'dashboard') ?: 'Grand Total' }}</strong></td>
                    <td><strong>{{ $total_contracts }}</strong></td>
                    <td colspan="2"></td>
                    <td><strong>{{ number_format($total_commission, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <p style="text-align: center; padding: 40px; color: #666;">
            {{ tr('pages.hr.commission_report.no_results', [], null, 'dashboard') ?: 'No results found' }}
        </p>
    @endif

    <div class="footer">
        <p>{{ tr('pages.hr.commission_report.title', [], null, 'dashboard') ?: 'Employee Commission Report' }} - {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
