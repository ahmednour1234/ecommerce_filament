<x-filament-panels::page>
    @php
        $record = $this->record;
    @endphp
    <div class="print-container" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="print-header mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold">{{ trans_dash('accounting.journal_entry', 'Journal Entry') }}</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ trans_dash('accounting.entry_number', 'Entry Number') }}: {{ $record->entry_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm"><strong>{{ trans_dash('accounting.date', 'Date') }}:</strong> {{ $record->entry_date->format('Y-m-d') }}</p>
                    @if($record->journal)
                        <p class="text-sm"><strong>{{ trans_dash('accounting.journal', 'Journal') }}:</strong> {{ $record->journal->name }}</p>
                    @endif
                </div>
            </div>
        </div>

        @if($record->description)
            <div class="mb-4">
                <p class="text-sm"><strong>{{ trans_dash('accounting.description', 'Description') }}:</strong> {{ $record->description }}</p>
            </div>
        @endif

        @if($record->reference)
            <div class="mb-4">
                <p class="text-sm"><strong>{{ trans_dash('accounting.reference', 'Reference') }}:</strong> {{ $record->reference }}</p>
            </div>
        @endif

        <div class="print-table-container">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-2 text-left">{{ trans_dash('accounting.account', 'Account') }}</th>
                        <th class="border border-gray-300 p-2 text-left">{{ trans_dash('accounting.description', 'Description') }}</th>
                        <th class="border border-gray-300 p-2 text-right">{{ trans_dash('accounting.debit', 'Debit') }}</th>
                        <th class="border border-gray-300 p-2 text-right">{{ trans_dash('accounting.credit', 'Credit') }}</th>
                        @php
                            $hasMultiCurrency = $record->lines->whereNotNull('currency_id')->count() > 0;
                        @endphp
                        @if($hasMultiCurrency)
                            <th class="border border-gray-300 p-2 text-center">{{ trans_dash('accounting.currency', 'Currency') }}</th>
                            <th class="border border-gray-300 p-2 text-right">{{ trans_dash('accounting.exchange_rate', 'Exchange Rate') }}</th>
                            <th class="border border-gray-300 p-2 text-right">{{ trans_dash('accounting.amount_in_base', 'Amount in Base') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($record->lines as $line)
                        <tr>
                            <td class="border border-gray-300 p-2">
                                {{ $line->account->code ?? '' }} - {{ $line->account->name ?? '' }}
                            </td>
                            <td class="border border-gray-300 p-2">
                                {{ $line->description ?? '' }}
                            </td>
                            <td class="border border-gray-300 p-2 text-right font-mono">
                                @if($line->debit > 0)
                                    {{ number_format($line->base_amount ?? $line->debit, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="border border-gray-300 p-2 text-right font-mono">
                                @if($line->credit > 0)
                                    {{ number_format($line->base_amount ?? $line->credit, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            @if($hasMultiCurrency)
                                <td class="border border-gray-300 p-2 text-center">
                                    {{ $line->currency->code ?? '-' }}
                                </td>
                                <td class="border border-gray-300 p-2 text-right font-mono">
                                    {{ $line->exchange_rate ? number_format($line->exchange_rate, 6) : '-' }}
                                </td>
                                <td class="border border-gray-300 p-2 text-right font-mono">
                                    {{ $line->base_amount ? number_format($line->base_amount, 2) : '-' }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td colspan="{{ $hasMultiCurrency ? '3' : '2' }}" class="border border-gray-300 p-2 text-right">
                            {{ trans_dash('accounting.total', 'Total') }}
                        </td>
                        <td class="border border-gray-300 p-2 text-right font-mono">
                            {{ number_format($record->total_debits, 2) }}
                        </td>
                        <td class="border border-gray-300 p-2 text-right font-mono">
                            {{ number_format($record->total_credits, 2) }}
                        </td>
                        @if($hasMultiCurrency)
                            <td colspan="3" class="border border-gray-300 p-2"></td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="print-footer mt-8">
            <div class="grid grid-cols-3 gap-4 mt-6">
                <div>
                    <p class="text-sm font-semibold">{{ trans_dash('accounting.prepared_by', 'Prepared By') }}</p>
                    <p class="text-sm mt-2">{{ $record->user->name ?? '-' }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ $record->created_at->format('Y-m-d H:i') }}</p>
                </div>
                @if($record->approved_by)
                    <div>
                        <p class="text-sm font-semibold">{{ trans_dash('accounting.approved_by', 'Approved By') }}</p>
                        <p class="text-sm mt-2">{{ $record->approvedBy->name ?? '-' }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ $record->approved_at ? $record->approved_at->format('Y-m-d H:i') : '-' }}</p>
                    </div>
                @endif
                @if($record->is_posted)
                    <div>
                        <p class="text-sm font-semibold">{{ trans_dash('accounting.posted', 'Posted') }}</p>
                        <p class="text-sm mt-2">{{ trans_dash('accounting.yes', 'Yes') }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ $record->posted_at ? $record->posted_at->format('Y-m-d H:i') : '-' }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="print-actions mt-6 flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
                {{ trans_dash('accounting.print', 'Print') }}
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                {{ trans_dash('accounting.close', 'Close') }}
            </button>
        </div>
    </div>
</x-filament-panels::page>

@push('styles')
<style>
    @media print {
        .print-actions {
            display: none;
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

