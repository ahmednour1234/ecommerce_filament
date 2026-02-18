<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $is_rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $transfer->request_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ public_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ public_path('fonts/Tajawal-Regular.ttf') }}') format('truetype');
            font-weight: normal;
        }
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ public_path('fonts/Tajawal-Bold.ttf') }}') format('truetype');
            font-weight: bold;
        }

        body {
            font-family: {{ $is_rtl ? "'Tajawal', 'DejaVu Sans', Arial, sans-serif" : "'DejaVu Sans', Arial, sans-serif" }};
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            direction: {{ $is_rtl ? 'rtl' : 'ltr' }};
            padding: 20px;
        }

        .invoice-container {
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

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .info-section {
            flex: 1;
            min-width: 250px;
            margin: 10px;
        }

        .info-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 10px;
            text-align: {{ $is_rtl ? 'right' : 'left' }};
            border: 1px solid #ddd;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .text-right {
            text-align: {{ $is_rtl ? 'left' : 'right' }};
        }

        .total-section {
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            padding: 5px 0;
        }

        .total-final {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-name">{{ $is_rtl ? 'فاتورة نقل الخدمات' : 'Service Transfer Invoice' }}</div>
            <div class="invoice-title">{{ $transfer->request_no }}</div>
        </div>

        <div class="invoice-info">
            <div class="info-section">
                <div class="info-label">{{ $is_rtl ? 'العميل:' : 'Customer:' }}</div>
                <div>{{ $transfer->customer->name ?? 'N/A' }}</div>
                <div class="info-label" style="margin-top: 10px;">{{ $is_rtl ? 'العاملة:' : 'Worker:' }}</div>
                <div>{{ $transfer->worker->name_ar ?? 'N/A' }}</div>
                <div class="info-label" style="margin-top: 10px;">{{ $is_rtl ? 'الفرع:' : 'Branch:' }}</div>
                <div>{{ $transfer->branch->name ?? 'N/A' }}</div>
            </div>
            <div class="info-section">
                <div class="info-label">{{ $is_rtl ? 'تاريخ الطلب:' : 'Request Date:' }}</div>
                <div>{{ $transfer->request_date->format('Y-m-d') }}</div>
                <div class="info-label" style="margin-top: 10px;">{{ $is_rtl ? 'حالة الدفع:' : 'Payment Status:' }}</div>
                <div>{{ $transfer->payment_status }}</div>
                <div class="info-label" style="margin-top: 10px;">{{ $is_rtl ? 'أنشئ بواسطة:' : 'Created By:' }}</div>
                <div>{{ $transfer->createdBy->name ?? 'N/A' }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>{{ $is_rtl ? 'الوصف' : 'Description' }}</th>
                    <th class="text-right">{{ $is_rtl ? 'المبلغ' : 'Amount' }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $is_rtl ? 'السعر الأساسي' : 'Base Price' }}</td>
                    <td class="text-right">{{ number_format($transfer->base_price, 2) }} SAR</td>
                </tr>
                <tr>
                    <td>{{ $is_rtl ? 'التكاليف الخارجية' : 'External Cost' }}</td>
                    <td class="text-right">{{ number_format($transfer->external_cost, 2) }} SAR</td>
                </tr>
                <tr>
                    <td>{{ $is_rtl ? 'الرسوم الحكومية' : 'Government Fees' }}</td>
                    <td class="text-right">{{ number_format($transfer->government_fees, 2) }} SAR</td>
                </tr>
                <tr>
                    <td>{{ $is_rtl ? 'الضريبة (' . $transfer->tax_percent . '%)' : 'Tax (' . $transfer->tax_percent . '%)' }}</td>
                    <td class="text-right">{{ number_format($transfer->tax_value, 2) }} SAR</td>
                </tr>
                @if($transfer->discount_value > 0)
                <tr>
                    <td>{{ $is_rtl ? 'الخصم (' . $transfer->discount_percent . '%)' : 'Discount (' . $transfer->discount_percent . '%)' }}</td>
                    <td class="text-right">-{{ number_format($transfer->discount_value, 2) }} SAR</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <div><strong>{{ $is_rtl ? 'المبلغ الإجمالي:' : 'Total Amount:' }}</strong></div>
                <div class="text-right"><strong>{{ number_format($transfer->total_amount, 2) }} SAR</strong></div>
            </div>
            <div class="total-row">
                <div>{{ $is_rtl ? 'المبلغ المدفوع:' : 'Total Paid:' }}</div>
                <div class="text-right">{{ number_format($transfer->totalPaid(), 2) }} SAR</div>
            </div>
            <div class="total-row total-final">
                <div>{{ $is_rtl ? 'المبلغ المتبقي:' : 'Remaining Amount:' }}</div>
                <div class="text-right">{{ number_format($transfer->remainingAmount(), 2) }} SAR</div>
            </div>
        </div>

        @if($transfer->payments->count() > 0)
        <h3 style="margin-top: 30px; margin-bottom: 10px;">{{ $is_rtl ? 'سجل المدفوعات' : 'Payments History' }}</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ $is_rtl ? 'رقم الدفعة' : 'Payment No' }}</th>
                    <th>{{ $is_rtl ? 'التاريخ' : 'Date' }}</th>
                    <th class="text-right">{{ $is_rtl ? 'المبلغ' : 'Amount' }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfer->payments as $payment)
                <tr>
                    <td>{{ $payment->payment_no }}</td>
                    <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                    <td class="text-right">{{ number_format($payment->amount, 2) }} SAR</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($transfer->notes)
        <div style="margin-top: 20px;">
            <div class="info-label">{{ $is_rtl ? 'ملاحظات:' : 'Notes:' }}</div>
            <div>{{ $transfer->notes }}</div>
        </div>
        @endif

        <div class="footer">
            <div>{{ $is_rtl ? 'تم إنشاء هذه الفاتورة تلقائياً' : 'This invoice was generated automatically' }}</div>
            <div style="margin-top: 5px;">{{ date('Y-m-d H:i:s') }}</div>
        </div>
    </div>
</body>
</html>
