<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Models\Recruitment\RecruitmentContract;
use App\Services\Recruitment\ContractAlertsService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecruitmentCoordinationDelayedTableWidget extends BaseWidget
{
    protected static ?string $heading = 'عقود فيها تأخير للحالة';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return RecruitmentContractResource::getUserSection() === RecruitmentContract::SECTION_COORDINATION
            || RecruitmentContractResource::getUserSection() === null;
    }

    public function table(Table $table): Table
    {
        $alerts = app(ContractAlertsService::class)->getContractsExceedingExpectedTime();
        $ids = $alerts->pluck('id')->toArray();

        return $table
            ->query(RecruitmentContract::query()->whereIn('id', $ids ?: [0])->with('client'))
            ->columns([
                TextColumn::make('contract_no')->label('رقم العقد')->url(fn ($record) => RecruitmentContractResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('client.name_ar')->label('العميل')->formatStateUsing(fn ($s, $record) => $record->client ? (app()->getLocale() === 'ar' ? $record->client->name_ar : $record->client->name_en) : '—'),
                TextColumn::make('status')->label('الحالة')->formatStateUsing(fn ($s) => tr("recruitment_contract.status.{$s}", [], null, 'dashboard') ?: $s),
            ])
            ->emptyStateHeading('لا توجد عقود متأخرة')
            ->paginated([10, 25]);
    }
}
