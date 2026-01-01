<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Resources\Accounting\VoucherResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Accounting\Voucher;
use App\Models\Accounting\Account;
use App\Models\Accounting\Journal;
use App\Models\Accounting\JournalEntry;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VoucherResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationTranslationKey = 'menu.accounting.vouchers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Voucher Information')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Voucher Type')
                            ->options([
                                'payment' => 'Payment Voucher (سند صرف)',
                                'receipt' => 'Receipt Voucher (سند قبض)',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Auto-generate voucher number
                                $prefix = $state === 'payment' ? 'PV' : 'RV';
                                $lastVoucher = Voucher::where('type', $state)
                                    ->orderBy('id', 'desc')
                                    ->first();
                                
                                $number = $lastVoucher ? ((int) substr($lastVoucher->voucher_number, -6)) + 1 : 1;
                                $voucherNumber = $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
                                $set('voucher_number', $voucherNumber);
                            }),

                        Forms\Components\TextInput::make('voucher_number')
                            ->label('Voucher Number')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText('Auto-generated voucher number'),

                        Forms\Components\DatePicker::make('voucher_date')
                            ->label('Voucher Date')
                            ->required()
                            ->default(now())
                            ->displayFormat('Y-m-d'),

                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->prefix('$'),

                        Forms\Components\Select::make('account_id')
                            ->label('Account')
                            ->relationship('account', 'name', fn ($query) => 
                                $query->where('is_active', true)
                            )
                            ->options(Account::active()->get()->mapWithKeys(function ($account) {
                                return [$account->id => $account->code . ' - ' . $account->name];
                            }))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('branch_id')
                            ->label('Branch')
                            ->relationship('branch', 'name')
                            ->options(Branch::active()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('cost_center_id')
                            ->label('Cost Center')
                            ->relationship('costCenter', 'name')
                            ->options(CostCenter::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('reference')
                            ->label('Reference')
                            ->maxLength(255)
                            ->helperText('External reference number (optional)'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('voucher_number')
                    ->label('Voucher Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'danger' => 'payment',
                        'success' => 'receipt',
                    ])
                    ->formatStateUsing(fn (string $state): string => 
                        $state === 'payment' ? 'Payment' : 'Receipt'
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('voucher_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('account.code')
                    ->label('Account Code')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('account.name')
                    ->label('Account')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cost_center.name')
                    ->label('Cost Center')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('journal_entry_id')
                    ->label('Journal Entry')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn (Voucher $record) => !is_null($record->journal_entry_id))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'payment' => 'Payment Voucher',
                        'receipt' => 'Receipt Voucher',
                    ]),

                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('account_id')
                    ->label('Account')
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('voucher_date')
                    ->form([
                        Forms\Components\DatePicker::make('voucher_date_from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('voucher_date_to')
                            ->label('To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['voucher_date_from'],
                                fn ($query, $date) => $query->whereDate('voucher_date', '>=', $date),
                            )
                            ->when(
                                $data['voucher_date_to'],
                                fn ($query, $date) => $query->whereDate('voucher_date', '<=', $date),
                            );
                    }),

                Tables\Filters\TernaryFilter::make('journal_entry_id')
                    ->label('Has Journal Entry')
                    ->placeholder('All')
                    ->trueLabel('With Journal Entry')
                    ->falseLabel('Without Journal Entry')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('journal_entry_id'),
                        false: fn ($query) => $query->whereNull('journal_entry_id'),
                        blank: fn ($query) => $query,
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('create_journal_entry')
                    ->label('Create Journal Entry')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Voucher $record) {
                        if ($record->journal_entry_id) {
                            throw new \Exception('Journal entry already exists for this voucher.');
                        }

                        // Create journal entry
                        $journal = Journal::where('type', $record->type === 'payment' ? 'cash' : 'cash')->first();
                        if (!$journal) {
                            $journal = Journal::where('type', 'general')->first();
                        }

                        if (!$journal) {
                            throw new \Exception('No suitable journal found. Please create a cash or general journal first.');
                        }

                        $prefix = strtoupper(substr($journal->code, 0, 3));
                        $lastEntry = JournalEntry::where('journal_id', $journal->id)
                            ->orderBy('id', 'desc')
                            ->first();
                        
                        $number = $lastEntry ? ((int) substr($lastEntry->entry_number, -6)) + 1 : 1;
                        $entryNumber = $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);

                        $journalEntry = JournalEntry::create([
                            'journal_id' => $journal->id,
                            'entry_number' => $entryNumber,
                            'entry_date' => $record->voucher_date,
                            'reference' => $record->voucher_number,
                            'description' => ($record->type === 'payment' ? 'Payment Voucher' : 'Receipt Voucher') . ': ' . $record->description,
                            'branch_id' => $record->branch_id,
                            'cost_center_id' => $record->cost_center_id,
                            'user_id' => auth()->id(),
                            'is_posted' => false,
                        ]);

                        // Create journal entry lines
                        if ($record->type === 'payment') {
                            // Payment: Debit expense/asset account, Credit cash/bank
                            $cashAccount = Account::where('code', '1000')->orWhere('name', 'like', '%cash%')->first();
                            if (!$cashAccount) {
                                throw new \Exception('Cash account not found. Please create account with code 1000 or name containing "cash".');
                            }

                            $journalEntry->lines()->create([
                                'account_id' => $record->account_id,
                                'debit' => $record->amount,
                                'credit' => 0,
                                'description' => $record->description,
                                'branch_id' => $record->branch_id,
                                'cost_center_id' => $record->cost_center_id,
                            ]);

                            $journalEntry->lines()->create([
                                'account_id' => $cashAccount->id,
                                'debit' => 0,
                                'credit' => $record->amount,
                                'description' => 'Payment for: ' . $record->description,
                                'branch_id' => $record->branch_id,
                                'cost_center_id' => $record->cost_center_id,
                            ]);
                        } else {
                            // Receipt: Debit cash/bank, Credit revenue/liability account
                            $cashAccount = Account::where('code', '1000')->orWhere('name', 'like', '%cash%')->first();
                            if (!$cashAccount) {
                                throw new \Exception('Cash account not found. Please create account with code 1000 or name containing "cash".');
                            }

                            $journalEntry->lines()->create([
                                'account_id' => $cashAccount->id,
                                'debit' => $record->amount,
                                'credit' => 0,
                                'description' => $record->description,
                                'branch_id' => $record->branch_id,
                                'cost_center_id' => $record->cost_center_id,
                            ]);

                            $journalEntry->lines()->create([
                                'account_id' => $record->account_id,
                                'debit' => 0,
                                'credit' => $record->amount,
                                'description' => 'Receipt for: ' . $record->description,
                                'branch_id' => $record->branch_id,
                                'cost_center_id' => $record->cost_center_id,
                            ]);
                        }

                        $record->update(['journal_entry_id' => $journalEntry->id]);
                    })
                    ->visible(fn (Voucher $record) => is_null($record->journal_entry_id) && (auth()->user()?->can('vouchers.create_journal_entry') ?? false)),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('vouchers.update') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('vouchers.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('vouchers.delete') ?? false),
                ]),
            ])
            ->defaultSort('voucher_date', 'desc');
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
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('vouchers.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('vouchers.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('vouchers.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('vouchers.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('vouchers.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

