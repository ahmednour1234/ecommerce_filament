<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>عقد التأجير - {{ $contract->contract_no }}</title>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path("fonts/Cairo-Regular.ttf") }}') format('truetype');
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Cairo', 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 12px;
            line-height: 1.6;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .contract-info {
            margin-bottom: 20px;
        }
        .contract-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .contract-info td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .contract-info td:first-child {
            font-weight: bold;
            width: 30%;
            background-color: #f5f5f5;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            font-size: 18px;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            border-top: 2px solid #000;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>عقد التأجير</h1>
        <p>رقم العقد: {{ $contract->contract_no }}</p>
    </div>

    <div class="contract-info">
        <table>
            <tr>
                <td>رقم العقد</td>
                <td>{{ $contract->contract_no }}</td>
            </tr>
            <tr>
                <td>العميل</td>
                <td>{{ $contract->customer->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>الفرع</td>
                <td>{{ $contract->branch->name ?? '-' }}</td>
            </tr>
            @if($contract->worker)
            <tr>
                <td>العامل/العاملة</td>
                <td>{{ $contract->worker->name_ar ?? '-' }}</td>
            </tr>
            @endif
            @if($contract->package)
            <tr>
                <td>الباقة</td>
                <td>{{ $contract->package->name ?? '-' }}</td>
            </tr>
            @endif
            <tr>
                <td>تاريخ البدء</td>
                <td>{{ $contract->start_date->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <td>تاريخ الانتهاء</td>
                <td>{{ $contract->end_date->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <td>المدة</td>
                <td>{{ $contract->duration }} {{ $contract->duration_type === 'month' ? 'شهر' : ($contract->duration_type === 'year' ? 'سنة' : 'يوم') }}</td>
            </tr>
            <tr>
                <td>الحالة</td>
                <td>{{ $contract->status }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>المعلومات المالية</h2>
        <table>
            <tr>
                <td>المجموع الفرعي</td>
                <td>{{ number_format($contract->subtotal, 2) }} ريال</td>
            </tr>
            <tr>
                <td>الضريبة</td>
                <td>{{ number_format($contract->tax_value, 2) }} ريال</td>
            </tr>
            <tr>
                <td>الإجمالي</td>
                <td>{{ number_format($contract->total, 2) }} ريال</td>
            </tr>
            <tr>
                <td>المدفوع</td>
                <td>{{ number_format($contract->paid_total, 2) }} ريال</td>
            </tr>
            <tr>
                <td>المتبقي</td>
                <td>{{ number_format($contract->remaining_total, 2) }} ريال</td>
            </tr>
        </table>
    </div>

    @if($contract->notes)
    <div class="section">
        <h2>ملاحظات</h2>
        <p>{{ $contract->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>تم الإنشاء في: {{ $contract->created_at->format('Y-m-d H:i') }}</p>
    </div>
</body>
</html>
