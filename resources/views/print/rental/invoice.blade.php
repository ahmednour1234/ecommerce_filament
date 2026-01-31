<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>فاتورة - {{ $contract->contract_no }}</title>
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
        .invoice-info {
            margin-bottom: 20px;
        }
        .invoice-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-info td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .invoice-info td:first-child {
            font-weight: bold;
            width: 30%;
            background-color: #f5f5f5;
        }
        .payments {
            margin-top: 20px;
        }
        .payments table {
            width: 100%;
            border-collapse: collapse;
        }
        .payments th,
        .payments td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: right;
        }
        .payments th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            border-top: 2px solid #000;
            padding-top: 20px;
        }
        .total-section {
            margin-top: 20px;
            text-align: left;
        }
        .total-section table {
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .total-section td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .total-section td:first-child {
            font-weight: bold;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>فاتورة</h1>
        <p>رقم العقد: {{ $contract->contract_no }}</p>
    </div>

    <div class="invoice-info">
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
            <tr>
                <td>تاريخ الفاتورة</td>
                <td>{{ $contract->created_at->format('Y-m-d') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>تفاصيل الفاتورة</h2>
        <table>
            <tr>
                <td>الباقة</td>
                <td>{{ $contract->package->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>المجموع الفرعي</td>
                <td>{{ number_format($contract->subtotal, 2) }} ريال</td>
            </tr>
            <tr>
                <td>الضريبة ({{ $contract->tax_percent }}%)</td>
                <td>{{ number_format($contract->tax_value, 2) }} ريال</td>
            </tr>
            <tr>
                <td><strong>الإجمالي</strong></td>
                <td><strong>{{ number_format($contract->total, 2) }} ريال</strong></td>
            </tr>
        </table>
    </div>

    @if($contract->payments->count() > 0)
    <div class="payments">
        <h2>المدفوعات</h2>
        <table>
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>المبلغ</th>
                    <th>طريقة الدفع</th>
                    <th>المرجع</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contract->payments->where('status', 'posted') as $payment)
                <tr>
                    <td>{{ $payment->paid_at->format('Y-m-d') }}</td>
                    <td>{{ number_format($payment->amount, 2) }} ريال</td>
                    <td>{{ $payment->method ?? '-' }}</td>
                    <td>{{ $payment->reference ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="total-section">
        <table>
            <tr>
                <td>المدفوع</td>
                <td>{{ number_format($contract->paid_total, 2) }} ريال</td>
            </tr>
            <tr>
                <td>المتبقي</td>
                <td>{{ number_format($contract->remaining_total, 2) }} ريال</td>
            </tr>
            <tr>
                <td>حالة الدفع</td>
                <td>{{ $contract->payment_status }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>شكراً لاستخدامكم خدماتنا</p>
    </div>
</body>
</html>
