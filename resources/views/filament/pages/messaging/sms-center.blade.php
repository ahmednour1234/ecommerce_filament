<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Widgets --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                    {{ tr('widgets.sent_count', [], null, 'dashboard') ?: 'الرسائل المرسلة' }}
                </h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $this->getSentCount() }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                    {{ tr('widgets.current_balance', [], null, 'dashboard') ?: 'الرصيد الحالي' }}
                </h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $this->getCurrentBalance() }}
                </p>
            </div>
        </div>

        {{-- Contacts Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold">
                    {{ tr('pages.sms_center.contacts_list', [], null, 'dashboard') ?: 'قائمة الأرقام' }}
                </h2>
            </div>
            <div class="p-4">
                {{ $this->table }}
            </div>
        </div>

        {{-- Send SMS Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold">
                    {{ tr('pages.sms_center.send_section', [], null, 'dashboard') ?: 'إرسال رسالة' }}
                </h2>
            </div>
            <form wire:submit="send" class="p-4">
                {{ $this->form }}
                <div class="mt-4">
                    <x-filament::button type="submit">
                        {{ tr('actions.send', [], null, 'dashboard') ?: 'إرسال' }}
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
