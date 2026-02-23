<x-filament-panels::page>
    <div>
        <div class="space-y-6">
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <form wire:submit.prevent wire:key="filter-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{ $this->filterForm }}
                    </div>
                </form>
            </div>

        <div
            x-data="{
                calendar: null,
                events: [],
                summary: null,
                employee: null,
                loading: true,
                get jsonUrl() {
                    return @js($this->getJsonUrl());
                },
                async init() {
                    await this.loadEventsIfNeeded();
                    this.initCalendar();
                },
                async loadEventsIfNeeded() {
                    const url = this.jsonUrl;
                    if (url) {
                        this.loading = true;
                        await this.loadEvents();
                    } else {
                        this.loading = false;
                    }
                },
                async loadEvents() {
                    const url = this.jsonUrl;
                    if (!url) {
                        this.loading = false;
                        return;
                    }
                    try {
                        const response = await fetch(url);
                        const data = await response.json();
                        this.events = data.events || [];
                        this.summary = data.summary || null;
                        this.employee = data.employee || null;

                        if (this.calendar) {
                            this.calendar.removeAllEvents();
                            this.calendar.addEventSource(this.events);
                        }
                    } catch (error) {
                        console.error('Error loading attendance data:', error);
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
                        firstDay: @js(config('app.locale') === 'ar' ? 6 : 1),
                        eventClick: function(info) {
                            const props = info.event.extendedProps;
                            let content = '<div class="p-4 space-y-2">';
                            content += '<h3 class="font-bold text-lg">' + info.event.title + '</h3>';

                            if (props.expected_start_time && props.expected_end_time) {
                                content += '<p><strong>موعد العمل المتوقع:</strong> ' + props.expected_start_time + ' - ' + props.expected_end_time + '</p>';
                            }

                            if (props.schedule_name) {
                                content += '<p><strong>جدول العمل:</strong> ' + props.schedule_name + '</p>';
                            }

                            if (props.first_in) {
                                content += '<p><strong>وقت الدخول:</strong> ' + props.first_in + '</p>';
                            }

                            if (props.last_out) {
                                content += '<p><strong>وقت الخروج:</strong> ' + props.last_out + '</p>';
                            }

                            content += '<p><strong>ساعات العمل:</strong> ' + props.worked_hours + ' ساعة</p>';

                            if (props.late_minutes > 0) {
                                content += '<p><strong>دقائق التأخير:</strong> ' + props.late_minutes + ' دقيقة</p>';
                            }

                            if (props.overtime_minutes > 0) {
                                content += '<p><strong>دقائق الإضافي:</strong> ' + props.overtime_minutes + ' دقيقة</p>';
                            }

                            content += '</div>';

                            alert(content.replace(/<[^>]*>/g, ''));
                        }
                    });

                    this.calendar.render();
                },
                watchJsonUrl() {
                    if (this.jsonUrl) {
                        this.loading = true;
                        this.loadEvents();
                    }
                }
            }"
            x-init="init()"
            class="w-full"
        >
            <div
                x-show="loading"
                class="flex items-center justify-center p-8"
            >
                <x-filament::loading-indicator />
            </div>

            <div
                x-show="!loading && !jsonUrl"
                class="rounded-lg bg-info-50 dark:bg-info-900/20 p-4 border border-info-200 dark:border-info-800"
            >
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-information-circle class="h-5 w-5 text-info-600 dark:text-info-400" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-info-800 dark:text-info-200">
                            {{ tr('messages.select_employee_and_month', [], null, 'dashboard') ?: 'Please select an employee and month to view attendance calendar.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                x-show="!loading && jsonUrl && summary"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6"
            >
                <div class="rounded-lg bg-success-50 dark:bg-success-900/20 p-4 border border-success-200 dark:border-success-800">
                    <div class="text-sm font-medium text-success-800 dark:text-success-200">
                        {{ tr('fields.present_days', [], null, 'dashboard') ?: 'Present Days' }}
                    </div>
                    <div class="text-2xl font-bold text-success-600 dark:text-success-400" x-text="summary?.present_days || 0"></div>
                </div>

                <div class="rounded-lg bg-danger-50 dark:bg-danger-900/20 p-4 border border-danger-200 dark:border-danger-800">
                    <div class="text-sm font-medium text-danger-800 dark:text-danger-200">
                        {{ tr('fields.absent_days', [], null, 'dashboard') ?: 'Absent Days' }}
                    </div>
                    <div class="text-2xl font-bold text-danger-600 dark:text-danger-400" x-text="summary?.absent_days || 0"></div>
                </div>

                <div class="rounded-lg bg-primary-50 dark:bg-primary-900/20 p-4 border border-primary-200 dark:border-primary-800">
                    <div class="text-sm font-medium text-primary-800 dark:text-primary-200">
                        {{ tr('fields.total_worked_hours', [], null, 'dashboard') ?: 'Total Worked Hours' }}
                    </div>
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400" x-text="(summary?.total_worked_hours || 0).toFixed(2)"></div>
                </div>

                <div class="rounded-lg bg-warning-50 dark:bg-warning-900/20 p-4 border border-warning-200 dark:border-warning-800">
                    <div class="text-sm font-medium text-warning-800 dark:text-warning-200">
                        {{ tr('fields.total_late_minutes', [], null, 'dashboard') ?: 'Total Late Minutes' }}
                    </div>
                    <div class="text-2xl font-bold text-warning-600 dark:text-warning-400" x-text="summary?.total_late_minutes || 0"></div>
                </div>

                <div class="rounded-lg bg-info-50 dark:bg-info-900/20 p-4 border border-info-200 dark:border-info-800">
                    <div class="text-sm font-medium text-info-800 dark:text-info-200">
                        {{ tr('fields.total_overtime_minutes', [], null, 'dashboard') ?: 'Total Overtime Minutes' }}
                    </div>
                    <div class="text-2xl font-bold text-info-600 dark:text-info-400" x-text="summary?.total_overtime_minutes || 0"></div>
                </div>

                <div class="rounded-lg bg-gray-50 dark:bg-gray-900/20 p-4 border border-gray-200 dark:border-gray-800">
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-200">
                        {{ tr('fields.average_worked_hours', [], null, 'dashboard') ?: 'Average Worked Hours/Day' }}
                    </div>
                    <div class="text-2xl font-bold text-gray-600 dark:text-gray-400" x-text="(summary?.average_worked_hours_per_day || 0).toFixed(2)"></div>
                </div>

                <div class="rounded-lg bg-purple-50 dark:bg-purple-900/20 p-4 border border-purple-200 dark:border-purple-800">
                    <div class="text-sm font-medium text-purple-800 dark:text-purple-200">
                        {{ tr('fields.holiday_days', [], null, 'dashboard') ?: 'Holiday Days' }}
                    </div>
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" x-text="summary?.holiday_days || 0"></div>
                </div>
            </div>

            <div
                x-show="!loading && jsonUrl"
                x-ref="calendar"
                id="monthly-attendance-calendar"
            ></div>
        </div>
        </div>
    </div>
</x-filament-panels::page>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    @if(app()->getLocale() === 'ar')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/ar.js"></script>
    @endif
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updated', ({ el, component }) => {
                const calendarEl = document.querySelector('[x-data*="calendar"]');
                if (calendarEl && calendarEl._x_dataStack) {
                    const alpineData = calendarEl._x_dataStack[0];
                    if (alpineData && typeof alpineData.loadEventsIfNeeded === 'function') {
                        alpineData.loadEventsIfNeeded();
                    }
                }
            });
        });
    </script>
@endpush
