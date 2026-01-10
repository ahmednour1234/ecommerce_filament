<div class="space-y-4">
    <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Amount</p>
            <p class="text-lg font-semibold">${{ number_format($totalAmount, 2) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Installments</p>
            <p class="text-lg font-semibold">{{ $count }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Monthly Payment</p>
            <p class="text-lg font-semibold">${{ number_format($installmentAmount, 2) }}</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Due Date</th>
                    <th class="px-4 py-3 text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schedule as $item)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-4 py-3">{{ $item['installment_no'] }}</td>
                    <td class="px-4 py-3">{{ $item['due_date'] }}</td>
                    <td class="px-4 py-3 text-right">${{ number_format($item['amount'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
