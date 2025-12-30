<div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="text-center">
            <div class="text-sm font-medium text-gray-600 mb-1">
                {{ trans_dash('accounting.total_debit', 'Total Debit') }}
            </div>
            <div class="text-2xl font-bold text-green-700">
                {{ number_format($totalDebits, 2) }} {{ $currencyCode }}
            </div>
        </div>
        <div class="text-center">
            <div class="text-sm font-medium text-gray-600 mb-1">
                {{ trans_dash('accounting.total_credit', 'Total Credit') }}
            </div>
            <div class="text-2xl font-bold text-red-700">
                {{ number_format($totalCredits, 2) }} {{ $currencyCode }}
            </div>
        </div>
        <div class="text-center">
            <div class="text-sm font-medium text-gray-600 mb-1">
                {{ trans_dash('accounting.difference', 'Difference') }}
            </div>
            <div class="text-2xl font-bold {{ $isBalanced ? 'text-green-600' : 'text-red-600' }}">
                {{ number_format($difference, 2) }} {{ $currencyCode }}
            </div>
            @if(!$isBalanced)
                <div class="text-xs text-red-600 mt-1 font-semibold">
                    {{ trans_dash('accounting.entries_not_balanced', 'Entries are not balanced') }}
                </div>
            @else
                <div class="text-xs text-green-600 mt-1">
                    {{ trans_dash('accounting.entries_balanced', 'Entries are balanced') }}
                </div>
            @endif
        </div>
    </div>
</div>

