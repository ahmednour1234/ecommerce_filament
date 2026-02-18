<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $is_rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $payment->payment_no }}</title>
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

        .receipt-container {
            max-width: 600px;
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

        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
        }

        .receipt-info {
            margin: 20px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            font-weight: bold;
        }

        .info-value {
            text-align: {{ $is_rtl ? 'left' : 'right' }};
        }

        .amount-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f5f5f5;
            border: 2px solid #333;
            text-align: center;
        }

        .amount-label {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-around;
        }

        .signature-box {
            text-align: center;
            width: 200px;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <div class="company-name">{{ $is_rtl ? 'إيصال استلام' : 'Payment Receipt' }}</div>
            <div class="receipt-title">{{ $payment->payment_no }}</div>
        </div>

        <div class="receipt-info">
            <div class="info-row">
                <div class="info-label">{{ $is_rtl ? 'رقم الطلب:' : 'Request No:' }}</div>
                <div class="info-value">{{ $transfer->request_no }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ $is_rtl ? 'العميل:' : 'Customer:' }}</div>
                <div class="info-value">{{ $transfer->customer->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ $is_rtl ? 'تاريخ الدفع:' : 'Payment Date:' }}</div>
                <div class="info-value">{{ $payment->payment_date->format('Y-m-d') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ $is_rtl ? 'طريقة الدفع:' : 'Payment Method:' }}</div>
                <div class="info-value">{{ $payment->paymentMethod->name ?? 'N/A' }}</div>
            </div>
            @if($payment->fromAccount)
            <div class="info-row">
                <div class="info-label">{{ $is_rtl ? 'من حساب:' : 'From Account:' }}</div>
                <div class="info-value">{{ $payment->fromAccount->code }} - {{ $payment->fromAccount->name }}</div>
            </div>
            @endif
            @if($payment->toAccount)
            <div class="info-row">
                <div class="info-label">{{ $is_rtl ? 'إلى حساب:' : 'To Account:' }}</div>
                <div class="info-value">{{ $payment->toAccount->code }} - {{ $payment->toAccount->name }}</div>
            </div>
            @endif
            @if($payment->reference)
            <div class="info-row">
                <div class="info-label">{{ $is_rtl ? 'المرجع:' : 'Reference:' }}</div>
                <div class="info-value">{{ $payment->reference }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">{{ $is_rtl ? 'الفرع:' : 'Branch:' }}</div>
                <div class="info-value">{{ $transfer->branch->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ $is_rtl ? 'أنشئ بواسطة:' : 'Created By:' }}</div>
                <div class="info-value">{{ $payment->createdBy->name ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="amount-section">
            <div class="amount-label">{{ $is_rtl ? 'المبلغ المدفوع' : 'Amount Paid' }}</div>
            <div class="amount-value">{{ number_format($payment->amount, 2) }} SAR</div>
        </div>

        @if($payment->notes)
        <div style="margin-top: 20px;">
            <div class="info-label">{{ $is_rtl ? 'ملاحظات:' : 'Notes:' }}</div>
            <div style="margin-top: 5px;">{{ $payment->notes }}</div>
        </div>
        @endif

        <div class="signature-section">
            <div class="signature-box">
                <div>{{ $is_rtl ? 'توقيع المستلم' : 'Receiver Signature' }}</div>
            </div>
            <div class="signature-box">
                <div>{{ $is_rtl ? 'توقيع المحاسب' : 'Accountant Signature' }}</div>
            </div>
        </div>

        <div class="footer">
            <div>{{ $is_rtl ? 'تم إنشاء هذا الإيصال تلقائياً' : 'This receipt was generated automatically' }}</div>
            <div style="margin-top: 5px;">{{ date('Y-m-d H:i:s') }}</div>
        </div>
    </div>
</body>
</html>
