<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Resources\Accounting\AccountResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Accounting\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Concerns\AccountingModuleGate;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class AccountResource extends Resource
{
    use TranslatableNavigation,AccountingModuleGate;

    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.accounts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('forms.accounts.sections.basic_information', [], null, 'dashboard'))
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(tr('forms.accounts.code.label', [], null, 'dashboard'))
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText(tr('forms.accounts.code.helper', [], null, 'dashboard')),

                        Forms\Components\TextInput::make('name')
                            ->label(tr('forms.accounts.name.label', [], null, 'dashboard'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label(tr('forms.accounts.type.label', [], null, 'dashboard'))
                            ->options([
                                'asset' => tr('forms.accounts.type.options.asset', [], null, 'dashboard'),
                                'liability' => tr('forms.accounts.type.options.liability', [], null, 'dashboard'),
                                'equity' => tr('forms.accounts.type.options.equity', [], null, 'dashboard'),
                                'revenue' => tr('forms.accounts.type.options.revenue', [], null, 'dashboard'),
                                'expense' => tr('forms.accounts.type.options.expense', [], null, 'dashboard'),
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('parent_id', null)),

                        Forms\Components\Select::make('parent_id')
                            ->label(tr('forms.accounts.parent_id.label', [], null, 'dashboard'))
                            ->relationship('parent', 'name', fn ($query, $get) =>
                                $query->where('type', $get('type'))
                            )
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText(tr('forms.accounts.parent_id.helper', [], null, 'dashboard'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $parent = Account::find($state);
                                    if ($parent) {
                                        $set('level', $parent->level + 1);
                                    }
                                } else {
                                    $set('level', 1);
                                }
                            }),

                        Forms\Components\TextInput::make('level')
                            ->label(tr('forms.accounts.level.label', [], null, 'dashboard'))
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->disabled()
                            ->helperText(tr('forms.accounts.level.helper', [], null, 'dashboard')),

                        Forms\Components\Toggle::make('is_active')
                            ->label(tr('forms.accounts.is_active.label', [], null, 'dashboard'))
                            ->default(true)
                            ->required(),

                        Forms\Components\Toggle::make('allow_manual_entry')
                            ->label(tr('forms.accounts.allow_manual_entry.label', [], null, 'dashboard'))
                            ->default(true)
                            ->helperText(tr('forms.accounts.allow_manual_entry.helper', [], null, 'dashboard')),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('forms.accounts.notes.label', [], null, 'dashboard'))
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
                Tables\Columns\TextColumn::make('code')
                    ->label(tr('tables.accounts.code', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.accounts.name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label(tr('tables.accounts.type', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state) => tr('forms.accounts.type.options.' . $state, [], null, 'dashboard'))
                    ->colors([
                        'success' => 'asset',
                        'danger' => 'liability',
                        'warning' => 'equity',
                        'info' => 'revenue',
                        'gray' => 'expense',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label(tr('tables.accounts.parent', [], null, 'dashboard'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('level')
                    ->label(tr('tables.accounts.level', [], null, 'dashboard'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.accounts.active', [], null, 'dashboard'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('allow_manual_entry')
                    ->label(tr('tables.accounts.manual_entry', [], null, 'dashboard'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(tr('tables.accounts.filters.type', [], null, 'dashboard'))
                    ->options([
                        'asset' => tr('forms.accounts.type.options.asset', [], null, 'dashboard'),
                        'liability' => tr('forms.accounts.type.options.liability', [], null, 'dashboard'),
                        'equity' => tr('forms.accounts.type.options.equity', [], null, 'dashboard'),
                        'revenue' => tr('forms.accounts.type.options.revenue', [], null, 'dashboard'),
                        'expense' => tr('forms.accounts.type.options.expense', [], null, 'dashboard'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.accounts.filters.active', [], null, 'dashboard'))
                    ->placeholder(tr('tables.accounts.filters.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.accounts.filters.active_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.accounts.filters.inactive_only', [], null, 'dashboard')),

                Tables\Filters\SelectFilter::make('parent_id')
                    ->label(tr('tables.accounts.filters.parent_account', [], null, 'dashboard'))
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('accounts.update') ?? false),
                TableDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('accounts.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('accounts.delete') ?? false),
                ]),
            ])
            ->defaultSort('code', 'asc');
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
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('accounts.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('accounts.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('accounts.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('accounts.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('accounts.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

