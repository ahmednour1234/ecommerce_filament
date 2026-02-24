<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\HolidayResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\Holiday;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HolidayResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = Holiday::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?string $navigationLabel = 'العطلات الرسمية';
    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(tr('fields.holiday_name', [], null, 'dashboard') ?: 'Holiday Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('start_date')
                            ->label(tr('fields.start_date', [], null, 'dashboard') ?: 'Start Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d'),

                        Forms\Components\TextInput::make('days_count')
                            ->label(tr('fields.days_count', [], null, 'dashboard') ?: 'Days Count')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(1)
                            ->helperText(tr('fields.days_count.helper', [], null, 'dashboard') ?: 'Number of days for this holiday'),

                        Forms\Components\Textarea::make('description')
                            ->label(tr('fields.description', [], null, 'dashboard') ?: 'Description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->nullable(),

                        Forms\Components\Placeholder::make('end_date')
                            ->label(tr('fields.end_date', [], null, 'dashboard') ?: 'End Date')
                            ->content(fn ($record) => $record?->end_date?->format('Y-m-d') ?? 'Will be calculated automatically')
                            ->visible(fn ($record) => $record !== null),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('fields.holiday_name', [], null, 'dashboard') ?: 'Holiday Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(tr('fields.start_date', [], null, 'dashboard') ?: 'Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(tr('fields.end_date', [], null, 'dashboard') ?: 'End Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('days_count')
                    ->label(tr('fields.days_count', [], null, 'dashboard') ?: 'Days')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('description')
                    ->label(tr('fields.description', [], null, 'dashboard') ?: 'Description')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label(tr('tables.hr_holidays.created_by', [], null, 'dashboard') ?: 'Created By')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.hr_holidays.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('start_date')
                    ->form([
                        Forms\Components\DatePicker::make('start_from')
                            ->label('Start From'),
                        Forms\Components\DatePicker::make('start_until')
                            ->label('Start Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['start_from'],
                                fn ($query, $date) => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['start_until'],
                                fn ($query, $date) => $query->whereDate('start_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_holidays.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_holidays.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_holidays.delete') ?? false),
                ]),
            ])
            ->defaultSort('start_date', 'desc');
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
            'index' => Pages\ListHolidays::route('/'),
            'create' => Pages\CreateHoliday::route('/create'),
            'edit' => Pages\EditHoliday::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_holidays.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_holidays.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_holidays.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_holidays.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('hr_holidays.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

