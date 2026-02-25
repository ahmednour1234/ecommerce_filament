<x-filament-panels::page>
    @php
        $record = $this->record;
        $items = $record->items()->with('laborer')->get();
    @endphp
    <div class="print-container" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="print-header mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold">{{ tr('housing.salary_batch.print.title', [], null, 'dashboard') ?: 'كشف رواتب العمالة' }}</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ tr('housing.salary_batch.month', [], null, 'dashboard') ?: 'الشهر' }}: {{ $record->month }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm"><strong>{{ tr('housing.salary_batch.print.date', [], null, 'dashboard') ?: 'تاريخ الطباعة' }}:</strong> {{ now()->format('Y-m-d') }}</p>
                    <p class="text-sm"><strong>{{ tr('housing.salary_batch.total_salaries', [], null, 'dashboard') ?: 'إجمالي الرواتب' }}:</strong> {{ number_format($record->total_salaries, 2) }} {{ tr('common.currency', [], null, 'dashboard') ?: 'ريال' }}</p>
                </div>
            </div>
        </div>

        <div class="print-table-container">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-2 text-right">{{ tr('tables.housing.salary_batch.print.worker_name', [], null, 'dashboard') ?: 'اسم العامل' }}</th>
                        <th class="border border-gray-300 p-2 text-right">{{ tr('tables.housing.salary_batch.print.passport', [], null, 'dashboard') ?: 'رقم الجواز' }}</th>
                        <th class="border border-gray-300 p-2 text-right">{{ tr('tables.housing.salary_batch.basic_salary', [], null, 'dashboard') ?: 'الراتب الأساسي' }}</th>
                        <th class="border border-gray-300 p-2 text-right">{{ tr('tables.housing.salary_batch.deductions', [], null, 'dashboard') ?: 'الخصومات' }}</th>
                        <th class="border border-gray-300 p-2 text-right">{{ tr('tables.housing.salary_batch.net_salary', [], null, 'dashboard') ?: 'صافي الراتب' }}</th>
                        <th class="border border-gray-300 p-2 text-center">{{ tr('tables.housing.salary_batch.status', [], null, 'dashboard') ?: 'الحالة' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td class="border border-gray-300 p-2">
                                {{ $item->laborer->name_ar ?? '-' }}
                            </td>
                            <td class="border border-gray-300 p-2">
                                {{ $item->laborer->passport_number ?? '-' }}
                            </td>
                            <td class="border border-gray-300 p-2 text-right font-mono">
                                {{ number_format($item->basic_salary, 2) }}
                            </td>
                            <td class="border border-gray-300 p-2 text-right font-mono">
                                {{ number_format($item->deductions_total, 2) }}
                            </td>
                            <td class="border border-gray-300 p-2 text-right font-mono font-bold">
                                {{ number_format($item->net_salary, 2) }}
                            </td>
                            <td class="border border-gray-300 p-2 text-center">
                                <span class="px-2 py-1 rounded text-xs {{ $item->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $item->status === 'paid' ? (tr('housing.salary_batch.status.paid', [], null, 'dashboard') ?: 'مدفوع') : (tr('housing.salary_batch.status.pending', [], null, 'dashboard') ?: 'معلق') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td colspan="2" class="border border-gray-300 p-2 text-right">
                            {{ tr('housing.salary_batch.print.total', [], null, 'dashboard') ?: 'الإجمالي' }}
                        </td>
                        <td class="border border-gray-300 p-2 text-right font-mono">
                            {{ number_format($items->sum('basic_salary'), 2) }}
                        </td>
                        <td class="border border-gray-300 p-2 text-right font-mono">
                            {{ number_format($items->sum('deductions_total'), 2) }}
                        </td>
                        <td class="border border-gray-300 p-2 text-right font-mono">
                            {{ number_format($items->sum('net_salary'), 2) }}
                        </td>
                        <td class="border border-gray-300 p-2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="print-summary mt-6 grid grid-cols-3 gap-4">
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm font-semibold mb-2">{{ tr('housing.salary_batch.total_paid', [], null, 'dashboard') ?: 'المدفوع' }}</p>
                <p class="text-lg font-bold">{{ number_format($record->total_paid, 2) }} {{ tr('common.currency', [], null, 'dashboard') ?: 'ريال' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm font-semibold mb-2">{{ tr('housing.salary_batch.total_pending', [], null, 'dashboard') ?: 'المعلق' }}</p>
                <p class="text-lg font-bold">{{ number_format($record->total_pending, 2) }} {{ tr('common.currency', [], null, 'dashboard') ?: 'ريال' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm font-semibold mb-2">{{ tr('housing.salary_batch.total_deductions', [], null, 'dashboard') ?: 'إجمالي الخصومات' }}</p>
                <p class="text-lg font-bold">{{ number_format($record->total_deductions, 2) }} {{ tr('common.currency', [], null, 'dashboard') ?: 'ريال' }}</p>
            </div>
        </div>

        <div class="print-footer mt-8">
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div>
                    <p class="text-sm font-semibold">{{ tr('housing.salary_batch.print.prepared_by', [], null, 'dashboard') ?: 'أعد بواسطة' }}</p>
                    <p class="text-sm mt-2">{{ $record->creator->name ?? auth()->user()->name ?? '-' }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ $record->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold">{{ tr('housing.salary_batch.print.workers_count', [], null, 'dashboard') ?: 'عدد العمالة' }}</p>
                    <p class="text-sm mt-2">{{ $items->count() }}</p>
                </div>
            </div>
        </div>

        <div class="print-actions mt-6 flex gap-2 no-print">
            <button onclick="window.print()" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
                {{ tr('actions.print', [], null, 'dashboard') ?: 'طباعة' }}
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                {{ tr('actions.close', [], null, 'dashboard') ?: 'إغلاق' }}
            </button>
        </div>
    </div>
</x-filament-panels::page>

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
    
    @media print {
        .print-actions,
        .no-print {
            display: none !important;
        }
        
        .print-container {
            padding: 20px;
        }
        
        body {
            margin: 0;
            padding: 0;
        }
    }
    
    .print-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Cairo', 'Tajawal', Arial, sans-serif;
    }
    
    .print-table-container {
        overflow-x: auto;
    }
    
    table {
        font-size: 12px;
    }
    
    [dir="rtl"] table {
        direction: rtl;
    }
    
    [dir="rtl"] .text-right {
        text-align: left;
    }
    
    [dir="rtl"] .text-left {
        text-align: right;
    }
</style>
@endpush
