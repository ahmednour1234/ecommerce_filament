<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Models\Recruitment\RecruitmentContract;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecruitmentCustomerServiceTableWidget extends BaseWidget
{
    protected static ?string $heading = 'عقود لم تُحوّل لقسم الحسابات';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return RecruitmentContractResource::getUserSection() === RecruitmentContract::SECTION_CUSTOMER_SERVICE;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(RecruitmentContract::query()->where('current_section', RecruitmentContract::SECTION_CUSTOMER_SERVICE)->latest()->limit(50))
            ->columns([
                TextColumn::make('contract_no')->label('رقم العقد')->searchable()->url(fn ($record) => RecruitmentContractResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('client.name_ar')->label('العميل')->formatStateUsing(fn ($state, $record) => $record->client ? (app()->getLocale() === 'ar' ? $record->client->name_ar : $record->client->name_en) : '—'),
                TextColumn::make('status')->label('الحالة')->formatStateUsing(fn ($s) => tr("recruitment_contract.status.{$s}", [], null, 'dashboard') ?: $s),
                TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime()->sortable(),
            ])
            ->emptyStateHeading('لا توجد عقود لم تُحوّل')
            ->emptyStateDescription('كل العقود تم توجيهها لقسم الحسابات أو لا توجد عقود.')
            ->paginated([10, 25, 50])
            ->headerActions([
                Action::make('create')
                    ->label('إضافة عقد استقدام')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->url(RecruitmentContractResource::getUrl('create')),
            ]);
    }
}
