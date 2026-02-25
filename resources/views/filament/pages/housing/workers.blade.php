<x-filament-panels::page class="rtl-dashboard">
    <div class="space-y-6">
        {{-- Stats Cards --}}
        @php
            $stats = $this->getStats();
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-blue-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.workers.stats.total', [], null, 'dashboard') ?: 'إجمالي العمالة' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-red-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.workers.stats.stopped', [], null, 'dashboard') ?: 'موقوفة' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['stopped'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-yellow-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.workers.stats.on_leave', [], null, 'dashboard') ?: 'في إجازة' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['on_leave'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-gray-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.workers.stats.outside_service', [], null, 'dashboard') ?: 'عمالة خارج الخدمة' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['outside_service'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-purple-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.workers.stats.transfer_kafala', [], null, 'dashboard') ?: 'نقل الكفالة' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['transfer_kafala'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-r-4 border-green-500">
                <p class="text-sm text-gray-600 mb-1">{{ tr('housing.workers.stats.rented', [], null, 'dashboard') ?: 'عمالة مستأجرة' }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['rented'] ?? 0) }}</p>
            </div>
        </div>

        {{-- Workers Table --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
