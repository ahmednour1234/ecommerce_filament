<x-filament-panels::page class="rtl-dashboard">

    {{-- Optional: عنوان + سطر صغير + مكان واضح للأكشنز --}}
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">لوحة التحكم</h1>
            <p class="text-sm text-gray-500">ملخص سريع للأداء + فلاتر لتحديد الفترة</p>
        </div>

        {{-- IMPORTANT:
            Filament should render header actions automatically,
            but with custom view sometimes you need to explicitly render them.
        --}}
        <div class="flex items-center gap-2">
            @if (method_exists($this, 'getCachedHeaderActions'))
                @foreach ($this->getCachedHeaderActions() as $action)
                    {{ $action }}
                @endforeach
            @endif
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
