@extends('layouts.owner-dashboard')

@section('title', 'لوحة تحكم مكتب الاستقدام')

@section('content')
<div class="max-w-screen-2xl mx-auto space-y-5">

    {{-- ══════════════════ FILTER BAR ══════════════════ --}}
    <div class="bg-white rounded-2xl px-5 py-4 flex flex-wrap items-center gap-3" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
        <div class="flex items-center gap-2 text-gray-500 text-xs font-medium ml-auto">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            تصفية البيانات
        </div>

        <select id="filterBranch" class="text-sm border border-gray-200 rounded-xl px-3 py-2 text-gray-700 bg-gray-50 focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 cursor-pointer" style="min-width:140px;">
            <option value="">كل الفروع</option>
            @foreach(\App\Models\MainCore\Branch::where('status','active')->get() as $br)
            <option value="{{ $br->id }}">{{ $br->name }}</option>
            @endforeach
        </select>

        <select id="filterPeriod" class="text-sm border border-gray-200 rounded-xl px-3 py-2 text-gray-700 bg-gray-50 focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 cursor-pointer" style="min-width:140px;">
            <option value="6">آخر 6 أشهر</option>
            <option value="3">آخر 3 أشهر</option>
            <option value="12">آخر 12 شهر</option>
            <option value="1">الشهر الحالي</option>
        </select>

        <input type="date" id="filterFrom" class="text-sm border border-gray-200 rounded-xl px-3 py-2 text-gray-700 bg-gray-50 focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400" placeholder="من تاريخ">
        <input type="date" id="filterTo" class="text-sm border border-gray-200 rounded-xl px-3 py-2 text-gray-700 bg-gray-50 focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400" placeholder="إلى تاريخ">

        <button id="applyFilter" class="text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 px-4 py-2 rounded-xl transition-colors flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
            تطبيق
        </button>
        <button id="resetFilter" class="text-sm font-medium text-gray-500 hover:text-gray-700 px-3 py-2 rounded-xl hover:bg-gray-50 transition-colors">
            إعادة تعيين
        </button>
    </div>

    {{-- ══════════════════ ROW: DARK SUMMARY + STATS ══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        {{-- Dark summary card --}}
        <div class="lg:col-span-1 rounded-2xl p-6 flex flex-col justify-between text-white relative overflow-hidden" style="background:linear-gradient(145deg,#0d1117 0%,#1a2332 100%);">
            <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 20% 80%, #10b981 0%, transparent 60%);"></div>
            <div class="relative">
                <p class="text-xs font-medium mb-1" style="color:#8b949e;">إجراءات اليوم</p>
                <p class="text-5xl font-bold text-white" id="stat-todayPending">{{ $todayPending }}</p>
                <p class="text-xs mt-1" style="color:#8b949e;">تحتاج متابعة من الإدارة</p>
            </div>
            <div class="mt-4 pt-4 relative" style="border-top:1px solid rgba(255,255,255,.07);">
                <div class="flex items-center gap-2 justify-end">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs" style="color:#8b949e;">صاحب الشركة</p>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold">{{ mb_substr(auth()->user()->name,0,1) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats row 1 (4 cards) --}}
        <div class="lg:col-span-4 grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
            $baseUrl = url('/admin/recruitment/recruitment-contracts');
            $sections = [
                ['label' => 'عقود قسم الاستقدام',  'key' => 'accounts',         'color' => 'blue',   'icon' => '🧾'],
                ['label' => 'عقود قسم التنسيق',     'key' => 'coordination',     'color' => 'purple', 'icon' => '📌'],
                ['label' => 'عقود خدمة العملاء',    'key' => 'customer_service', 'color' => 'emerald','icon' => '🤝'],
                ['label' => 'عقود تم التسليم',      'key' => '_received',        'color' => 'gray',   'icon' => '✅'],
            ];
            $colorMap = [
                'blue'    => ['bg'=>'bg-blue-50',    'text'=>'text-blue-700',    'dot'=>'bg-blue-500'],
                'purple'  => ['bg'=>'bg-purple-50',  'text'=>'text-purple-700',  'dot'=>'bg-purple-500'],
                'emerald' => ['bg'=>'bg-emerald-50', 'text'=>'text-emerald-700', 'dot'=>'bg-emerald-500'],
                'gray'    => ['bg'=>'bg-gray-50',    'text'=>'text-gray-700',    'dot'=>'bg-gray-400'],
            ];
            @endphp
            @foreach($sections as $sec)
            @php
            $cnt = $sec['key'] === '_received'
                ? ($statusCounts['received'] ?? 0)
                : ($sectionCounts[$sec['key']] ?? 0);
            $href = $sec['key'] === '_received'
                ? $baseUrl . '?tableFilters[status][value]=received'
                : $baseUrl . '?tableFilters[current_section][value]=' . $sec['key'];
            $c = $colorMap[$sec['color']];
            @endphp
            <a href="{{ $href }}" class="bg-white rounded-2xl p-5 stat-card flex flex-col gap-2 hover:shadow-lg transition-all" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
                <div class="flex items-center justify-between">
                    <div class="w-9 h-9 rounded-xl {{ $c['bg'] }} flex items-center justify-center text-lg">{{ $sec['icon'] }}</div>
                    <span class="w-2 h-2 rounded-full {{ $c['dot'] }}"></span>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400 font-medium leading-snug">{{ $sec['label'] }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-0.5 leading-none">{{ number_format($cnt) }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════ CONTRACT STATUS CARDS ══════════════════ --}}
    @php
    $statusLabels = [
        'new'                              => ['label' => 'جديد',                                   'color' => '#3b82f6'],
        'external_office_approval'         => ['label' => 'بانتظار موافقة المكتب الخارجي',         'color' => '#f59e0b'],
        'contract_accepted_external_office'=> ['label' => 'قبول العقد من المكتب الخارجي',         'color' => '#f59e0b'],
        'waiting_approval'                 => ['label' => 'انتظار الموافقة',                       'color' => '#8b5cf6'],
        'contract_accepted_labor_ministry' => ['label' => 'قبول العقد من وزارة العمل',            'color' => '#8b5cf6'],
        'sent_to_saudi_embassy'            => ['label' => 'إرسال التأشيرة إلى السفارة السعودية',  'color' => '#06b6d4'],
        'visa_issued'                      => ['label' => 'إصدار التأشيرة',                        'color' => '#10b981'],
        'visa_cancelled'                   => ['label' => 'إلغاء التفييز',                         'color' => '#ef4444'],
        'travel_permit_after_visa_issued'  => ['label' => 'تصريح سفر بعد تم التفييز',             'color' => '#14b8a6'],
        'waiting_flight_booking'           => ['label' => 'انتظار حجز تذكرة الطيران',             'color' => '#f97316'],
        'arrival_scheduled'                => ['label' => 'معاد الوصول',                           'color' => '#6366f1'],
        'received'                         => ['label' => 'تم الاستلام',                           'color' => '#22c55e'],
        'return_during_warranty'           => ['label' => 'رجع خلال فترة الضمان',                 'color' => '#f43f5e'],
        'runaway'                          => ['label' => 'هروب',                                  'color' => '#dc2626'],
    ];
    $baseUrl = url('/admin/recruitment/recruitment-contracts');
    @endphp
    <div class="bg-white rounded-2xl p-6" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
        <div class="flex items-center justify-between mb-5">
            <a href="{{ $baseUrl }}" class="text-xs text-emerald-600 hover:underline font-medium">عرض كل العقود ←</a>
            <h3 class="text-sm font-bold text-gray-900">حالات عقود الاستقدام</h3>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-7 gap-3">
            @foreach($statusLabels as $statusKey => $info)
            @php $cnt = $statusCounts[$statusKey] ?? 0; @endphp
            <a href="{{ $baseUrl }}?tableFilters[status][value]={{ $statusKey }}"
               class="rounded-xl p-3 text-right hover:scale-105 transition-all cursor-pointer border"
               style="background:{{ $info['color'] }}12; border-color:{{ $info['color'] }}30;">
                <p class="text-2xl font-bold leading-none" style="color:{{ $info['color'] }};">{{ $cnt }}</p>
                <p class="text-xs text-gray-600 mt-1.5 leading-snug font-medium">{{ $info['label'] }}</p>
            </a>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════ STATS ROW 2 (HR/Finance quick stats) ══════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
        $stats2 = [
            ['icon' => '🏢', 'label' => 'العقود الإيجارية',    'id' => 'stat-activeRentals',      'value' => $activeRentals,          'sub' => 'عقود فعالة حالياً',  'url' => url('/admin/rental/rental-contracts')],
            ['icon' => '💰', 'label' => 'معاملات مالية تحتاج موافقة', 'id' => 'stat-pendingJournals', 'value' => $pendingJournals,  'sub' => 'في قسم المالية',   'url' => url('/admin/finance/branch-transactions') . '?tableFilters[status][value]=pending'],
            ['icon' => '✍️', 'label' => 'عدد الشكاوى',        'id' => 'stat-openComplaints',     'value' => $openComplaints,         'sub' => 'مفتوحة ومعلقة',     'url' => url('/admin/complaints')],
            ['icon' => '⭐', 'label' => 'نسبة رضا العملاء',   'id' => 'stat-satisfactionRate',   'value' => $satisfactionRate . '%', 'sub' => 'مؤشر عام ممتاز',    'url' => url('/admin/complaints')],
        ];
        @endphp
        @foreach($stats2 as $stat)
        <a href="{{ $stat['url'] }}" class="bg-white rounded-2xl p-5 stat-card flex flex-col gap-3 hover:shadow-lg transition-all" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-xl border border-gray-100">{{ $stat['icon'] }}</div>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 font-medium">{{ $stat['label'] }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-0.5 leading-none" id="{{ $stat['id'] }}">{{ $stat['value'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stat['sub'] }}</p>
            </div>
        </a>
        @endforeach
    </div>

    {{-- ══════════════════ CHARTS ROW ══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Branch comparison bar chart --}}
        <div class="bg-white rounded-2xl p-6" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-full">آخر 6 أشهر</span>
                <h3 class="text-sm font-bold text-gray-900">مقارنة العقود بين الفروع</h3>
            </div>
            <canvas id="branchComparisonChart" height="200"></canvas>
            {{-- legend --}}
            <div class="flex flex-wrap gap-4 justify-center mt-4" id="branchLegend"></div>
        </div>

        {{-- Monthly bar chart --}}
        <div class="bg-white rounded-2xl p-6" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <span id="chartPeriodLabel" class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-full">آخر 6 أشهر</span>
                <h3 class="text-sm font-bold text-gray-900">حركة عقود الاستقدام الشهرية</h3>
            </div>
            <canvas id="monthlyChart" height="180"></canvas>
        </div>
    </div>

    {{-- ══════════════════ ACCOUNTING + HR PENDING ══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Accounting --}}
        <div class="bg-white rounded-2xl p-6" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-amber-600 bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-full">بانتظار المدير</span>
                <h3 class="text-sm font-bold text-gray-900">المحاسبة والاعتمادات</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ url('/admin/accounting/journal-entries') }}"
                   class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex items-center justify-between hover:bg-amber-100 transition-colors block">
                    <span class="text-2xl font-bold text-amber-600">{{ $pendingJournals }}</span>
                    <div class="text-right">
                        <p class="font-bold text-gray-900 text-sm">قيود يومية تنتظر الاعتماد</p>
                        <p class="text-xs text-gray-500 mt-0.5">تحتاج اعتماد سريع</p>
                    </div>
                </a>
                <a href="{{ url('/admin/accounting/vouchers') }}"
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
        <div class="bg-white rounded-2xl p-6" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-full">المهام الحالية</span>
                <h3 class="text-sm font-bold text-gray-900">الموارد البشرية HR</h3>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ url('/admin/h-r/excuse-requests') }}"
                   class="border border-gray-100 rounded-xl p-4 text-right hover:bg-gray-50 transition-colors block">
                    <p class="text-xs text-gray-500">طلبات استئذان اليوم</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $pendingExcuse }}</p>
                </a>
                <a href="{{ url('/admin/h-r/leave-requests') }}"
                   class="border border-gray-100 rounded-xl p-4 text-right hover:bg-gray-50 transition-colors block">
                    <p class="text-xs text-gray-500">طلبات إجازة بانتظار المدير</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $pendingLeave }}</p>
                </a>
                <a href="{{ url('/admin/h-r/employees') }}"
                   class="border border-gray-100 rounded-xl p-4 text-right hover:bg-gray-50 transition-colors block">
                    <p class="text-xs text-gray-500">مقابلات مجدولة هذا الأسبوع</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $scheduledInterviews }}</p>
                </a>
                <a href="{{ url('/admin/rental/rental-contracts') }}"
                   class="border border-gray-100 rounded-xl p-4 text-right hover:bg-gray-50 transition-colors block">
                    <p class="text-xs text-gray-500">عقود موظفين تحتاج تجديد</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $activeRentals }}</p>
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════ ALERTS + BRANCH TABLE ══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Branch revenue table --}}
        <div class="bg-white rounded-2xl p-6" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">إيرادات ومصاريف الفروع</h3>
                <a href="{{ url('/admin/finance/branch-transactions') }}" class="text-xs text-emerald-600 hover:underline font-medium">عرض التقارير المالية ←</a>
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
                            onclick="window.location='{{ url('/admin/main-core/branches') }}'">
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

        {{-- Alerts --}}
        <div class="bg-white rounded-2xl p-6" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <h3 class="text-sm font-bold text-gray-900 text-right mb-4">تنبيهات سريعة</h3>
            <div class="space-y-3">
                @if($pendingLeave > 0)
                <div class="bg-amber-50 border-r-4 border-amber-400 rounded-xl px-4 py-3 text-right">
                    <a href="{{ url('/admin/h-r/leave-requests') }}" class="text-sm font-bold text-amber-700">
                        {{ $pendingLeave }} طلب إجازة بانتظار الاعتماد
                    </a>
                    <p class="text-xs text-gray-500 mt-0.5">يفضل مراجعتها اليوم</p>
                </div>
                @endif
                @if($pendingJournals > 0)
                <div class="bg-rose-50 border-r-4 border-rose-400 rounded-xl px-4 py-3 text-right">
                    <a href="{{ url('/admin/accounting/journal-entries') }}" class="text-sm font-bold text-rose-700">
                        {{ $pendingJournals }} قيد محاسبي معلق
                    </a>
                    <p class="text-xs text-gray-500 mt-0.5">هناك موافقات متأخرة</p>
                </div>
                @endif
                @if($pendingVouchers > 0)
                <div class="bg-rose-50 border-r-4 border-rose-400 rounded-xl px-4 py-3 text-right">
                    <a href="{{ url('/admin/finance/branch-transactions') }}?tableFilters[status][value]=pending" class="text-sm font-bold text-rose-700">
                        {{ $pendingVouchers }} معاملة مالية معلقة
                    </a>
                    <p class="text-xs text-gray-500 mt-0.5">معاملات الفروع بانتظار الاعتماد</p>
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
                @if($pendingLeave === 0 && $pendingJournals === 0 && $pendingVouchers === 0 && $openComplaints === 0)
                <div class="bg-emerald-50 border-r-4 border-emerald-400 rounded-xl px-4 py-3 text-right">
                    <p class="text-sm font-bold text-emerald-700">لا توجد تنبيهات عاجلة</p>
                    <p class="text-xs text-gray-500 mt-0.5">كل شيء على ما يرام اليوم</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════ KPI + LATEST CONTRACTS ══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Latest contracts --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-6" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <a href="{{ url('/admin/recruitment/recruitment-contracts') }}" class="text-xs text-emerald-600 hover:underline font-medium">عرض الكل ←</a>
                <h3 class="text-sm font-bold text-gray-900">آخر عقود الاستقدام</h3>
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
                            onclick="window.location='{{ url('/admin/recruitment/recruitment-contracts/' . $contract->id . '/edit') }}'">
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

        {{-- KPI Card --}}
        <div class="text-white rounded-2xl p-6 flex flex-col relative overflow-hidden" style="background:linear-gradient(145deg,#0d1117 0%,#1a2332 100%);">
            <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 80% 20%, #10b981 0%, transparent 55%);"></div>
            <h3 class="text-sm font-bold mb-3 text-right relative">مؤشر الإدارة اليومي</h3>
            <div class="relative text-center my-2">
                <p class="text-6xl font-bold text-white" id="stat-kpiRate">{{ $kpiRate }}%</p>
                <div class="mt-3 h-1.5 rounded-full overflow-hidden mx-2" style="background:rgba(255,255,255,.1);">
                    <div class="h-full bg-emerald-400 rounded-full" id="kpi-bar" style="width:{{ $kpiRate }}%;"></div>
                </div>
            </div>
            <p class="text-xs text-center mb-4 relative" style="color:#8b949e;">نسبة إنجاز المهام والاعتمادات اليومية</p>
            <div class="space-y-2 border-t border-gray-700 pt-4 text-sm relative">
                <a href="{{ url('/admin/accounting/journal-entries') }}"
                   class="flex justify-between hover:text-emerald-400 transition-colors">
                    <span class="text-white font-semibold" id="stat-approvedToday">{{ $approvedToday }}</span>
                    <span class="text-gray-400">الموافقات المنجزة</span>
                </a>
                <a href="{{ url('/admin/complaints') }}"
                   class="flex justify-between hover:text-emerald-400 transition-colors">
                    <span class="text-white font-semibold">{{ $resolvedComplaints }}</span>
                    <span class="text-gray-400">الشكاوى المغلقة</span>
                </a>
                <a href="{{ url('/admin/rental/rental-contracts') }}"
                   class="flex justify-between hover:text-emerald-400 transition-colors">
                    <span class="text-white font-semibold">{{ $activeContracts }}</span>
                    <span class="text-gray-400">العقود النشطة</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════ BRANCH COMPLAINTS RESOLUTION ══════════════════ --}}
    @php
    $targetBranches = ['الرياض', 'عرعر', 'حفر الباطن'];
    $filteredBranchStats = collect($branchStats)->filter(
        fn($b) => in_array($b['name'], $targetBranches)
    )->reverse()->values();
    @endphp
    <div class="bg-white rounded-2xl p-6" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
        <div class="flex items-center justify-between mb-5">
            <span class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-full">كلما ارتفعت نسبة الحل كان الأداء أفضل</span>
            <h3 class="text-sm font-bold text-gray-900">أداء الفروع في معالجة الشكاوى</h3>
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
    // ── Branch comparison chart ────────────────────────────────
    const bcMonths = @json($branchComparisonMonths);
    const bcData   = @json($branchComparisonData);
    const branchColors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'];

    const bcCtx = document.getElementById('branchComparisonChart');
    if (bcCtx && Object.keys(bcData).length > 0) {
        const datasets = Object.entries(bcData).map(([name, data], i) => ({
            label: name,
            data,
            backgroundColor: branchColors[i % branchColors.length],
            borderRadius: 6,
            borderSkipped: false,
        }));

        new Chart(bcCtx, {
            type: 'bar',
            data: { labels: bcMonths, datasets },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { rtl: true, textDirection: 'rtl' }
                },
                scales: {
                    x: {
                        reverse: true,
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

        // Build legend
        const legend = document.getElementById('branchLegend');
        if (legend) {
            Object.keys(bcData).forEach((name, i) => {
                legend.innerHTML += `<span class="flex items-center gap-1.5 text-xs text-gray-600 font-medium">
                    <span class="w-3 h-3 rounded-full inline-block" style="background:${branchColors[i % branchColors.length]}"></span>${name}
                </span>`;
            });
        }
    } else if (bcCtx) {
        bcCtx.closest('.bg-white').innerHTML += '<p class="text-center text-gray-400 text-sm py-8">لا توجد بيانات للفروع المحددة</p>';
    }

    // ── Monthly total chart ────────────────────────────────────
    let chartLabels = @json($months);
    let chartData   = @json($monthlyData);

    const ctx = document.getElementById('monthlyChart');
    if (!ctx) return;

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartData,
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
                    reverse: true,
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

    // ── Filter logic ───────────────────────────────────
    function getFilters() {
        return {
            branch_id : document.getElementById('filterBranch').value,
            period    : document.getElementById('filterPeriod').value,
            from      : document.getElementById('filterFrom').value,
            to        : document.getElementById('filterTo').value,
            _token    : '{{ csrf_token() }}'
        };
    }

    function setLoading(on) {
        document.getElementById('applyFilter').disabled = on;
        document.getElementById('applyFilter').textContent = on ? '...' : 'تطبيق';
    }

    function applyFilters() {
        const params = getFilters();
        setLoading(true);

        fetch('{{ route("owner.dashboard.filter") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': params._token },
            body: JSON.stringify(params)
        })
        .then(r => r.json())
        .then(d => {
            // Update chart
            chart.data.labels   = d.months;
            chart.data.datasets[0].data = d.monthlyData;
            chart.update();

            // Update period label
            const pMap = {'1':'الشهر الحالي','3':'آخر 3 أشهر','6':'آخر 6 أشهر','12':'آخر 12 شهر'};
            document.getElementById('chartPeriodLabel').textContent = pMap[params.period] || 'آخر 6 أشهر';

            // Update stat cards
            const ids = {
                'stat-totalContracts'     : d.totalContracts,
                'stat-inProgress'         : d.inProgressContracts,
                'stat-pendingLeave'       : d.pendingLeave,
                'stat-pendingExcuse'      : d.pendingExcuse,
                'stat-activeRentals'      : d.activeRentals,
                'stat-pendingJournals'    : d.pendingJournals,
                'stat-openComplaints'     : d.openComplaints,
                'stat-satisfactionRate'   : d.satisfactionRate + '%',
                'stat-todayPending'       : d.todayPending,
                'stat-pendingVouchers'    : d.pendingVouchers,
                'stat-kpiRate'            : d.kpiRate + '%',
                'stat-approvedToday'      : d.approvedToday,
                'stat-resolvedComplaints' : d.resolvedComplaints,
                'stat-activeContracts'    : d.activeContracts,
            };
            Object.entries(ids).forEach(([id, val]) => {
                const el = document.getElementById(id);
                if (el) el.textContent = val;
            });

            // KPI bar
            const kpiBar = document.getElementById('kpi-bar');
            if (kpiBar) kpiBar.style.width = d.kpiRate + '%';
        })
        .catch(() => {})
        .finally(() => setLoading(false));
    }

    document.getElementById('applyFilter').addEventListener('click', applyFilters);

    document.getElementById('resetFilter').addEventListener('click', function () {
        document.getElementById('filterBranch').value = '';
        document.getElementById('filterPeriod').value = '6';
        document.getElementById('filterFrom').value   = '';
        document.getElementById('filterTo').value     = '';
        applyFilters();
    });
})();
</script>
@endsection
