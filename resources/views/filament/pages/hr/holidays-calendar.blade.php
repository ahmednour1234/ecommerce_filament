<x-filament-panels::page>
    <div class="space-y-6">
        @if(!$this->hasHolidays())
            <div class="space-y-4">
                <div class="rounded-lg bg-warning-50 dark:bg-warning-900/20 p-4 border border-warning-200 dark:border-warning-800">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning-600 dark:text-warning-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-warning-800 dark:text-warning-200">
                                {{ tr('messages.no_holidays', [], null, 'dashboard') ?: 'No holidays found. Please add holidays to view them on the calendar.' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="rounded-lg bg-info-50 dark:bg-info-900/20 p-4 border border-info-200 dark:border-info-800">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-information-circle class="h-5 w-5 text-info-600 dark:text-info-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-info-800 dark:text-info-200">
                                {{ tr('messages.no_holidays.info', [], null, 'dashboard') ?: 'Click "Add New Holiday" button to create your first holiday.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div 
            x-data="{
                calendar: null,
                events: [],
                loading: true,
                async init() {
                    await this.loadEvents();
                    this.initCalendar();
                },
                async loadEvents() {
                    try {
                        const response = await fetch('{{ route('filament.admin.pages.hr.holidays-calendar.json') }}');
                        this.events = await response.json();
                    } catch (error) {
                        console.error('Error loading holidays:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                initCalendar() {
                    const calendarEl = this.$refs.calendar;
                    if (!calendarEl) return;

                    this.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        },
                        events: this.events,
                        eventDisplay: 'block',
                        locale: @js(app()->getLocale()),
                        firstDay: @js(config('app.locale') === 'ar' ? 6 : 1), // Saturday for Arabic, Monday for English
                    });

                    this.calendar.render();
                }
            }"
            class="w-full"
        >
            <div 
                x-show="loading" 
                class="flex items-center justify-center p-8"
            >
                <x-filament::loading-indicator />
            </div>

            <div 
                x-show="!loading"
                x-ref="calendar"
                id="holidays-calendar"
            ></div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        @if(app()->getLocale() === 'ar')
            <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/ar.js"></script>
        @endif
    @endpush
</x-filament-panels::page>

