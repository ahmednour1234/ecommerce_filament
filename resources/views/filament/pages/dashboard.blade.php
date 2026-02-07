<x-filament-panels::page class="rtl-dashboard">

    {{-- Optional: عنوان + سطر صغير + مكان واضح للأكشنز --}}
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">لوحة التحكم</h1>
            <p class="text-sm text-gray-500">ملخص سريع للأداء + فلاتر لتحديد الفترة</p>
        </div>

        {{-- Render Filament header actions using the proper component --}}
        <div class="flex items-center gap-2">
            <x-filament::actions :actions="$this->getCachedHeaderActions()" />
        </div>
    </div>

    <div class="space-y-6">

        {{-- Header Widgets --}}
        <x-filament-widgets::widgets
            :widgets="$this->getHeaderWidgets()"
            :columns="$this->getHeaderWidgetsColumns()"
        />

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-filament-widgets::widgets
                :widgets="[$this->getFooterWidgets()[0]]"
                :columns="1"
            />
            <x-filament-widgets::widgets
                :widgets="[$this->getFooterWidgets()[1]]"
                :columns="1"
            />
        </div>

        {{-- Table Full Width --}}
        <x-filament-widgets::widgets
            :widgets="[$this->getFooterWidgets()[2]]"
            :columns="1"
        />
    </div>

    {{-- Auto-open filters modal if filters not applied --}}
    <div x-data="{
        init() {
            // Wait for Livewire to be fully loaded
            if (typeof window.Livewire === 'undefined') {
                setTimeout(() => this.init(), 100);
                return;
            }

            // Check if filters are applied (date_from and date_to in query string OR ?filters=1 flag)
            const urlParams = new URLSearchParams(window.location.search);
            const hasDateFrom = urlParams.has('date_from');
            const hasDateTo = urlParams.has('date_to');
            const hasFiltersFlag = urlParams.get('filters') === '1';
            const skipModal = urlParams.get('skip_filters_modal') === '1';

            const filtersApplied = (hasDateFrom && hasDateTo) || hasFiltersFlag;

            // Check sessionStorage to avoid annoying users repeatedly
            const alreadyPrompted = sessionStorage.getItem('dashboard_filters_prompted') === '1';

            // Auto-open modal if:
            // - Filters are NOT applied
            // - User hasn't been prompted in this session
            // - No skip_filters_modal flag in URL
            if (!filtersApplied && !alreadyPrompted && !skipModal) {
                // Mark as prompted to avoid opening again in this session
                sessionStorage.setItem('dashboard_filters_prompted', '1');

                // Dispatch Livewire event to open the filters modal
                window.Livewire.dispatch('open-dashboard-filters');
            }
        }
    }" x-init="init()"></div>

    <style>
        /* 1) اجعل RTL على مستوى الصفحة بالكامل */
        .rtl-dashboard {
            direction: rtl;
        }

        /* 2) محاذاة النصوص داخل عناصر Filament بدون تكسير layout */
        .rtl-dashboard .fi-section,
        .rtl-dashboard .fi-header-heading,
        .rtl-dashboard .fi-section-header-heading,
        .rtl-dashboard .fi-section-header-description {
            text-align: right;
        }

        .rtl-dashboard .fi-input-wrp input,
        .rtl-dashboard .fi-select-wrp select,
        .rtl-dashboard .fi-ta-search-field input {
            text-align: right;
        }

        /* 3) جدول Filament */
        .rtl-dashboard .fi-ta-table {
            direction: rtl;
        }

        .rtl-dashboard .fi-ta-table th,
        .rtl-dashboard .fi-ta-table td {
            text-align: right;
        }

        /* أرقام/مبالغ: خليه end يبقى يسار عشان القراءة تكون صح */
        .rtl-dashboard .fi-ta-table td[align="end"] {
            text-align: left !important;
        }

        /* 4) تحسين الشكل */
        .rtl-dashboard .fi-stats-overview-stat,
        .rtl-dashboard .fi-widget,
        .rtl-dashboard .fi-section {
            border-radius: 0.75rem;
        }

        /* 5) تصغير الـ padding العلوي لو الهيدر كبير */
        .rtl-dashboard .fi-main {
            padding-top: 0.75rem;
        }
    </style>
</x-filament-panels::page>
