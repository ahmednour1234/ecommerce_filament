<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Resources\Accounting\BankAccountResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Accounting\BankAccount;
use App\Models\Accounting\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BankAccountResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = BankAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 15;
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.bank_accounts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('forms.bank_accounts.sections.bank_account_information', [], null, 'dashboard'))
                    ->schema([
                        Forms\Components\Select::make('account_id')
                            ->label(tr('forms.bank_accounts.account_id.label', [], null, 'dashboard'))
                            ->relationship('account', 'name', fn ($query) => 
                                $query->where('type', 'asset')->where('is_active', true)
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText(tr('forms.bank_accounts.account_id.helper', [], null, 'dashboard'))
                            ->createOptionForm([
                                Forms\Components\TextInput::make('code')
                                    ->required()
                                    ->maxLength(50)
                                    ->unique('accounts', 'code'),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $account = \App\Models\Accounting\Account::create([
                                    'code' => $data['code'],
                                    'name' => $data['name'],
                                    'type' => 'asset',
                                    'level' => 1,
                                    'is_active' => true,
                                    'allow_manual_entry' => true,
                                ]);
                                return $account->id;
                            }),

                        Forms\Components\TextInput::make('bank_name')
                            ->label(tr('forms.bank_accounts.bank_name.label', [], null, 'dashboard'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('account_number')
                            ->label(tr('forms.bank_accounts.account_number.label', [], null, 'dashboard'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('iban')
                            ->label(tr('forms.bank_accounts.iban.label', [], null, 'dashboard'))
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\TextInput::make('swift_code')
                            ->label(tr('forms.bank_accounts.swift_code.label', [], null, 'dashboard'))
                            ->maxLength(50)
                            ->nullable(),

                        Forms\Components\Select::make('branch_id')
                            ->label(tr('forms.bank_accounts.branch_id.label', [], null, 'dashboard'))
                            ->relationship('branch', 'name', fn ($query) => 
                                $query->where('status', 'active')
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('currency_id')
                            ->label(tr('forms.bank_accounts.currency_id.label', [], null, 'dashboard'))
                            ->relationship('currency', 'name', fn ($query) => 
                                $query->where('is_active', true)
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('opening_balance')
                            ->label(tr('forms.bank_accounts.opening_balance.label', [], null, 'dashboard'))
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),

                        Forms\Components\TextInput::make('current_balance')
                            ->label(tr('forms.bank_accounts.current_balance.label', [], null, 'dashboard'))
                            ->numeric()
                            ->default(0)
                            ->prefix('$')
                            ->disabled()
                            ->helperText(tr('forms.bank_accounts.current_balance.helper', [], null, 'dashboard')),

                        Forms\Components\Toggle::make('is_active')
                            ->label(tr('forms.bank_accounts.is_active.label', [], null, 'dashboard'))
                            ->default(true)
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('forms.bank_accounts.notes.label', [], null, 'dashboard'))
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
                Tables\Columns\TextColumn::make('account.code')
                    ->label(tr('tables.bank_accounts.account_code', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('account.name')
                    ->label(tr('tables.bank_accounts.account_name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bank_name')
                    ->label(tr('tables.bank_accounts.bank_name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('account_number')
                    ->label(tr('tables.bank_accounts.account_number', [], null, 'dashboard'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('tables.bank_accounts.branch', [], null, 'dashboard'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('currency.code')
                    ->label(tr('tables.bank_accounts.currency', [], null, 'dashboard'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('current_balance')
                    ->label(tr('tables.bank_accounts.current_balance', [], null, 'dashboard'))
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.bank_accounts.active', [], null, 'dashboard'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('tables.bank_accounts.filters.branch', [], null, 'dashboard'))
                    ->relationship('branch', 'name', fn ($query) => 
                        $query->where('status', 'active')
                    )
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('currency_id')
                    ->label(tr('tables.bank_accounts.filters.currency', [], null, 'dashboard'))
                    ->relationship('currency', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.bank_accounts.filters.active', [], null, 'dashboard'))
                    ->placeholder(tr('tables.bank_accounts.filters.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.bank_accounts.filters.active_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.bank_accounts.filters.inactive_only', [], null, 'dashboard')),
            ])
            ->actions([
                Tables\Actions\Action::make('reconcile')
                    ->label(tr('tables.bank_accounts.actions.reconcile', [], null, 'dashboard'))
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn () => auth()->user()?->can('bank_accounts.reconcile') ?? false)
                    ->action(function ($record) {
                        // Reconcile logic
                    }),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('bank_accounts.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('bank_accounts.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('bank_accounts.delete') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('bank_accounts.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('bank_accounts.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('bank_accounts.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('bank_accounts.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

