<?php

namespace App\Filament\Pages\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Services\Recruitment\ContractAlertsService;
use App\Models\Recruitment\Nationality;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ContractAlertsPage extends Page implements HasTable
{
    use TranslatableNavigation;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationGroup = 'notifications';
    protected static ?string $navigationLabel = 'تنبيهات العقود';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.recruitment.contract-alerts';
    protected static ?string $title = null;

    public function getTitle(): string | Htmlable
    {
        return tr('recruitment_contract.alerts.title', [], null, 'dashboard') ?: 'تنبيهات العقود';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('recruitment_contracts.view_any') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return 'تنبيهات العقود';
    }

    public function table(Table $table): Table
    {
        $alertsService = app(ContractAlertsService::class);
        $alerts = $alertsService->getAllAlerts();
        $alertIds = $alerts->pluck('id')->toArray();

        return $table
            ->query(
                \App\Models\Recruitment\RecruitmentContract::query()
                    ->whereIn('id', $alertIds ?: [0])
                    ->with(['client', 'branch', 'nationality'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('contract_no')
                    ->label(tr('recruitment_contract.fields.contract_no', [], null, 'dashboard') ?: 'رقم العقد')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.name_ar')
                    ->label(tr('recruitment_contract.fields.client', [], null, 'dashboard') ?: 'العميل')
                    ->formatStateUsing(fn ($state, $record) => app()->getLocale() === 'ar' ? $record->client->name_ar : $record->client->name_en)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('common.created_at', [], null, 'dashboard') ?: 'تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('recruitment_contract.fields.status', [], null, 'dashboard') ?: 'الحالة')
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'primary',
                        'foreign_embassy_approval', 'external_sending_office_approval', 'foreign_labor_ministry_approval' => 'info',
                        'accepted_by_external_sending_office', 'accepted_by_foreign_labor_ministry', 'visa_issued', 'arrived_in_saudi_arabia', 'labor_services_transfer' => 'success',
                        'sent_to_saudi_embassy', 'return_during_warranty', 'outside_kingdom_during_warranty', 'temporary' => 'warning',
                        'runaway' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => tr("recruitment_contract.status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('alert_type')
                    ->label(tr('recruitment_contract.alerts.alert_type', [], null, 'dashboard') ?: 'نوع التنبيه')
                    ->formatStateUsing(function ($record) use ($alerts) {
                        $alert = $alerts->firstWhere('id', $record->id);
                        if (!$alert) return '';
                        
                        if ($alert->alert_type === 'over_25_days') {
                            return new HtmlString(
                                '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">' .
                                tr('recruitment_contract.alerts.over_25_days', [], null, 'dashboard') ?: 'مر عليه 25 يوم' .
                                '</span>'
                            );
                        } else {
                            return new HtmlString(
                                '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">' .
                                tr('recruitment_contract.alerts.over_3_months', [], null, 'dashboard') ?: 'مر عليه 3 شهور' .
                                '</span>'
                            );
                        }
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('days_overdue')
                    ->label(tr('recruitment_contract.alerts.days_overdue', [], null, 'dashboard') ?: 'الأيام المتأخرة')
                    ->formatStateUsing(function ($record) use ($alerts) {
                        $alert = $alerts->firstWhere('id', $record->id);
                        if (!$alert) return '';
                        
                        if (isset($alert->days_overdue)) {
                            return $alert->days_overdue . ' ' . (tr('recruitment_contract.alerts.days', [], null, 'dashboard') ?: 'يوم');
                        } elseif (isset($alert->months_overdue)) {
                            return $alert->months_overdue . ' ' . (tr('recruitment_contract.alerts.months', [], null, 'dashboard') ?: 'شهر');
                        }
                        return '';
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('alert_type')
                    ->label(tr('recruitment_contract.alerts.alert_type', [], null, 'dashboard') ?: 'نوع التنبيه')
                    ->options([
                        'over_25_days' => tr('recruitment_contract.alerts.over_25_days', [], null, 'dashboard') ?: 'مر عليه 25 يوم',
                        'over_3_months' => tr('recruitment_contract.alerts.over_3_months', [], null, 'dashboard') ?: 'مر عليه 3 شهور',
                    ])
                    ->query(function ($query, array $data) use ($alerts) {
                        if (!isset($data['value'])) {
                            return $query;
                        }
                        
                        $filteredAlerts = $alerts->where('alert_type', $data['value']);
                        $filteredIds = $filteredAlerts->pluck('id')->toArray();
                        return $query->whereIn('id', $filteredIds ?: [0]);
                    }),

                Tables\Filters\SelectFilter::make('nationality_id')
                    ->label(tr('recruitment_contract.fields.nationality', [], null, 'dashboard') ?: 'بالجنسية')
                    ->options(function () {
                        return Nationality::where('is_active', true)
                            ->get()
                            ->mapWithKeys(function ($nationality) {
                                $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                                return [$nationality->id => $label];
                            })
                            ->toArray();
                    })
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(tr('common.view', [], null, 'dashboard') ?: 'عرض')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => RecruitmentContractResource::getUrl('view', ['record' => $record])),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
