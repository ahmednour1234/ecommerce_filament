<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Models\Recruitment\RecruitmentContract;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecruitmentCoordinationLatestTableWidget extends BaseWidget
{
    protected static ?string $heading = 'آخر العقود وحالاتها';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return RecruitmentContractResource::getUserSection() === RecruitmentContract::SECTION_COORDINATION
            || RecruitmentContractResource::getUserSection() === null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                RecruitmentContract::query()
                    ->where(fn ($q) => $q->where('current_section', RecruitmentContract::SECTION_COORDINATION)->orWhere('status', 'received'))
                    ->latest('updated_at')
                    ->limit(20)
            )
            ->columns([
                TextColumn::make('contract_no')->label('رقم العقد')->url(fn ($record) => RecruitmentContractResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('client.name_ar')->label('العميل')->formatStateUsing(fn ($s, $record) => $record->client ? (app()->getLocale() === 'ar' ? $record->client->name_ar : $record->client->name_en) : '—'),
                TextColumn::make('status')->label('الحالة')->formatStateUsing(fn ($s) => tr("recruitment_contract.status.{$s}", [], null, 'dashboard') ?: $s),
                TextColumn::make('updated_at')->label('آخر تحديث')->dateTime()->sortable(),
            ])
            ->paginated([10, 20]);
    }
}
