<x-filament-panels::page class="rtl-dashboard dashboard-sections">
    @php $sections = $this->getDashboardSections(); @endphp

    {{-- ═══ Filter bar (always at top) ═══ --}}
    <x-filament-widgets::widgets
        :widgets="[\App\Filament\Widgets\Dashboard\DashboardFilterWidget::class]"
        :columns="1"
    />

    {{-- ═══ All 4 sections — full page scroll, no tabs ═══ --}}
    <div class="space-y-10 mt-4">
        @foreach($sections as $section)
        <div class="dashboard-section">

            {{-- Section header --}}
            <div class="section-header">
                <span class="section-accent"></span>
                <h2 class="section-title">{{ $section['label'] }}</h2>
            </div>

            {{-- Section rows --}}
            <div class="space-y-6">
                @foreach($section['rows'] as $row)
                    @if(!empty($row['pair']) && count($row['widgets']) >= 2)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($row['widgets'] as $widget)
                            <div class="min-w-0">
                                <x-filament-widgets::widgets :widgets="[$widget]" :columns="1" />
                            </div>
                            @endforeach
                        </div>
                    @else
                        <x-filament-widgets::widgets :widgets="$row['widgets']" :columns="1" />
                    @endif
                @endforeach
            </div>

        </div>
        @endforeach
    </div>

    <style>
        .rtl-dashboard { direction: rtl; }
        .rtl-dashboard .fi-section,
        .rtl-dashboard .fi-header-heading,
        .rtl-dashboard .fi-section-header-heading,
        .rtl-dashboard .fi-section-header-description { text-align: right; }
        .rtl-dashboard .fi-input-wrp input,
        .rtl-dashboard .fi-select-wrp select,
        .rtl-dashboard .fi-ta-search-field input { text-align: right; }
        .rtl-dashboard .fi-ta-table { direction: rtl; }
        .rtl-dashboard .fi-ta-table th,
        .rtl-dashboard .fi-ta-table td { text-align: right; }
        .rtl-dashboard .fi-ta-table td[align="end"] { text-align: left !important; }
        .rtl-dashboard .fi-stats-overview-stat,
        .rtl-dashboard .fi-widget,
        .rtl-dashboard .fi-section { border-radius: 0.75rem; }
        .rtl-dashboard .fi-main { padding-top: 0.75rem; }

        .dashboard-section .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid rgba(var(--primary-400), 0.35);
        }
        .dashboard-section .section-accent {
            display: inline-block;
            width: 0.35rem;
            height: 1.75rem;
            border-radius: 9999px;
            background: rgb(var(--primary-500));
            flex-shrink: 0;
        }
        .dashboard-section .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: rgb(17 24 39);
        }
        .dark .dashboard-section .section-title { color: rgb(243 244 246); }
        .dashboard-sections .grid > div > .fi-wi { height: 100%; }
    </style>
</x-filament-panels::page>
