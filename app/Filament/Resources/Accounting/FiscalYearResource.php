<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Resources\Accounting\FiscalYearResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Accounting\FiscalYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Concerns\AccountingModuleGate;
class FiscalYearResource extends Resource
{
    use TranslatableNavigation,AccountingModuleGate;

    protected static ?string $model = FiscalYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 12;
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.fiscal_years';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Fiscal Year Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(trans_dash('accounting.fiscal_year', 'Fiscal Year Name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., FY 2025'),

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

                        Forms\Components\Toggle::make('is_active')
                            ->label(trans_dash('accounting.active', 'Active'))
                            ->default(true)
                            ->required(),

                        Forms\Components\Toggle::make('is_closed')
                            ->label(trans_dash('accounting.closed', 'Closed'))
                            ->default(false)
                            ->disabled(fn ($record) => $record && $record->is_closed)
                            ->helperText(trans_dash('accounting.cannot_reopen_closed_year', 'Once closed, fiscal year cannot be reopened')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.fiscal_years.fiscal_year', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(tr('tables.fiscal_years.start_date', [], null, 'dashboard'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(tr('tables.fiscal_years.end_date', [], null, 'dashboard'))
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.fiscal_years.active', [], null, 'dashboard'))
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_closed')
                    ->label(tr('tables.fiscal_years.closed', [], null, 'dashboard'))
                    ->boolean()
                    ->color(fn ($record) => $record->is_closed ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('periods_count')
                    ->label(tr('tables.fiscal_years.periods', [], null, 'dashboard'))
                    ->counts('periods')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.fiscal_years.filters.active', [], null, 'dashboard'))
                    ->placeholder(tr('tables.fiscal_years.filters.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.fiscal_years.filters.active_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.fiscal_years.filters.inactive_only', [], null, 'dashboard')),

                Tables\Filters\TernaryFilter::make('is_closed')
                    ->label(tr('tables.fiscal_years.filters.closed', [], null, 'dashboard'))
                    ->placeholder(tr('tables.fiscal_years.filters.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.fiscal_years.filters.closed_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.fiscal_years.filters.open_only', [], null, 'dashboard')),
            ])
            ->actions([
                Tables\Actions\Action::make('close')
                    ->label(trans_dash('accounting.close', 'Close'))
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (FiscalYear $record) {
                        if ($record->is_closed) {
                            throw new \Exception(trans_dash('accounting.already_closed', 'Fiscal year is already closed.'));
                        }
                        $record->update([
                            'is_closed' => true,
                            'closed_at' => now(),
                            'closed_by' => auth()->id(),
                        ]);
                    })
                    ->visible(fn (FiscalYear $record) => !$record->is_closed && (auth()->user()?->can('fiscal_years.close') ?? false)),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('fiscal_years.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('fiscal_years.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('fiscal_years.delete') ?? false),
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
            'index' => Pages\ListFiscalYears::route('/'),
            'create' => Pages\CreateFiscalYear::route('/create'),
            'edit' => Pages\EditFiscalYear::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('fiscal_years.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('fiscal_years.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('fiscal_years.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('fiscal_years.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

