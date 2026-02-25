<?php

namespace App\Filament\Resources\Housing;

use App\Filament\Resources\Housing\HousingRequestResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HousingRequestResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = \App\Models\Housing\HousingRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'قسم الإيواء';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.requests';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('housing.requests.pending', [], null, 'dashboard') ?: 'Pending Requests')
                    ->schema([
                        Forms\Components\TextInput::make('order_no')
                            ->label(tr('housing.requests.order_no', [], null, 'dashboard') ?: 'Order No')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('contract_no')
                            ->label(tr('housing.requests.contract_no', [], null, 'dashboard') ?: 'Contract No')
                            ->columnSpan(1),

                        Forms\Components\Select::make('client_id')
                            ->label(tr('housing.requests.client', [], null, 'dashboard') ?: 'Client')
                            ->relationship('client', 'name_ar')
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('laborer_id')
                            ->label(tr('housing.requests.laborer', [], null, 'dashboard') ?: 'Laborer')
                            ->relationship('laborer', 'name_ar')
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('request_type')
                            ->label(tr('housing.requests.request_type', [], null, 'dashboard') ?: 'نوع الطلب')
                            ->options([
                                'new_rent' => tr('housing.requests.type.new_rent', [], null, 'dashboard') ?: 'إيجار جديد',
                                'cancel_rent' => tr('housing.requests.type.cancel_rent', [], null, 'dashboard') ?: 'إلغاء الإيجار',
                                'transfer_kafala' => tr('housing.requests.type.transfer_kafala', [], null, 'dashboard') ?: 'نقل الكفالة',
                                'outside_service' => tr('housing.requests.type.outside_service', [], null, 'dashboard') ?: 'خارج الخدمة',
                                'leave_request' => tr('housing.requests.type.leave_request', [], null, 'dashboard') ?: 'طلب إجازة',
                            ])
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('building_id')
                            ->label(tr('housing.requests.building', [], null, 'dashboard') ?: 'المبنى')
                            ->relationship('building', 'name_ar')
                            ->searchable()
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\Select::make('unit_id')
                            ->label(tr('housing.requests.unit', [], null, 'dashboard') ?: 'الوحدة')
                            ->relationship('unit', 'unit_number', fn ($query, $get) => $query->where('building_id', $get('building_id')))
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('requested_from')
                            ->label(tr('housing.requests.requested_from', [], null, 'dashboard') ?: 'من تاريخ')
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('requested_to')
                            ->label(tr('housing.requests.requested_to', [], null, 'dashboard') ?: 'إلى تاريخ')
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label(tr('housing.requests.status', [], null, 'dashboard') ?: 'الحالة')
                            ->options([
                                'pending' => tr('housing.requests.status.pending', [], null, 'dashboard') ?: 'معلق',
                                'approved' => tr('housing.requests.status.approved', [], null, 'dashboard') ?: 'موافق عليه',
                                'completed' => tr('housing.requests.status.completed', [], null, 'dashboard') ?: 'مكتمل',
                                'rejected' => tr('housing.requests.status.rejected', [], null, 'dashboard') ?: 'مرفوض',
                                'suspended' => tr('housing.requests.status.suspended', [], null, 'dashboard') ?: 'موقوف',
                            ])
                            ->default('pending')
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('request_date')
                            ->label(tr('housing.requests.request_date', [], null, 'dashboard') ?: 'Request Date')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('housing.requests.notes', [], null, 'dashboard') ?: 'Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\CheckboxColumn::make('selected')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('order_no')
                    ->label(tr('housing.requests.order_no', [], null, 'dashboard') ?: 'Order No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contract_no')
                    ->label(tr('housing.requests.contract_no', [], null, 'dashboard') ?: 'Contract No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.name_ar')
                    ->label(tr('housing.requests.client', [], null, 'dashboard') ?: 'Client')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('laborer.name_ar')
                    ->label(tr('housing.requests.laborer', [], null, 'dashboard') ?: 'Laborer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('request_type')
                    ->label(tr('housing.requests.request_type', [], null, 'dashboard') ?: 'نوع الطلب')
                    ->color(fn (string $state): string => match ($state) {
                        'new_rent' => 'success',
                        'cancel_rent' => 'danger',
                        'transfer_kafala' => 'info',
                        'outside_service' => 'warning',
                        'leave_request' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => tr("housing.requests.type.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('housing.requests.status', [], null, 'dashboard') ?: 'الحالة')
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'approved' => 'info',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        'suspended' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => tr("housing.requests.status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('request_date')
                    ->label(tr('housing.requests.request_date', [], null, 'dashboard') ?: 'Request Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label(tr('housing.requests.notes', [], null, 'dashboard') ?: 'Notes')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('actions')
                    ->label(tr('housing.requests.actions', [], null, 'dashboard') ?: 'Actions')
                    ->formatStateUsing(fn () => '')
                    ->view('filament.tables.columns.housing-action-button'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(tr('housing.requests.type', [], null, 'dashboard') ?: 'Type')
                    ->options([
                        'delivery' => tr('housing.requests.type.delivery', [], null, 'dashboard') ?: 'تسليم',
                        'return' => tr('housing.requests.type.return', [], null, 'dashboard') ?: 'استرجاع',
                    ]),

                Tables\Filters\Filter::make('request_date')
                    ->form([
                        Forms\Components\DatePicker::make('request_from')
                            ->label(tr('housing.dashboard.from_date', [], null, 'dashboard') ?: 'From Date'),
                        Forms\Components\DatePicker::make('request_until')
                            ->label(tr('housing.dashboard.to_date', [], null, 'dashboard') ?: 'To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['request_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('request_date', '>=', $date),
                            )
                            ->when(
                                $data['request_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('request_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(tr('actions.housing.approve', [], null, 'dashboard') ?: 'موافقة')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        if ($record->request_type === 'new_rent' && $record->laborer_id && $record->building_id) {
                            \App\Models\Housing\HousingAssignment::create([
                                'laborer_id' => $record->laborer_id,
                                'building_id' => $record->building_id,
                                'unit_id' => $record->unit_id,
                                'start_date' => $record->requested_from ?? $record->request_date,
                                'end_date' => $record->requested_to,
                                'rent_amount' => 0,
                                'status_id' => \App\Models\Housing\HousingStatus::where('key', 'rented')->first()?->id,
                            ]);
                        }
                    }),

                Tables\Actions\Action::make('reject')
                    ->label(tr('actions.housing.reject', [], null, 'dashboard') ?: 'رفض')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'rejected'])),

                Tables\Actions\Action::make('complete')
                    ->label(tr('actions.housing.complete', [], null, 'dashboard') ?: 'إكمال')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->action(fn ($record) => $record->update(['status' => 'completed'])),

                Tables\Actions\Action::make('suspend')
                    ->label(tr('actions.housing.suspend', [], null, 'dashboard') ?: 'تعليق')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'approved']))
                    ->action(fn ($record) => $record->update(['status' => 'suspended'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('request_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHousingRequests::route('/'),
            'create' => Pages\CreateHousingRequest::route('/create'),
            'edit' => Pages\EditHousingRequest::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('housing.requests.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('housing.requests.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('housing.requests.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('housing.requests.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
