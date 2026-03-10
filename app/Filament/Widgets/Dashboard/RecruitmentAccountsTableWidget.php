<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Models\Recruitment\RecruitmentContract;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecruitmentAccountsTableWidget extends BaseWidget
{
    protected static ?string $heading = 'عقود استقدام واقفة عند قسم الحسابات';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return RecruitmentContractResource::getUserSection() === RecruitmentContract::SECTION_ACCOUNTS;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(RecruitmentContract::query()->where('current_section', RecruitmentContract::SECTION_ACCOUNTS)->latest()->limit(50))
            ->columns([
                TextColumn::make('contract_no')->label('رقم العقد')->url(fn ($record) => RecruitmentContractResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('client.name_ar')->label('العميل')->formatStateUsing(fn ($s, $record) => $record->client ? (app()->getLocale() === 'ar' ? $record->client->name_ar : $record->client->name_en) : '—'),
                TextColumn::make('payment_status')->label('حالة الدفع')->formatStateUsing(fn ($s) => tr("recruitment_contract.payment_status.{$s}", [], null, 'dashboard') ?: $s),
                TextColumn::make('total_cost')->label('الإجمالي')->money('SAR'),
            ])
            ->emptyStateHeading('لا توجد عقود واقفة')
            ->paginated([10, 25, 50]);
    }
}
