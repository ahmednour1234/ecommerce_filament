<x-filament-panels::page class="rtl-dashboard" x-data="{ activeTab: '{{ ($this->getDashboardTabs()[0]['id'] ?? 'main') }}' }">
    @php $tabs = $this->getDashboardTabs(); @endphp

    <div class="mb-6">
        <div class="fi-section rounded-xl bg-white shadow-sm border border-gray-200/60 dark:border-white/10 dark:bg-white/5 p-4">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-white/10 pb-3 mb-4 -mx-2 px-2 overflow-x-auto">
                @foreach($tabs as $tab)
                <button type="button"
                    @click="activeTab = '{{ $tab['id'] }}'"
                    x-bind:class="activeTab === '{{ $tab['id'] }}' ? 'fi-tabs-item-active bg-primary-500 text-white' : 'fi-tabs-item bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                    class="fi-tabs-item rounded-lg px-4 py-2 text-sm font-medium transition">
                    {{ $tab['label'] }}
                </button>
                @endforeach
            </div>

            <div class="space-y-6">
                @foreach($tabs as $tab)
                <div x-show="activeTab === '{{ $tab['id'] }}'" x-transition class="space-y-6"
                    @if(!$loop->first) style="display: none;" @endif>
                    @if(!empty($tab['widgets']))
                    <x-filament-widgets::widgets :widgets="$tab['widgets']" :columns="$this->getHeaderWidgetsColumns()" />
                    @endif
                    @if(!empty($tab['footer']))
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach(array_chunk($tab['footer'], 2) as $chunk)
                        <x-filament-widgets::widgets :widgets="$chunk" :columns="1" />
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
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

        /* كروت عقود الاستقدام البسيطة - أصغر وأوضح */
        .rtl-dashboard .recruitment-stat-card-simple {
            padding: 0.75rem 1rem;
        }
        .rtl-dashboard .recruitment-stat-card-simple .fi-stats-overview-stat-value {
            font-size: 1.25rem;
        }
    </style>
</x-filament-panels::page>
