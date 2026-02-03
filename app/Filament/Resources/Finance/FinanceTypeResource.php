<?php

namespace App\Filament\Resources\Finance;

use App\Filament\Resources\Finance\FinanceTypeResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\FinanceModuleGate;
use App\Models\Finance\FinanceType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FinanceTypeResource extends Resource
{
    use TranslatableNavigation, FinanceModuleGate;

    protected static ?string $model = FinanceType::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'finance';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationTranslationKey = 'sidebar.finance.types';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('kind')
                            ->label(tr('forms.finance_types.kind', [], null, 'dashboard') ?: 'Kind')
                            ->options([
                                'income' => tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income',
                                'expense' => tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('name.ar')
                            ->label(tr('forms.finance_types.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name.en')
                            ->label(tr('forms.finance_types.name_en', [], null, 'dashboard') ?: 'Name (English)')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label(tr('forms.finance_types.code', [], null, 'dashboard') ?: 'Code')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, $get) {
                                return $rule->where('kind', $get('kind'));
                            }),

                        Forms\Components\TextInput::make('sort')
                            ->label(tr('forms.finance_types.sort', [], null, 'dashboard') ?: 'Sort Order')
                            ->numeric()
                            ->default(0),

                        Forms\Components\Toggle::make('is_active')
                            ->label(tr('forms.finance_types.is_active', [], null, 'dashboard') ?: 'Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('kind')
                    ->label(tr('tables.finance_types.kind', [], null, 'dashboard') ?: 'Kind')
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'income' 
                        ? (tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income')
                        : (tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_text')
                    ->label(tr('tables.finance_types.name', [], null, 'dashboard') ?: 'Name')
                    ->getStateUsing(fn ($record) => $record->name_text)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$search}%"]);
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label(tr('tables.finance_types.code', [], null, 'dashboard') ?: 'Code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sort')
                    ->label(tr('tables.finance_types.sort', [], null, 'dashboard') ?: 'Sort')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.finance_types.is_active', [], null, 'dashboard') ?: 'Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('transactions_count')
                    ->label(tr('tables.finance_types.transactions_count', [], null, 'dashboard') ?: 'Transactions')
                    ->counts('transactions')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kind')
                    ->label(tr('tables.finance_types.kind', [], null, 'dashboard') ?: 'Kind')
                    ->options([
                        'income' => tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income',
                        'expense' => tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.finance_types.is_active', [], null, 'dashboard') ?: 'Active')
                    ->placeholder(tr('common.all', [], null, 'dashboard') ?: 'All')
                    ->trueLabel(tr('common.active_only', [], null, 'dashboard') ?: 'Active only')
                    ->falseLabel(tr('common.inactive_only', [], null, 'dashboard') ?: 'Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.manage_types') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.manage_types') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.manage_types') ?? false),
                ]),
            ])
            ->defaultSort('sort');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinanceTypes::route('/'),
            'create' => Pages\CreateFinanceType::route('/create'),
            'edit' => Pages\EditFinanceType::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('finance.view_types') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('finance.manage_types') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('finance.manage_types') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('finance.manage_types') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('finance.manage_types') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
