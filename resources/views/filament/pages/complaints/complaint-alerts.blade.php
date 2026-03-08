<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">
                {{ tr('complaint.alerts.description', [], null, 'dashboard') ?: 'شكاوي تحتاج إلى متابعة' }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
                {{ tr('complaint.alerts.description_text', [], null, 'dashboard') ?: 'الشكاوي قيد الانتظار أو قيد المعالجة' }}
            </p>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
