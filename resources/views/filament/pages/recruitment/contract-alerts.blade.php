<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">
                {{ tr('recruitment_contract.alerts.description', [], null, 'dashboard') ?: 'عقود تحتاج إلى متابعة' }}
            </h2>
            <p class="text-gray-600">
                {{ tr('recruitment_contract.alerts.description_text', [], null, 'dashboard') ?: 'العقود التالية تحتاج إلى متابعة فورية' }}
            </p>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
