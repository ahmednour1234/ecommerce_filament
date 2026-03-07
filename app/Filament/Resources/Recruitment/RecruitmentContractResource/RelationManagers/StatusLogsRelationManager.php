<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\Services\Recruitment\RecruitmentContractService;

class StatusLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'statusLogs';

    protected static ?string $title = null;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return tr('recruitment_contract.tabs.status_logs', [], null, 'dashboard') ?: 'سجل الأحداث';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('new_status')
                    ->label(tr('recruitment_contract.fields.new_status', [], null, 'dashboard') ?: 'New Status')
                    ->options([
                        'new' => tr('recruitment_contract.status.new', [], null, 'dashboard') ?: 'جديد',
                        'external_office_approval' => tr('recruitment_contract.status.external_office_approval', [], null, 'dashboard') ?: 'موافقة المكتب الخارجي',
                        'contract_accepted_external_office' => tr('recruitment_contract.status.contract_accepted_external_office', [], null, 'dashboard') ?: 'قبول العقد من مكتب الخارجي',
                        'waiting_approval' => tr('recruitment_contract.status.waiting_approval', [], null, 'dashboard') ?: 'انتظار الابروف',
                        'contract_accepted_labor_ministry' => tr('recruitment_contract.status.contract_accepted_labor_ministry', [], null, 'dashboard') ?: 'قبول العقد من مكتب العمل الخارجي',
                        'sent_to_saudi_embassy' => tr('recruitment_contract.status.sent_to_saudi_embassy', [], null, 'dashboard') ?: 'إرسال التأشيرة إلى السفارة السعودية',
                        'visa_issued' => tr('recruitment_contract.status.visa_issued', [], null, 'dashboard') ?: 'تم التفييز',
                        'travel_permit_after_visa_issued' => tr('recruitment_contract.status.travel_permit_after_visa_issued', [], null, 'dashboard') ?: 'تصريح سفر بعد تم التفييز',
                        'waiting_flight_booking' => tr('recruitment_contract.status.waiting_flight_booking', [], null, 'dashboard') ?: 'انتظار حجز تذكرة الطيران',
                        'arrival_scheduled' => tr('recruitment_contract.status.arrival_scheduled', [], null, 'dashboard') ?: 'معاد الوصول',
                        'received' => tr('recruitment_contract.status.received', [], null, 'dashboard') ?: 'تم الاستلام',
                        'return_during_warranty' => tr('recruitment_contract.status.return_during_warranty', [], null, 'dashboard') ?: 'رجيع خلال فترة الضمان',
                        'runaway' => tr('recruitment_contract.status.runaway', [], null, 'dashboard') ?: 'هروب',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('status_date')
                    ->label('تاريخ الحالة')
                    ->default(now())
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label(tr('recruitment_contract.fields.notes', [], null, 'dashboard') ?: 'Notes')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('new_status')
            ->columns([
                Tables\Columns\TextColumn::make('old_status')
                    ->label(tr('recruitment_contract.fields.old_status', [], null, 'dashboard') ?: 'Old Status')
                    ->formatStateUsing(fn ($state) => $state ? tr("recruitment_contract.status.{$state}", [], null, 'dashboard') : '-'),

                Tables\Columns\TextColumn::make('new_status')
                    ->label(tr('recruitment_contract.fields.new_status', [], null, 'dashboard') ?: 'New Status')
                    ->formatStateUsing(fn ($state) => tr("recruitment_contract.status.{$state}", [], null, 'dashboard')),

                Tables\Columns\TextColumn::make('duration')
                    ->label('المدة المتوقعة')
                    ->getStateUsing(function ($record) {
                        $service = app(RecruitmentContractService::class);
                        $expectedDays = $service->getExpectedDaysBetweenStatuses($record->old_status, $record->new_status);
                        if ($expectedDays) {
                            return "{$expectedDays} يوم";
                        }
                        return '-';
                    }),

                Tables\Columns\TextColumn::make('notes')
                    ->label(tr('recruitment_contract.fields.notes', [], null, 'dashboard') ?: 'Notes')
                    ->limit(50),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label(tr('recruitment_contract.fields.created_by', [], null, 'dashboard') ?: 'Created By'),

                Tables\Columns\TextColumn::make('status_date')
                    ->label('تاريخ الحالة')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('common.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة حالة جديدة')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['old_status'] = $this->ownerRecord->status;
                        $data['recruitment_contract_id'] = $this->ownerRecord->id;
                        return $data;
                    })
                    ->after(function ($record) {
                        if ($record->new_status !== $this->ownerRecord->status) {
                            $service = app(\App\Services\Recruitment\RecruitmentContractService::class);
                            $this->ownerRecord->update(['status' => $record->new_status]);
                        }
                    }),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}
