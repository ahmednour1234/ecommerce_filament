<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Resources\Accounting\PeriodResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Accounting\Period;
use App\Models\Accounting\FiscalYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Concerns\AccountingModuleGate;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class PeriodResource extends Resource
{
    use TranslatableNavigation, AccountingModuleGate;

    protected static ?string $model = Period::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 13;
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.periods';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Period Information')
                    ->schema([
                        Forms\Components\Select::make('fiscal_year_id')
                            ->label(trans_dash('accounting.fiscal_year', 'Fiscal Year'))
                            ->relationship('fiscalYear', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive(),

                        Forms\Components\TextInput::make('name')
                            ->label(trans_dash('accounting.period', 'Period Name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., January 2025'),

                        Forms\Components\TextInput::make('period_number')
                            ->label(trans_dash('accounting.period_number', 'Period Number'))
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(12)
                            ->helperText('Period number within the fiscal year (1-12)'),

                        Forms\Components\DatePicker::make('start_date')
                            ->label(trans_dash('accounting.start_date', 'Start Date'))
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d'),

                        Forms\Components\DatePicker::make('end_date')
                            ->label(trans_dash('accounting.end_date', 'End Date'))
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->after('start_date'),

                        Forms\Components\Toggle::make('is_closed')
                            ->label(trans_dash('accounting.closed', 'Closed'))
                            ->default(false)
                            ->disabled(fn ($record) => $record && $record->is_closed)
                            ->helperText(trans_dash('accounting.cannot_reopen_closed_period', 'Once closed, period cannot be reopened')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fiscalYear.name')
                    ->label(tr('tables.periods.fiscal_year', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.periods.period', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period_number')
                    ->label(tr('tables.periods.period_number', [], null, 'dashboard'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(tr('tables.periods.start_date', [], null, 'dashboard'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(tr('tables.periods.end_date', [], null, 'dashboard'))
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_closed')
                    ->label(tr('tables.periods.closed', [], null, 'dashboard'))
                    ->boolean()
                    ->color(fn ($record) => $record->is_closed ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('fiscal_year_id')
                    ->label(tr('tables.periods.filters.fiscal_year', [], null, 'dashboard'))
                    ->relationship('fiscalYear', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_closed')
                    ->label(tr('tables.periods.filters.closed', [], null, 'dashboard'))
                    ->placeholder(tr('tables.periods.filters.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.periods.filters.closed_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.periods.filters.open_only', [], null, 'dashboard')),
            ])
            ->actions([
                Tables\Actions\Action::make('close')
                    ->label(trans_dash('accounting.close', 'Close'))
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Period $record) {
                        if ($record->is_closed) {
                            throw new \Exception(trans_dash('accounting.already_closed', 'Period is already closed.'));
                        }
                        $record->update([
                            'is_closed' => true,
                            'closed_at' => now(),
                            'closed_by' => auth()->id(),
                        ]);
                    })
                    ->visible(fn (Period $record) => !$record->is_closed && (auth()->user()?->can('periods.close') ?? false)),

                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('periods.update') ?? false),
                TableDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('periods.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('periods.delete') ?? false),
                ]),
            ])
            ->defaultSort('fiscal_year_id', 'desc')
            ->defaultSort('period_number', 'asc');
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
            'index' => Pages\ListPeriods::route('/'),
            'create' => Pages\CreatePeriod::route('/create'),
            'edit' => Pages\EditPeriod::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('periods.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('periods.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('periods.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('periods.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

