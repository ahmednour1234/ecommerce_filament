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
    protected static ?string $navigationTranslationKey = 'menu.accounting.bank_accounts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bank Account Information')
                    ->schema([
                        Forms\Components\Select::make('account_id')
                            ->label(trans_dash('accounting.account', 'Account'))
                            ->relationship('account', 'name', fn ($query) => 
                                $query->where('type', 'asset')->where('is_active', true)
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Select the account associated with this bank account')
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
                            ->label('Bank Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('account_number')
                            ->label('Account Number')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('iban')
                            ->label('IBAN')
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\TextInput::make('swift_code')
                            ->label('SWIFT Code')
                            ->maxLength(50)
                            ->nullable(),

                        Forms\Components\Select::make('branch_id')
                            ->label(trans_dash('accounting.branch', 'Branch'))
                            ->relationship('branch', 'name', fn ($query) => 
                                $query->where('is_active', true)
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('currency_id')
                            ->label(trans_dash('accounting.currency', 'Currency'))
                            ->relationship('currency', 'name', fn ($query) => 
                                $query->where('is_active', true)
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('opening_balance')
                            ->label('Opening Balance')
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),

                        Forms\Components\TextInput::make('current_balance')
                            ->label('Current Balance')
                            ->numeric()
                            ->default(0)
                            ->prefix('$')
                            ->disabled(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label(trans_dash('accounting.notes', 'Notes'))
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
                    ->label('Account Code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('account.name')
                    ->label('Account Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Bank Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('account_number')
                    ->label('Account Number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('currency.code')
                    ->label('Currency')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('current_balance')
                    ->label('Current Balance')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('currency_id')
                    ->relationship('currency', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\Action::make('reconcile')
                    ->label('Reconcile')
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

