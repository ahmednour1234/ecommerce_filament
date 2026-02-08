<?php

namespace App\Filament\Pages\Recruitment;

use App\Exports\ReceivingRecruitmentExport;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Models\Client;
use App\Models\User;
use App\Services\Recruitment\ReceivingRecruitmentReportService;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReceivingRecruitmentReportPage extends Page implements HasTable
{
    use InteractsWithTable;
    use TranslatableNavigation;

    protected ReceivingRecruitmentReportService $reportService;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'recruitment';
    protected static ?int $navigationSort = 12;
    protected static ?string $navigationTranslationKey = 'sidebar.recruitment.receiving_labor';
    protected static string $view = 'filament.pages.recruitment.receiving-recruitment-report';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_receiving_recruitment_report') ?? false;
    }

    public function getTitle(): string
    {
        return tr('recruitment.receiving_labor.title', [], null, 'dashboard') ?: 'استلام العمالة';
    }

    public function getHeading(): string
    {
        return tr('recruitment.receiving_labor.title', [], null, 'dashboard') ?: 'استلام العمالة';
    }

    public function mount(): void
    {
        $this->reportService = app(ReceivingRecruitmentReportService::class);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->reportService->getBaseQuery())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(tr('recruitment.receiving_labor.table.id', [], null, 'dashboard') ?: 'رقم')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => RecruitmentContractResource::getUrl('view', ['record' => $record]))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('client.name_ar')
                    ->label(tr('recruitment.receiving_labor.table.client', [], null, 'dashboard') ?: 'العميل')
                    ->formatStateUsing(fn ($state, $record) => app()->getLocale() === 'ar' 
                        ? ($record->client->name_ar ?? '') 
                        : ($record->client->name_en ?? ''))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('client', function ($q) use ($search) {
                            $q->where('name_ar', 'like', "%{$search}%")
                              ->orWhere('name_en', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('worker.name_ar')
                    ->label(tr('recruitment.receiving_labor.table.worker', [], null, 'dashboard') ?: 'اسم العامل')
                    ->formatStateUsing(fn ($state, $record) => $record->worker 
                        ? (app()->getLocale() === 'ar' ? $record->worker->name_ar : $record->worker->name_en)
                        : '')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('worker', function ($q) use ($search) {
                            $q->where('name_ar', 'like', "%{$search}%")
                              ->orWhere('name_en', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('worker.passport_number')
                    ->label(tr('recruitment.receiving_labor.table.passport', [], null, 'dashboard') ?: 'رقم الجواز')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('worker', function ($q) use ($search) {
                            $q->where('passport_number', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('arrival_date')
                    ->label(tr('recruitment.receiving_labor.table.arrival_date', [], null, 'dashboard') ?: 'تاريخ الوصول')
                    ->date()
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('trial_end_date')
                    ->label(tr('recruitment.receiving_labor.table.trial_end_date', [], null, 'dashboard') ?: 'تاريخ نهاية فترة التجربة')
                    ->date()
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('contract_end_date')
                    ->label(tr('recruitment.receiving_labor.table.contract_end_date', [], null, 'dashboard') ?: 'تاريخ نهاية العقد')
                    ->date()
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('recruitment.receiving_labor.table.status', [], null, 'dashboard') ?: 'حالة الطلب')
                    ->colors([
                        'success' => 'worker_received',
                        'gray' => 'pending',
                        'danger' => 'canceled',
                    ])
                    ->formatStateUsing(function ($state) {
                        if ($state === 'worker_received') {
                            return tr('recruitment.receiving_labor.status.received', [], null, 'dashboard') ?: 'تم الاستلام';
                        }
                        if ($state === 'pending') {
                            return tr('recruitment.receiving_labor.status.pending', [], null, 'dashboard') ?: 'قيد الانتظار';
                        }
                        if ($state === 'canceled' || $state === 'returned') {
                            return tr('recruitment.receiving_labor.status.canceled', [], null, 'dashboard') ?: 'ملغي';
                        }
                        return $state;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label(tr('recruitment.receiving_labor.table.employee', [], null, 'dashboard') ?: 'الموظف')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('recruitment.receiving_labor.table.status', [], null, 'dashboard') ?: 'حالة الطلب')
                    ->options([
                        'worker_received' => tr('recruitment.receiving_labor.status.received', [], null, 'dashboard') ?: 'تم الاستلام',
                        'pending' => tr('recruitment.receiving_labor.status.pending', [], null, 'dashboard') ?: 'قيد الانتظار',
                        'canceled' => tr('recruitment.receiving_labor.status.canceled', [], null, 'dashboard') ?: 'ملغي',
                    ]),

                Tables\Filters\Filter::make('arrival_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('arrival_from')
                            ->label(tr('tables.filters.date_from', [], null, 'dashboard') ?: 'من تاريخ'),
                        \Filament\Forms\Components\DatePicker::make('arrival_until')
                            ->label(tr('tables.filters.date_to', [], null, 'dashboard') ?: 'إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['arrival_from'], fn ($q, $date) => $q->whereDate('arrival_date', '>=', $date))
                            ->when($data['arrival_until'], fn ($q, $date) => $q->whereDate('arrival_date', '<=', $date));
                    }),

                Tables\Filters\Filter::make('trial_end_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('trial_from')
                            ->label(tr('tables.filters.date_from', [], null, 'dashboard') ?: 'من تاريخ'),
                        \Filament\Forms\Components\DatePicker::make('trial_until')
                            ->label(tr('tables.filters.date_to', [], null, 'dashboard') ?: 'إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['trial_from'], fn ($q, $date) => $q->whereDate('trial_end_date', '>=', $date))
                            ->when($data['trial_until'], fn ($q, $date) => $q->whereDate('trial_end_date', '<=', $date));
                    }),

                Tables\Filters\Filter::make('contract_end_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('contract_from')
                            ->label(tr('tables.filters.date_from', [], null, 'dashboard') ?: 'من تاريخ'),
                        \Filament\Forms\Components\DatePicker::make('contract_until')
                            ->label(tr('tables.filters.date_to', [], null, 'dashboard') ?: 'إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['contract_from'], fn ($q, $date) => $q->whereDate('contract_end_date', '>=', $date))
                            ->when($data['contract_until'], fn ($q, $date) => $q->whereDate('contract_end_date', '<=', $date));
                    }),

                Tables\Filters\SelectFilter::make('client_id')
                    ->label(tr('recruitment.receiving_labor.table.client', [], null, 'dashboard') ?: 'العميل')
                    ->options(function () {
                        return Client::query()
                            ->get()
                            ->mapWithKeys(function ($client) {
                                $name = app()->getLocale() === 'ar' ? $client->name_ar : $client->name_en;
                                return [$client->id => $name];
                            })
                            ->toArray();
                    })
                    ->searchable(),

                Tables\Filters\SelectFilter::make('created_by')
                    ->label(tr('recruitment.receiving_labor.table.employee', [], null, 'dashboard') ?: 'الموظف')
                    ->options(function () {
                        return User::query()
                            ->get()
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable(),
            ])
            ->defaultSort('id', 'desc')
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10)
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(tr('actions.view', [], null, 'dashboard') ?: 'عرض')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => RecruitmentContractResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'تصدير Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return $this->exportToExcel();
                }),

            \Filament\Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard') ?: 'طباعة')
                ->icon('heroicon-o-printer')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }

    protected function exportToExcel(): BinaryFileResponse
    {
        $table = $this->table($this->makeTable());
        $tableQuery = $table->getQuery();
        $contracts = $tableQuery->get();

        $export = new ReceivingRecruitmentExport($contracts);
        $filename = $this->getExportFilename('xlsx');

        return Excel::download($export, $filename);
    }

    protected function getPrintUrl(): string
    {
        $table = $this->table($this->makeTable());
        $tableQuery = $table->getQuery();
        $contracts = $tableQuery->get();

        $headers = $this->reportService->getExportHeaders();
        $rows = $contracts->map(function ($contract) {
            $formatted = $this->reportService->formatContractForExport($contract);
            return [
                $formatted['id'],
                $formatted['contract_no'],
                $formatted['client'],
                $formatted['worker'],
                $formatted['passport_number'],
                $formatted['arrival_date'],
                $formatted['trial_end_date'],
                $formatted['contract_end_date'],
                $formatted['status'],
                $formatted['employee'],
            ];
        })->toArray();

        session()->flash('print_data', [
            'title' => tr('recruitment.receiving_labor.title', [], null, 'dashboard') ?: 'استلام العمالة',
            'headers' => $headers,
            'rows' => $rows,
            'metadata' => [
                'exported_at' => now()->format('Y-m-d H:i:s'),
                'exported_by' => auth()->user()?->name ?? 'System',
                'report_type' => 'receiving_labor',
            ],
        ]);

        return route('filament.exports.print');
    }

    protected function getExportFilename(string $extension = 'xlsx'): string
    {
        $title = tr('recruitment.receiving_labor.title', [], null, 'dashboard') ?: 'receiving_labor';
        $sanitized = preg_replace('/[^a-z0-9]+/i', '_', $title);
        return strtolower($sanitized) . '_' . date('Y-m-d_His') . '.' . $extension;
    }
}
