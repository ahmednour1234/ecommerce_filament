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

    // TODO: Replace with actual model when it exists
    // protected static ?string $model = \App\Models\Housing\HousingRequest::class;
    protected static ?string $model = null;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'housing';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.housing_requests';

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

                        Forms\Components\Select::make('type')
                            ->label(tr('housing.requests.type', [], null, 'dashboard') ?: 'Type')
                            ->options([
                                'delivery' => tr('housing.requests.type.delivery', [], null, 'dashboard') ?: 'تسليم',
                                'return' => tr('housing.requests.type.return', [], null, 'dashboard') ?: 'استرجاع',
                            ])
                            ->required()
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

                Tables\Columns\BadgeColumn::make('type')
                    ->label(tr('housing.requests.type', [], null, 'dashboard') ?: 'Type')
                    ->color(fn (string $state): string => match ($state) {
                        'delivery' => 'success',
                        'return' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => tr("housing.requests.type.{$state}", [], null, 'dashboard') ?: $state)
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
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'approved'])),
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
