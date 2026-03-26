<x-filament-panels::page class="rtl-dashboard dashboard-sections">
    @php $sections = $this->getDashboardSections(); @endphp

    {{-- ═══ Filter bar (always at top) ═══ --}}
    <x-filament-widgets::widgets
        :widgets="[\App\Filament\Widgets\Dashboard\DashboardFilterWidget::class]"
        :columns="1"
    />

    {{-- ═══ Section quick-navigation pills ═══ --}}
    @if(count($sections) > 1)
    <div class="section-quick-nav flex flex-wrap gap-2 my-2">
        @foreach($sections as $section)
        <a href="#section-{{ $section['id'] }}"
           class="section-nav-pill">
            {{ $section['label'] }}
        </a>
        @endforeach
    </div>
    @endif

    {{-- ═══ The 4 sections ═══ --}}
    <div class="space-y-12 mt-4">
        @foreach($sections as $section)
        <div id="section-{{ $section['id'] }}" class="dashboard-section scroll-mt-20">

            {{-- Section header --}}
            <div class="section-header">
                <span class="section-accent"></span>
                <h2 class="section-title">{{ $section['label'] }}</h2>
            </div>

            {{-- Section rows --}}
            <div class="space-y-6">
                @foreach($section['rows'] as $row)
                    @if(!empty($row['pair']) && count($row['widgets']) >= 2)
                        {{-- Two widgets / tables / charts side by side --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($row['widgets'] as $widget)
                            <div class="min-w-0">
                                <x-filament-widgets::widgets :widgets="[$widget]" :columns="1" />
                            </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Full-width row --}}
                        <x-filament-widgets::widgets :widgets="$row['widgets']" :columns="1" />
                    @endif
                @endforeach
            </div>

        </div>
        @endforeach
    </div>

    <style>
        /* ── RTL & base ── */
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

        /* ── Section header ── */
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

        /* ── Quick-nav pills ── */
        .section-nav-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 1rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 600;
            border: 1.5px solid rgba(var(--primary-400), 0.5);
            color: rgb(var(--primary-700));
            background: rgba(var(--primary-50), 0.7);
            text-decoration: none;
            transition: background 0.15s, border-color 0.15s;
        }
        .section-nav-pill:hover {
            background: rgba(var(--primary-100), 1);
            border-color: rgb(var(--primary-500));
        }
        .dark .section-nav-pill {
            color: rgb(var(--primary-300));
            background: rgba(var(--primary-900), 0.25);
            border-color: rgba(var(--primary-600), 0.5);
        }
        .dark .section-nav-pill:hover {
            background: rgba(var(--primary-800), 0.4);
            border-color: rgb(var(--primary-500));
        }

        /* ── Pair grid: ensure equal height cards ── */
        .dashboard-sections .grid > div > .fi-wi { height: 100%; }
    </style>
</x-filament-panels::page>

