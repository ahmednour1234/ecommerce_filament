<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\WorkPlaceResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\HR\WorkPlace;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkPlaceResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = WorkPlace::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'HR > Attendance';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'navigation.hr_work_places';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(tr('fields.name', [], null, 'dashboard') ?: 'Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('address')
                            ->label(tr('fields.address', [], null, 'dashboard') ?: 'Address')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('latitude')
                            ->label(tr('fields.latitude', [], null, 'dashboard') ?: 'Latitude')
                            ->required()
                            ->numeric()
                            ->step(0.0000001)
                            ->suffix('°'),

                        Forms\Components\TextInput::make('longitude')
                            ->label(tr('fields.longitude', [], null, 'dashboard') ?: 'Longitude')
                            ->required()
                            ->numeric()
                            ->step(0.0000001)
                            ->suffix('°'),

                        Forms\Components\TextInput::make('radius_meters')
                            ->label(tr('fields.radius_meters', [], null, 'dashboard') ?: 'Radius (meters)')
                            ->required()
                            ->numeric()
                            ->default(50)
                            ->minValue(1)
                            ->suffix('m'),

                        Forms\Components\Select::make('default_schedule_id')
                            ->label(tr('fields.default_schedule', [], null, 'dashboard') ?: 'Default Schedule')
                            ->relationship('defaultSchedule', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Toggle::make('status')
                            ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('fields.name', [], null, 'dashboard') ?: 'Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('address')
                    ->label(tr('fields.address', [], null, 'dashboard') ?: 'Address')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('latitude')
                    ->label(tr('fields.latitude', [], null, 'dashboard') ?: 'Latitude')
                    ->numeric(decimalPlaces: 7)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('longitude')
                    ->label(tr('fields.longitude', [], null, 'dashboard') ?: 'Longitude')
                    ->numeric(decimalPlaces: 7)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('radius_meters')
                    ->label(tr('fields.radius_meters', [], null, 'dashboard') ?: 'Radius')
                    ->suffix(' m')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('defaultSchedule.name')
                    ->label(tr('fields.default_schedule', [], null, 'dashboard') ?: 'Default Schedule')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.common.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_status')
                    ->label(fn (WorkPlace $record) => $record->status ? tr('actions.deactivate', [], null, 'dashboard') : tr('actions.activate', [], null, 'dashboard'))
                    ->icon(fn (WorkPlace $record) => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (WorkPlace $record) => $record->status ? 'danger' : 'success')
                    ->action(function (WorkPlace $record) {
                        app(\App\Services\HR\WorkPlaceService::class)->toggleStatus($record);
                    })
                    ->visible(fn () => auth()->user()?->can('hr_work_places.update') ?? false),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_work_places.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_work_places.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_work_places.delete') ?? false),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkPlaces::route('/'),
            'create' => Pages\CreateWorkPlace::route('/create'),
            'edit' => Pages\EditWorkPlace::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_work_places.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_work_places.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_work_places.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_work_places.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

