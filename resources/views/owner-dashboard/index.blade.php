@extends('layouts.owner-dashboard')

@section('title', 'لوحة تحكم مكتب الاستقدام')

@section('content')
<div class="max-w-screen-2xl mx-auto px-4 py-6 space-y-6">

    {{-- ══════════════════ TOP NAV CARD ══════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <div class="flex flex-col lg:flex-row-reverse lg:items-start gap-4">
            {{-- Title (right side in RTL = flex-row-reverse start) --}}
            <div class="flex-1 text-right">
                <h1 class="text-2xl font-bold text-gray-900">لوحة تحكم مكتب الاستقدام</h1>
                <p class="text-sm text-gray-500 mt-1">واجهة سهلة وواضحة لمتابعة الطلبات، الموارد البشرية، المحاسبة، الفروع، والشكاوى</p>
            </div>

            {{-- Nav Pills (left side) --}}
            <div class="flex flex-wrap gap-2 justify-start">
                <a href="{{ route('owner.dashboard') }}"
                   class="px-4 py-2 rounded-full text-sm font-semibold bg-emerald-500 text-white">الرئيسية</a>
                <a href="{{ url('/admin/recruitment-contracts') }}"
                   class="px-4 py-2 rounded-full text-sm text-gray-600 hover:bg-gray-100">الطلبات</a>
                <a href="{{ url('/admin/employees') }}"
                   class="px-4 py-2 rounded-full text-sm text-gray-600 hover:bg-gray-100">الموارد البشرية</a>
                <a href="{{ url('/admin/rental-contracts') }}"
                   class="px-4 py-2 rounded-full text-sm text-gray-600 hover:bg-gray-100">العقود</a>
                <a href="{{ url('/admin/journal-entries') }}"
                   class="px-4 py-2 rounded-full text-sm text-gray-600 hover:bg-gray-100">المحاسبة</a>
                <a href="{{ url('/admin/complaints') }}"
                   class="px-4 py-2 rounded-full text-sm text-gray-600 hover:bg-gray-100">الشكاوى</a>
                <a href="{{ url('/admin/branches') }}"
                   class="px-4 py-2 rounded-full text-sm text-gray-600 hover:bg-gray-100">الفروع</a>
                <a href="{{ route('filament.admin.pages.dashboard') }}"
                   class="px-4 py-2 rounded-full text-sm text-gray-600 hover:bg-gray-100">التقارير</a>
            </div>
        </div>
    </div>

    {{-- ══════════════════ ROW: DARK SUMMARY + STATS ══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        {{-- Dark summary card --}}
        <div class="bg-gray-900 text-white rounded-2xl p-6 flex flex-col justify-between lg:col-span-1">
            <div>
                <p class="text-xs text-gray-400 font-medium mb-1">ملخص اليوم</p>
                <p class="text-5xl font-bold">{{ $todayPending }}</p>
            </div>
            <div class="mt-4">
                <p class="text-xs text-gray-400">إجراء يحتاج متابعة من الإدارة</p>
                <p class="text-sm font-semibold mt-2">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">صاحب الشركة</p>
            </div>
        </div>

        {{-- Stats row 1 (4 cards) --}}
        <div class="lg:col-span-4 grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
            $stats1 = [
                ['icon' => '📋', 'label' => 'إجمالي الطلبات',    'value' => $totalContracts,      'sub' => 'طلبات الاستقدام الكلية', 'url' => url('/admin/recruitment-contracts')],
                ['icon' => '⏳', 'label' => 'طلبات قيد التنفيذ', 'value' => $inProgressContracts, 'sub' => 'تحتاج متابعة يومية',     'url' => url('/admin/recruitment-contracts')],
                ['icon' => '✉️', 'label' => 'طلبات الإجازة',     'value' => $pendingLeave,         'sub' => 'بانتظار الاعتماد',        'url' => url('/admin/leave-requests')],
                ['icon' => '🕐', 'label' => 'طلبات الاستئذان',   'value' => $pendingExcuse,        'sub' => 'معلقة لدى الإدارة',       'url' => url('/admin/excuse-requests')],
            ];
            @endphp
            @foreach($stats1 as $stat)
            <a href="{{ $stat['url'] }}" class="bg-white rounded-2xl p-5 stat-card shadow-sm flex items-start gap-3 hover:shadow-md transition-shadow">
                <div class="text-3xl">{{ $stat['icon'] }}</div>
                <div class="text-right flex-1">
                    <p class="text-xs text-gray-500 font-medium">{{ $stat['label'] }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stat['value']) }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $stat['sub'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════ STATS ROW 2 ══════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
        $stats2 = [
            ['icon' => '🏢', 'label' => 'العقود الإيجارية',    'value' => $activeRentals,          'sub' => 'عقود فعالة حالياً',  'url' => url('/admin/rental-contracts')],
            ['icon' => '💰', 'label' => 'قيود محاسبية معلقة', 'value' => $pendingJournals,        'sub' => 'بانتظار الموافقة',   'url' => url('/admin/journal-entries')],
            ['icon' => '✍️', 'label' => 'عدد الشكاوى',        'value' => $openComplaints,         'sub' => 'مفتوحة ومعلقة',     'url' => url('/admin/complaints')],
            ['icon' => '⭐', 'label' => 'نسبة رضا العملاء',   'value' => $satisfactionRate . '%', 'sub' => 'مؤشر عام ممتاز',    'url' => url('/admin/complaints')],
        ];
        @endphp
        @foreach($stats2 as $stat)
        <a href="{{ $stat['url'] }}" class="bg-white rounded-2xl p-5 stat-card shadow-sm flex items-start gap-3 hover:shadow-md transition-shadow">
            <div class="text-3xl">{{ $stat['icon'] }}</div>
            <div class="text-right flex-1">
                <p class="text-xs text-gray-500 font-medium">{{ $stat['label'] }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stat['value'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stat['sub'] }}</p>
            </div>
        </a>
        @endforeach
    </div>

    {{-- ══════════════════ CHARTS ROW ══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Monthly bar chart --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs text-gray-400">آخر 6 أشهر</p>
                <h3 class="text-base font-bold text-gray-900">حركة الطلبات الشهرية</h3>
            </div>
            <canvas id="monthlyChart" height="180"></canvas>
        </div>

        {{-- Nationality bars --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h3 class="text-base font-bold text-gray-900 text-right mb-5">الجنسيات الأكثر طلباً</h3>
            @php
            $maxNat = $topNationalities->max('count') ?: 1;
            @endphp
            @foreach($topNationalities as $nat)
            <div class="mb-4">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-500">{{ $nat['percent'] }}%</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $nat['name'] }}</span>
                </div>
                <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full"
                         style="width: {{ $nat['percent'] }}%"></div>
                </div>
            </div>
            @endforeach
            @if($topNationalities->isEmpty())
            <p class="text-center text-gray-400 text-sm py-8">لا توجد بيانات</p>
            @endif
        </div>
    </div>

    {{-- ══════════════════ ACCOUNTING + HR PENDING ══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Accounting --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-gray-400">بانتظار المدير</span>
                <h3 class="text-base font-bold text-gray-900">المحاسبة والاعتمادات</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ url('/admin/journal-entries') }}"
                   class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex items-center justify-between hover:bg-amber-100 transition-colors block">
                    <span class="text-2xl font-bold text-amber-600">{{ $pendingJournals }}</span>
                    <div class="text-right">
                        <p class="font-bold text-gray-900 text-sm">قيود يومية تنتظر الاعتماد</p>
                        <p class="text-xs text-gray-500 mt-0.5">تحتاج اعتماد سريع</p>
                    </div>
                </a>
                <a href="{{ url('/admin/vouchers') }}"
                   class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex items-center justify-between hover:bg-amber-100 transition-colors block">
                    <span class="text-2xl font-bold text-amber-600">{{ $pendingVouchers }}</span>
                    <div class="text-right">
                        <p class="font-bold text-gray-900 text-sm">سندات صرف تنتظر الموافقة</p>
                        <p class="text-xs text-gray-500 mt-0.5">تحتاج اعتماد سريع</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- HR --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-gray-400">المهام الحالية</span>
                <h3 class="text-base font-bold text-gray-900">الموارد البشرية HR</h3>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ url('/admin/excuse-requests') }}"
                   class="border border-gray-100 rounded-xl p-4 text-right hover:bg-gray-50 transition-colors block">
                    <p class="text-xs text-gray-500">طلبات استئذان اليوم</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $pendingExcuse }}</p>
                </a>
                <a href="{{ url('/admin/leave-requests') }}"
                   class="border border-gray-100 rounded-xl p-4 text-right hover:bg-gray-50 transition-colors block">
                    <p class="text-xs text-gray-500">طلبات إجازة بانتظار المدير</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $pendingLeave }}</p>
                </a>
                <a href="{{ url('/admin/employees') }}"
                   class="border border-gray-100 rounded-xl p-4 text-right hover:bg-gray-50 transition-colors block">
                    <p class="text-xs text-gray-500">مقابلات مجدولة هذا الأسبوع</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $scheduledInterviews }}</p>
                </a>
                <a href="{{ url('/admin/rental-contracts') }}"
                   class="border border-gray-100 rounded-xl p-4 text-right hover:bg-gray-50 transition-colors block">
                    <p class="text-xs text-gray-500">عقود موظفين تحتاج تجديد</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $activeRentals }}</p>
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════ ALERTS + BRANCH TABLE ══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Alerts --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h3 class="text-base font-bold text-gray-900 text-right mb-4">تنبيهات سريعة</h3>
            <div class="space-y-3">
                @if($pendingLeave > 0)
                <div class="bg-amber-50 border-r-4 border-amber-400 rounded-xl px-4 py-3 text-right">
                    <a href="{{ url('/admin/leave-requests') }}" class="text-sm font-bold text-amber-700">
                        {{ $pendingLeave }} طلب إجازة بانتظار الاعتماد
                    </a>
                    <p class="text-xs text-gray-500 mt-0.5">يفضل مراجعتها اليوم</p>
                </div>
                @endif
                @if($pendingJournals > 0 || $pendingVouchers > 0)
                <div class="bg-rose-50 border-r-4 border-rose-400 rounded-xl px-4 py-3 text-right">
                    <a href="{{ url('/admin/journal-entries') }}" class="text-sm font-bold text-rose-700">
                        {{ $pendingJournals + $pendingVouchers }} معاملة محاسبية معلقة
                    </a>
                    <p class="text-xs text-gray-500 mt-0.5">هناك موافقات متأخرة</p>
                </div>
                @endif
                @if($openComplaints > 0)
                <div class="bg-blue-50 border-r-4 border-blue-400 rounded-xl px-4 py-3 text-right">
                    <a href="{{ url('/admin/complaints') }}" class="text-sm font-bold text-blue-700">
                        {{ $openComplaints }} شكوى مسجلة
                    </a>
                    <p class="text-xs text-gray-500 mt-0.5">بعض الفروع تحتاج تحسين سرعة المعالجة</p>
                </div>
                @endif
                @if($pendingLeave === 0 && ($pendingJournals + $pendingVouchers) === 0 && $openComplaints === 0)
                <div class="bg-emerald-50 border-r-4 border-emerald-400 rounded-xl px-4 py-3 text-right">
                    <p class="text-sm font-bold text-emerald-700">لا توجد تنبيهات عاجلة</p>
                    <p class="text-xs text-gray-500 mt-0.5">كل شيء على ما يرام اليوم</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Branch revenue table --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <a href="{{ url('/admin/branch-transactions') }}" class="text-xs text-emerald-600 hover:underline">عرض التقارير المالية</a>
                <h3 class="text-base font-bold text-gray-900">إيرادات ومصاريف الفروع</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead>
                        <tr class="text-xs text-gray-400 border-b border-gray-100">
                            <th class="pb-2 font-medium">الأداء</th>
                            <th class="pb-2 font-medium">الشكاوى</th>
                            <th class="pb-2 font-medium">المصاريف</th>
                            <th class="pb-2 font-medium">الإيرادات</th>
                            <th class="pb-2 font-medium">الفرع</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($branchStats as $b)
                        <tr class="text-sm cursor-pointer hover:bg-gray-50 transition-colors"
                            onclick="window.location='{{ url('/admin/branches') }}'">
                            <td class="py-3">
                                @php
                                $badgeColor = match($b['rating']) {
                                    'ممتاز'   => 'bg-emerald-100 text-emerald-700',
                                    'جيد جداً'=> 'bg-blue-100 text-blue-700',
                                    'جيد'     => 'bg-amber-100 text-amber-700',
                                    default   => 'bg-gray-100 text-gray-600',
                                };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeColor }}">{{ $b['rating'] }}</span>
                            </td>
                            <td class="py-3 text-gray-600">{{ $b['complaints'] }}</td>
                            <td class="py-3 text-red-600 font-semibold">{{ number_format($b['expense']) }} ر.س</td>
                            <td class="py-3 text-gray-900 font-semibold">{{ number_format($b['income']) }} ر.س</td>
                            <td class="py-3 font-medium text-gray-900">{{ $b['name'] }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-400">لا توجد بيانات فروع</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════ KPI + LATEST CONTRACTS ══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- KPI Card --}}
        <div class="bg-gray-900 text-white rounded-2xl p-6 flex flex-col">
            <h3 class="text-base font-bold mb-4 text-right">مؤشر الإدارة اليومي</h3>
            <p class="text-6xl font-bold text-center my-4">{{ $kpiRate }}%</p>
            <p class="text-xs text-gray-400 text-center mb-6">نسبة إنجاز المهام والاعتمادات اليومية</p>
            <div class="space-y-2 border-t border-gray-700 pt-4 text-sm">
                <a href="{{ url('/admin/journal-entries') }}"
                   class="flex justify-between hover:text-emerald-400 transition-colors">
                    <span class="text-white font-semibold">{{ $approvedToday }}</span>
                    <span class="text-gray-400">الموافقات المنجزة</span>
                </a>
                <a href="{{ url('/admin/complaints') }}"
                   class="flex justify-between hover:text-emerald-400 transition-colors">
                    <span class="text-white font-semibold">{{ $resolvedComplaints }}</span>
                    <span class="text-gray-400">الشكاوى المغلقة</span>
                </a>
                <a href="{{ url('/admin/rental-contracts') }}"
                   class="flex justify-between hover:text-emerald-400 transition-colors">
                    <span class="text-white font-semibold">{{ $activeContracts }}</span>
                    <span class="text-gray-400">العقود النشطة</span>
                </a>
            </div>
        </div>

        {{-- Latest contracts --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <a href="{{ url('/admin/recruitment-contracts') }}" class="text-xs text-emerald-600 hover:underline">عرض الكل</a>
                <h3 class="text-base font-bold text-gray-900">آخر الطلبات</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead>
                        <tr class="text-xs text-gray-400 border-b border-gray-100">
                            <th class="pb-2 font-medium">القيمة</th>
                            <th class="pb-2 font-medium">الحالة</th>
                            <th class="pb-2 font-medium">الخدمة</th>
                            <th class="pb-2 font-medium">العميل</th>
                            <th class="pb-2 font-medium">رقم الطلب</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($latestContracts as $contract)
                        <tr class="cursor-pointer hover:bg-gray-50 transition-colors"
                            onclick="window.location='{{ url('/admin/recruitment-contracts/' . $contract->id . '/edit') }}'">
                            <td class="py-3 font-semibold text-gray-900">
                                {{ number_format($contract->total_cost ?? 0) }} ر.س
                            </td>
                            <td class="py-3">
                                @php
                                $statusMap = [
                                    'new'                     => ['label' => 'جديد',          'class' => 'bg-blue-100 text-blue-700'],
                                    'processing'              => ['label' => 'قيد المراجعة',   'class' => 'bg-amber-100 text-amber-700'],
                                    'received'                => ['label' => 'مكتمل',          'class' => 'bg-emerald-100 text-emerald-700'],
                                    'visa_issued'             => ['label' => 'تم إصدار الفيزا','class' => 'bg-indigo-100 text-indigo-700'],
                                    'waiting_flight_booking'  => ['label' => 'بانتظار الدفع',  'class' => 'bg-rose-100 text-rose-700'],
                                ];
                                $s = $statusMap[$contract->status] ?? ['label' => $contract->status, 'class' => 'bg-gray-100 text-gray-600'];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $s['class'] }}">{{ $s['label'] }}</span>
                            </td>
                            <td class="py-3 text-gray-600">{{ $contract->nationality?->name_ar ?? '—' }}</td>
                            <td class="py-3 text-gray-800 font-medium">{{ $contract->client?->name_ar ?? $contract->client?->name_en ?? '—' }}</td>
                            <td class="py-3 font-semibold text-gray-900">#{{ $contract->contract_no ?? $contract->id }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-400">لا توجد طلبات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════ BRANCH COMPLAINTS RESOLUTION ══════════════════ --}}
    @php
    $targetBranches = ['الرياض', 'عرعر', 'حفر الباطن'];
    $filteredBranchStats = collect($branchStats)->filter(
        fn($b) => in_array($b['name'], $targetBranches)
    )->values();
    @endphp
    <div class="bg-white rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <p class="text-xs text-gray-400">كلما ارتفعت نسبة الحل كان الأداء أفضل</p>
            <h3 class="text-base font-bold text-gray-900">أداء الفروع في معالجة الشكاوى</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($filteredBranchStats as $b)
            <a href="{{ url('/admin/complaints') }}"
               class="border border-gray-100 rounded-xl p-4 text-right hover:shadow-md transition-shadow block">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-emerald-600 font-semibold">{{ $b['resolve_rate'] }}% حل</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $b['name'] }}</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden mb-2">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $b['resolve_rate'] }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-400">
                    <span>معلق: %{{ $b['pending_rate'] }}</span>
                    <span>تم الحل: %{{ $b['resolve_rate'] }}</span>
                </div>
            </a>
            @empty
            <div class="col-span-3 text-center text-gray-400 py-6">لا توجد بيانات للفروع المحددة</div>
            @endforelse
        </div>
    </div>

</div>

{{-- Chart.js script --}}
<script>
(function () {
    const labels = @json($months);
    const data   = @json($monthlyData);

    const ctx = document.getElementById('monthlyChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: '#10b981',
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Cairo', size: 11 } }
                },
                y: {
                    grid: { color: '#f3f4f6' },
                    ticks: { font: { family: 'Cairo', size: 11 }, stepSize: 1 }
                }
            }
        }
    });
})();
</script>
@endsection
