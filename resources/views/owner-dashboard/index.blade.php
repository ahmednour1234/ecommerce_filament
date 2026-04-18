@extends('layouts.owner-dashboard')

@section('title', 'لوحة تحكم مكتب الاستقدام')

@section('content')
<div class="max-w-screen-2xl mx-auto space-y-5">

    {{-- ══════════════════ FILTER BAR ══════════════════ --}}
    <div class="bg-white rounded-2xl px-4 py-4 flex flex-wrap items-center gap-2.5" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
        <div class="flex items-center gap-2 text-gray-500 text-xs font-medium w-full sm:w-auto sm:ml-auto">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            تصفية البيانات
        </div>

        <select id="filterBranch" class="flex-1 sm:flex-none text-sm border border-gray-200 rounded-xl px-3 py-2 text-gray-700 bg-gray-50 focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 cursor-pointer" style="min-width:130px;">
            <option value="">كل الفروع</option>
            @foreach(\App\Models\MainCore\Branch::where('status','active')->get() as $br)
            <option value="{{ $br->id }}">{{ $br->name }}</option>
            @endforeach
        </select>

        <select id="filterPeriod" class="flex-1 sm:flex-none text-sm border border-gray-200 rounded-xl px-3 py-2 text-gray-700 bg-gray-50 focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 cursor-pointer" style="min-width:130px;">
            <option value="6">آخر 6 أشهر</option>
            <option value="3">آخر 3 أشهر</option>
            <option value="12">آخر 12 شهر</option>
            <option value="1">الشهر الحالي</option>
        </select>

        <input type="date" id="filterFrom" class="flex-1 sm:flex-none text-sm border border-gray-200 rounded-xl px-3 py-2 text-gray-700 bg-gray-50 focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400" placeholder="من تاريخ">
        <input type="date" id="filterTo" class="flex-1 sm:flex-none text-sm border border-gray-200 rounded-xl px-3 py-2 text-gray-700 bg-gray-50 focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400" placeholder="إلى تاريخ">

        <button id="applyFilter" class="text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 px-4 py-2 rounded-xl transition-colors flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
            تطبيق
        </button>
        <button id="resetFilter" class="text-sm font-medium text-gray-500 hover:text-gray-700 px-3 py-2 rounded-xl hover:bg-gray-50 transition-colors">
            إعادة تعيين
        </button>
    </div>

    {{-- ══════════════════ ROW: DARK SUMMARY + STATS ══════════════════ --}}
    {{-- ══════════════════ ROW: DARK SUMMARY + STATUS CARDS ══════════════════ --}}
    @php
    $statusLabels = [
        'new'                              => ['label' => 'جديد',                                  'color' => '#3b82f6'],
        'external_office_approval'         => ['label' => 'بانتظار موافقة المكتب الخارجي',        'color' => '#f59e0b'],
        'contract_accepted_external_office'=> ['label' => 'قبول العقد من المكتب الخارجي',        'color' => '#f59e0b'],
        'waiting_approval'                 => ['label' => 'انتظار الموافقة',                      'color' => '#8b5cf6'],
        'contract_accepted_labor_ministry' => ['label' => 'قبول العقد من وزارة العمل',           'color' => '#8b5cf6'],
        'sent_to_saudi_embassy'            => ['label' => 'إرسال التأشيرة إلى السفارة السعودية', 'color' => '#06b6d4'],
        'visa_issued'                      => ['label' => 'إصدار التأشيرة',                       'color' => '#10b981'],
        'visa_cancelled'                   => ['label' => 'إلغاء التفييز',                        'color' => '#ef4444'],
        'travel_permit_after_visa_issued'  => ['label' => 'تصريح سفر بعد تم التفييز',            'color' => '#14b8a6'],
        'waiting_flight_booking'           => ['label' => 'انتظار حجز تذكرة الطيران',            'color' => '#f97316'],
        'arrival_scheduled'                => ['label' => 'معاد الوصول',                          'color' => '#6366f1'],
        'received'                         => ['label' => 'تم الاستلام',                          'color' => '#22c55e'],
        'return_during_warranty'           => ['label' => 'رجع خلال فترة الضمان',                'color' => '#f43f5e'],
        'runaway'                          => ['label' => 'هروب',                                 'color' => '#dc2626'],
    ];
    $baseUrl = url('/admin/recruitment/recruitment-contracts');
    @endphp
    <div class="flex flex-col lg:flex-row gap-4 lg:items-stretch">

        {{-- Dark summary card --}}
        <div class="flex-shrink-0 lg:w-64 w-full rounded-2xl p-5 flex flex-col gap-3 text-white relative overflow-hidden" style="background:linear-gradient(145deg,#0d1117 0%,#1a2332 100%)">
            <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 20% 80%, #10b981 0%, transparent 60%);"></div>

            <a href="{{ $baseUrl }}" class="relative block">
                <p class="text-xs font-medium mb-0.5" style="color:#8b949e;">إجمالي عقود الاستقدام</p>
                <p class="text-4xl font-bold text-white leading-none" id="stat-totalContracts">{{ $totalContracts }}</p>
            </a>

            <div class="relative" style="border-top:1px solid rgba(255,255,255,.07);padding-top:10px;">
                <a href="{{ $baseUrl }}?tableFilters[current_section][value]=accounts"
                   class="flex items-center justify-between py-1.5 hover:opacity-80 transition-opacity">
                    <span class="text-lg font-bold text-emerald-400" id="sc-accounts">{{ $sectionCounts['accounts'] ?? 0 }}</span>
                    <span class="text-xs text-right" style="color:#8b949e;">عقود قسم الاستقدام</span>
                </a>
                <a href="{{ $baseUrl }}?tableFilters[current_section][value]=coordination"
                   class="flex items-center justify-between py-1.5 hover:opacity-80 transition-opacity">
                    <span class="text-lg font-bold text-blue-400" id="sc-coordination">{{ $sectionCounts['coordination'] ?? 0 }}</span>
                    <span class="text-xs text-right" style="color:#8b949e;">عقود قسم التنسيق</span>
                </a>
                <a href="{{ $baseUrl }}?tableFilters[current_section][value]=customer_service"
                   class="flex items-center justify-between py-1.5 hover:opacity-80 transition-opacity">
                    <span class="text-lg font-bold text-purple-400" id="sc-customer_service">{{ $sectionCounts['customer_service'] ?? 0 }}</span>
                    <span class="text-xs text-right" style="color:#8b949e;">عقود خدمة العملاء</span>
                </a>
            </div>

            <div class="relative mt-auto pt-3" style="border-top:1px solid rgba(255,255,255,.07);">
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

        {{-- Status cards grid --}}
        <div class="flex-1 bg-white rounded-2xl p-5" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <a href="{{ $baseUrl }}" class="text-xs text-emerald-600 hover:underline font-medium">عرض كل العقود ←</a>
                <h3 class="text-sm font-bold text-gray-900">حالات عقود الاستقدام</h3>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-7 gap-2" id="statusCardsGrid">
                @foreach($statusLabels as $statusKey => $info)
                @php $cnt = $statusCounts[$statusKey] ?? 0; @endphp
                <a href="{{ $baseUrl }}?tableFilters[status][value]={{ $statusKey }}"
                   class="rounded-xl p-3 text-right hover:scale-105 transition-all cursor-pointer border"
                   style="background:{{ $info['color'] }}12; border-color:{{ $info['color'] }}30;"
                   data-status="{{ $statusKey }}">
                    <p class="text-2xl font-bold leading-none status-count" style="color:{{ $info['color'] }};">{{ $cnt }}</p>
                    <p class="text-xs text-gray-600 mt-1.5 leading-snug font-medium">{{ $info['label'] }}</p>
                </a>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ══════════════════ RECRUITMENT CHARTS ══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Branch comparison bar chart --}}
        <div class="bg-white rounded-2xl p-6" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-full">آخر 6 أشهر</span>
                <h3 class="text-sm font-bold text-gray-900">مقارنة عقود الاستقدام بين الفروع</h3>
            </div>
            <canvas id="branchComparisonChart" height="200"></canvas>
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

    {{-- ══════════════════ RENTAL CONTRACTS SECTION ══════════════════ --}}
    @php
    $rentalStatusLabels = [
        'pending_approval' => ['label' => 'ينتظر الموافقة',   'color' => '#f59e0b'],
        'active'           => ['label' => 'نشط',              'color' => '#10b981'],
        'suspended'        => ['label' => 'معلق',             'color' => '#f97316'],
        'completed'        => ['label' => 'مكتمل',            'color' => '#3b82f6'],
        'cancelled'        => ['label' => 'ملغي',             'color' => '#ef4444'],
        'returned'         => ['label' => 'مرتجع',            'color' => '#8b5cf6'],
        'archived'         => ['label' => 'مؤرشف',            'color' => '#6b7280'],
        'rejected'         => ['label' => 'مرفوض',            'color' => '#dc2626'],
    ];
    $rentalReqLabels = [
        'pending'     => ['label' => 'طلبات قيد الانتظار', 'color' => '#f59e0b'],
        'under_review'=> ['label' => 'قيد المراجعة',       'color' => '#6366f1'],
        'approved'    => ['label' => 'موافق عليها',        'color' => '#10b981'],
        'rejected'    => ['label' => 'طلبات مرفوضة',      'color' => '#ef4444'],
        'converted'   => ['label' => 'تم التحويل',         'color' => '#06b6d4'],
    ];
    $rentalBaseUrl = url('/admin/rental/rental-contracts');
    $rentalReqBaseUrl = url('/admin/rental/rental-requests');
    @endphp

    {{-- ROW: Rental dark card + status cards --}}
    <div class="flex flex-col lg:flex-row gap-4 lg:items-stretch">

        {{-- Rental dark summary card --}}
        <div class="flex-shrink-0 lg:w-64 w-full rounded-2xl p-5 flex flex-col gap-3 text-white relative overflow-hidden" style="background:linear-gradient(145deg,#0f172a 0%,#1e293b 100%)">
            <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 80% 20%, #3b82f6 0%, transparent 60%);"></div>

            <a href="{{ $rentalBaseUrl }}" class="relative block">
                <p class="text-xs font-medium mb-0.5" style="color:#94a3b8;">إجمالي عقود الإيجار</p>
                <p class="text-4xl font-bold text-white leading-none" id="stat-rentalTotal">{{ $rentalTotalContracts }}</p>
            </a>

            <div class="relative" style="border-top:1px solid rgba(255,255,255,.07);padding-top:10px;">
                <a href="{{ $rentalBaseUrl }}?tableFilters[status][value]=active"
                   class="flex items-center justify-between py-1.5 hover:opacity-80 transition-opacity">
                    <span class="text-lg font-bold text-emerald-400" id="rsc-active">{{ $rentalStatusCounts['active'] ?? 0 }}</span>
                    <span class="text-xs text-right" style="color:#94a3b8;">عقود نشطة</span>
                </a>
                <a href="{{ $rentalBaseUrl }}?tableFilters[status][value]=pending_approval"
                   class="flex items-center justify-between py-1.5 hover:opacity-80 transition-opacity">
                    <span class="text-lg font-bold text-amber-400" id="rsc-pending_approval">{{ $rentalStatusCounts['pending_approval'] ?? 0 }}</span>
                    <span class="text-xs text-right" style="color:#94a3b8;">تنتظر الموافقة</span>
                </a>
                <a href="{{ $rentalBaseUrl }}?tableFilters[status][value]=completed"
                   class="flex items-center justify-between py-1.5 hover:opacity-80 transition-opacity">
                    <span class="text-lg font-bold text-blue-400" id="rsc-completed">{{ $rentalStatusCounts['completed'] ?? 0 }}</span>
                    <span class="text-xs text-right" style="color:#94a3b8;">مكتملة</span>
                </a>
            </div>

            {{-- Requests footer --}}
            <div class="relative mt-auto pt-3" style="border-top:1px solid rgba(255,255,255,.07);">
                <a href="{{ $rentalReqBaseUrl }}" class="flex items-center gap-2 justify-end hover:opacity-80">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-white" id="stat-rentalReqTotal">{{ $rentalTotalRequests }}</p>
                        <p class="text-xs" style="color:#94a3b8;">إجمالي الطلبات</p>
                    </div>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#3b82f620;">
                        <span class="text-blue-400 text-base">📋</span>
                    </div>
                </a>
            </div>
        </div>

        {{-- Rental status cards grid --}}
        <div class="flex-1 bg-white rounded-2xl p-5" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <a href="{{ $rentalBaseUrl }}" class="text-xs text-blue-600 hover:underline font-medium">عرض كل العقود ←</a>
                <h3 class="text-sm font-bold text-gray-900">حالات عقود الإيجار</h3>
            </div>

            {{-- Contract statuses --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2 mb-3" id="rentalStatusCardsGrid">
                @foreach($rentalStatusLabels as $sk => $info)
                @php $cnt = $rentalStatusCounts[$sk] ?? 0; @endphp
                <a href="{{ $rentalBaseUrl }}?tableFilters[status][value]={{ $sk }}"
                   class="rounded-xl p-3 text-right hover:scale-105 transition-all cursor-pointer border"
                   style="background:{{ $info['color'] }}12; border-color:{{ $info['color'] }}30;"
                   data-rental-status="{{ $sk }}">
                    <p class="text-2xl font-bold leading-none rental-status-count" style="color:{{ $info['color'] }};">{{ $cnt }}</p>
                    <p class="text-xs text-gray-600 mt-1.5 leading-snug font-medium">{{ $info['label'] }}</p>
                </a>
                @endforeach
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-100 pt-3">
                <p class="text-xs font-semibold text-gray-400 text-right mb-2">طلبات الإيجار</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2" id="rentalReqCardsGrid">
                    @foreach($rentalReqLabels as $sk => $info)
                    @php $cnt = $rentalRequestCounts[$sk] ?? 0; @endphp
                    <a href="{{ $rentalReqBaseUrl }}?tableFilters[status][value]={{ $sk }}"
                       class="rounded-xl p-3 text-right hover:scale-105 transition-all cursor-pointer border"
                       style="background:{{ $info['color'] }}12; border-color:{{ $info['color'] }}30;"
                       data-rental-req-status="{{ $sk }}">
                        <p class="text-xl font-bold leading-none rental-req-count" style="color:{{ $info['color'] }};">{{ $cnt }}</p>
                        <p class="text-xs text-gray-600 mt-1.5 leading-snug font-medium">{{ $info['label'] }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- ROW: Rental charts (branch comparison bar + monthly trend line) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Rental branch comparison bar chart --}}
        <div class="bg-white rounded-2xl p-5" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-gray-400">آخر 6 أشهر</span>
                <h3 class="text-sm font-bold text-gray-900">مقارنة الفروع — عقود الإيجار</h3>
            </div>
            <canvas id="rentalBranchChart" height="220"></canvas>
        </div>

        {{-- Rental monthly trend line chart --}}
        <div class="bg-white rounded-2xl p-5" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-gray-400" id="rentalChartPeriodLabel">آخر 6 أشهر</span>
                <h3 class="text-sm font-bold text-gray-900">إجمالي عقود الإيجار الشهري</h3>
            </div>
            <canvas id="rentalLineChart" height="220"></canvas>
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

    {{-- ══════════════════ FINANCE BRANCH CHARTS ══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Income bar chart --}}
        <div class="bg-white rounded-2xl p-6" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-full">آخر 6 أشهر</span>
                <h3 class="text-sm font-bold text-gray-900">مقارنة الإيرادات بين الفروع</h3>
            </div>
            <canvas id="financeIncomeChart" height="200"></canvas>
            <div class="flex flex-wrap gap-4 justify-center mt-4" id="financeIncomeLegend"></div>
        </div>
        {{-- Expense line chart --}}
        <div class="bg-white rounded-2xl p-6" style="box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f1f5f9;">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-full">آخر 6 أشهر</span>
                <h3 class="text-sm font-bold text-gray-900">مقارنة المصاريف بين الفروع</h3>
            </div>
            <canvas id="financeExpenseChart" height="200"></canvas>
            <div class="flex flex-wrap gap-4 justify-center mt-4" id="financeExpenseLegend"></div>
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
                            <th class="pb-2 font-medium">الفرع</th>
                            <th class="pb-2 font-medium">الإيرادات</th>
                            <th class="pb-2 font-medium">المصاريف</th>
                            <th class="pb-2 font-medium">الشكاوى</th>
                            <th class="pb-2 font-medium">الأداء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($branchStats as $b)
                        <tr class="text-sm cursor-pointer hover:bg-gray-50 transition-colors"
                            onclick="window.location='{{ url('/admin/main-core/branches') }}'">
                            <td class="py-3 font-medium text-gray-900">{{ $b['name'] }}</td>
                            <td class="py-3 text-gray-900 font-semibold">{{ number_format($b['income']) }} ر.س</td>
                            <td class="py-3 text-red-600 font-semibold">{{ number_format($b['expense']) }} ر.س</td>
                            <td class="py-3 text-gray-600">{{ $b['complaints'] }}</td>
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

            // Update section counts in dark card
            const scMap = { 'sc-accounts': 'accounts', 'sc-coordination': 'coordination', 'sc-customer_service': 'customer_service' };
            Object.entries(scMap).forEach(([elId, key]) => {
                const el = document.getElementById(elId);
                if (el) el.textContent = (d.sectionCounts && d.sectionCounts[key] != null) ? d.sectionCounts[key] : 0;
            });

            // Update status cards
            if (d.statusCounts) {
                document.querySelectorAll('#statusCardsGrid [data-status]').forEach(card => {
                    const key = card.dataset.status;
                    const countEl = card.querySelector('.status-count');
                    if (countEl) countEl.textContent = d.statusCounts[key] != null ? d.statusCounts[key] : 0;
                });
            }
        })
        .catch(() => {})
        .finally(() => setLoading(false));
    }

    // ── Rental branch comparison bar chart ────────────────────────
    const rentalBranchData   = @json($rentalBranchData);
    const rentalBranchColors = ['#10b981', '#3b82f6', '#f59e0b'];

    const rbCtx = document.getElementById('rentalBranchChart');
    if (rbCtx) {
        const rbDatasets = Object.entries(rentalBranchData).map(([name, data], i) => ({
            label: name,
            data,
            backgroundColor: rentalBranchColors[i % rentalBranchColors.length],
            borderRadius: 6,
            borderSkipped: false,
        }));

        new Chart(rbCtx, {
            type: 'bar',
            data: { labels: bcMonths, datasets: rbDatasets },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        rtl: true,
                        labels: { font: { family: 'Cairo', size: 11 }, usePointStyle: true, pointStyleWidth: 10 }
                    },
                    tooltip: { rtl: true, textDirection: 'rtl' }
                },
                scales: {
                    x: { reverse: true, grid: { display: false }, ticks: { font: { family: 'Cairo', size: 11 } } },
                    y: { grid: { color: '#f3f4f6' }, ticks: { font: { family: 'Cairo', size: 11 }, stepSize: 1 } }
                }
            }
        });
    }

    // ── Rental monthly trend line chart ───────────────────────────
    const rentalLineCtx = document.getElementById('rentalLineChart');
    const rentalLineChart = rentalLineCtx ? new Chart(rentalLineCtx, {
        type: 'line',
        data: {
            labels: bcMonths,
            datasets: [{
                label: 'عقود الإيجار',
                data: @json($rentalMonthlyData),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#3b82f6',
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { rtl: true, textDirection: 'rtl' }
            },
            scales: {
                x: { reverse: true, grid: { display: false }, ticks: { font: { family: 'Cairo', size: 11 } } },
                y: { grid: { color: '#f3f4f6' }, ticks: { font: { family: 'Cairo', size: 11 }, stepSize: 1 }, beginAtZero: true }
            }
        }
    }) : null;

    // ── Finance charts: income (bar) + expense (line) — separate ──────
    const financeMonths = @json($financeChartMonths);
    const financeData   = @json($financeChartData);
    const branchPalette = ['#10b981', '#3b82f6', '#f59e0b'];

    function makeFinanceLegend(elId, names) {
        const el = document.getElementById(elId);
        if (!el) return;
        names.forEach((name, i) => {
            el.innerHTML += `<span class="flex items-center gap-1.5 text-xs text-gray-600 font-medium">
                <span class="w-3 h-3 rounded-sm inline-block" style="background:${branchPalette[i]}"></span>${name}
            </span>`;
        });
    }

    const branchNames = Object.keys(financeData);

    // Income — bar chart
    const incomeCtx = document.getElementById('financeIncomeChart');
    if (incomeCtx && branchNames.length > 0) {
        new Chart(incomeCtx, {
            type: 'bar',
            data: {
                labels: financeMonths,
                datasets: branchNames.map((name, i) => ({
                    label: name,
                    data: financeData[name].income,
                    backgroundColor: branchPalette[i % branchPalette.length] + 'cc',
                    borderRadius: 6,
                    borderSkipped: false,
                }))
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { rtl: true, textDirection: 'rtl',
                        callbacks: { label: ctx => `${ctx.dataset.label}: ${Number(ctx.raw).toLocaleString('ar-SA')} ر.س` }
                    }
                },
                scales: {
                    x: { reverse: true, grid: { display: false }, ticks: { font: { family: 'Cairo', size: 11 } } },
                    y: { grid: { color: '#f3f4f6' }, beginAtZero: true,
                        ticks: { font: { family: 'Cairo', size: 11 }, callback: v => Number(v).toLocaleString('ar-SA') + ' ر.س' }
                    }
                }
            }
        });
        makeFinanceLegend('financeIncomeLegend', branchNames);
    }

    // Expense — line chart
    const expenseCtx = document.getElementById('financeExpenseChart');
    if (expenseCtx && branchNames.length > 0) {
        new Chart(expenseCtx, {
            type: 'line',
            data: {
                labels: financeMonths,
                datasets: branchNames.map((name, i) => ({
                    label: name,
                    data: financeData[name].expense,
                    borderColor: branchPalette[i % branchPalette.length],
                    backgroundColor: branchPalette[i % branchPalette.length] + '18',
                    borderWidth: 2.5,
                    pointBackgroundColor: branchPalette[i % branchPalette.length],
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4,
                }))
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { rtl: true, textDirection: 'rtl',
                        callbacks: { label: ctx => `${ctx.dataset.label}: ${Number(ctx.raw).toLocaleString('ar-SA')} ر.س` }
                    }
                },
                scales: {
                    x: { reverse: true, grid: { display: false }, ticks: { font: { family: 'Cairo', size: 11 } } },
                    y: { grid: { color: '#f3f4f6' }, beginAtZero: true,
                        ticks: { font: { family: 'Cairo', size: 11 }, callback: v => Number(v).toLocaleString('ar-SA') + ' ر.س' }
                    }
                }
            }
        });
        makeFinanceLegend('financeExpenseLegend', branchNames);
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
