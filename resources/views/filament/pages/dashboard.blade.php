<x-filament-panels::page class="rtl-dashboard dashboard-with-tabs" x-data="{ activeTab: '{{ ($this->getDashboardTabs()[0]['id'] ?? 'main') }}' }">
    @php $tabs = $this->getDashboardTabs(); @endphp

    <div class="dashboard-tabs-wrapper -mx-4 sm:-mx-6 md:-mx-8 mb-6">
        <div class="dashboard-tabs-bar flex gap-1 overflow-x-auto border-b border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-white/5 px-4 sm:px-6 md:px-8 py-2 scrollbar-thin">
            @foreach($tabs as $tab)
            <button type="button"
                @click="activeTab = '{{ $tab['id'] }}'"
                x-bind:class="activeTab === '{{ $tab['id'] }}' ? 'dashboard-tab-active' : 'dashboard-tab'"
                class="rounded-t-lg px-4 py-2.5 text-sm font-medium whitespace-nowrap transition shrink-0">
                {{ $tab['label'] }}
            </button>
            @endforeach
        </div>
    </div>

    <div class="space-y-6">
        @foreach($tabs as $tab)
        <div x-show="activeTab === '{{ $tab['id'] }}'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-6"
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


    <style>
        .dashboard-tabs-bar .dashboard-tab {
            color: rgb(107 114 128);
            background: transparent;
        }
        .dashboard-tabs-bar .dashboard-tab:hover {
            color: rgb(55 65 81);
            background: rgb(229 231 235 / 0.6);
        }
        .dark .dashboard-tabs-bar .dashboard-tab { color: rgb(156 163 175); }
        .dark .dashboard-tabs-bar .dashboard-tab:hover { color: rgb(229 231 235); background: rgb(255 255 255 / 0.08); }
        .dashboard-tabs-bar .dashboard-tab-active {
            color: white;
            background: rgb(var(--primary-500));
            border-bottom: 2px solid rgb(var(--primary-500));
            margin-bottom: -1px;
        }
        .dashboard-with-tabs .fi-main > div:first-child { margin-top: 0; }
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
