<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Resources\Accounting\JournalEntryResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\Account;
use App\Models\Accounting\Journal;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Period;
use App\Models\Accounting\Project;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\MainCore\Currency;
use App\Enums\Accounting\JournalEntryStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class JournalEntryResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = JournalEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        $accounts = Account::active()->where('allow_manual_entry', true)->get();
        $accountOptions = $accounts->mapWithKeys(function ($account) {
            return [$account->id => $account->code . ' - ' . $account->name];
        })->toArray();

        $currencies = Currency::active()->get();
        $currencyOptions = $currencies->mapWithKeys(function ($currency) {
            return [$currency->id => $currency->code . ' - ' . $currency->name];
        })->toArray();

        $defaultCurrency = Currency::where('is_default', true)->first();
        $defaultCurrencyId = $defaultCurrency?->id;

        return $form
            ->schema([
                Forms\Components\Section::make(trans_dash('accounting.entry_information', 'Entry Information'))
                    ->schema([
                        Forms\Components\Select::make('journal_id')
                            ->label(trans_dash('accounting.journal', 'Journal'))
                            ->options(function () {
                                try {
                                    return Journal::active()->pluck('name', 'id');
                                } catch (\Exception $e) {
                                    return [];
                                }
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $journal = Journal::find($state);
                                    if ($journal) {
                                        $prefix = strtoupper(substr($journal->code, 0, 3));
                                        $lastEntry = JournalEntry::where('journal_id', $state)
                                            ->orderBy('id', 'desc')
                                            ->first();
                                        
                                        $number = $lastEntry ? ((int) substr($lastEntry->entry_number, -6)) + 1 : 1;
                                        $entryNumber = $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
                                        $set('entry_number', $entryNumber);
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('entry_number')
                            ->label(trans_dash('accounting.entry_number', 'Entry Number'))
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText(trans_dash('accounting.auto_generated', 'Auto-generated entry number')),

                        Forms\Components\DatePicker::make('entry_date')
                            ->label(trans_dash('accounting.entry_date', 'Entry Date'))
                            ->required()
                            ->default(now())
                            ->displayFormat('Y-m-d')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $period = Period::getForDate(\Carbon\Carbon::parse($state));
                                    if ($period) {
                                        $set('period_id', $period->id);
                                        $set('fiscal_year_id', $period->fiscal_year_id);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('fiscal_year_id')
                            ->label(trans_dash('accounting.fiscal_year', 'Fiscal Year'))
                            ->options(function () {
                                try {
                                    return FiscalYear::all()->pluck('name', 'id');
                                } catch (\Exception $e) {
                                    return [];
                                }
                            })
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->nullable()
                            ->default(function () {
                                try {
                                    $fiscalYear = FiscalYear::getActive();
                                    return $fiscalYear?->id;
                                } catch (\Exception $e) {
                                    return null;
                                }
                            }),

                        Forms\Components\Select::make('period_id')
                            ->label(trans_dash('accounting.period', 'Period'))
                            ->options(function ($get) {
                                try {
                                    $fiscalYearId = $get('fiscal_year_id');
                                    if ($fiscalYearId) {
                                        return Period::where('fiscal_year_id', $fiscalYearId)
                                            ->pluck('name', 'id');
                                    }
                                    return Period::all()->pluck('name', 'id');
                                } catch (\Exception $e) {
                                    return [];
                                }
                            })
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->nullable()
                            ->default(function ($get) {
                                try {
                                    $entryDate = $get('entry_date');
                                    if ($entryDate) {
                                        $period = Period::getForDate(\Carbon\Carbon::parse($entryDate));
                                        return $period?->id;
                                    }
                                    return null;
                                } catch (\Exception $e) {
                                    return null;
                                }
                            }),

                        Forms\Components\TextInput::make('reference')
                            ->label(trans_dash('accounting.reference', 'Reference'))
                            ->maxLength(255)
                            ->helperText(trans_dash('accounting.external_reference', 'External reference number (optional)')),

                        Forms\Components\Textarea::make('description')
                            ->label(trans_dash('accounting.description', 'Description'))
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('branch_id')
                            ->label(trans_dash('accounting.branch', 'Branch'))
                            ->options(function () {
                                try {
                                    return Branch::active()->pluck('name', 'id');
                                } catch (\Exception $e) {
                                    return [];
                                }
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->user()?->branch_id),

                        Forms\Components\Select::make('cost_center_id')
                            ->label(trans_dash('accounting.cost_center', 'Cost Center'))
                            ->options(function () {
                                try {
                                    return CostCenter::active()->pluck('name', 'id');
                                } catch (\Exception $e) {
                                    return [];
                                }
                            })
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('status')
                            ->label(trans_dash('accounting.status', 'Status'))
                            ->options(collect(JournalEntryStatus::cases())->mapWithKeys(fn ($status) => [
                                $status->value => $status->label()
                            ]))
                            ->default(JournalEntryStatus::DRAFT->value)
                            ->disabled(fn ($record) => $record && $record->is_posted)
                            ->visible(fn ($record) => $record !== null),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(trans_dash('accounting.journal_entry_lines', 'Journal Entry Lines'))
                    ->schema([
                        Forms\Components\Repeater::make('lines')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label(trans_dash('accounting.type', 'Type'))
                                    ->options([
                                        'debit' => trans_dash('accounting.debit', 'Debit'),
                                        'credit' => trans_dash('accounting.credit', 'Credit'),
                                    ])
                                    ->required()
                                    ->default('debit')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // Clear amount when type changes
                                        if ($state === 'debit') {
                                            $set('credit', 0);
                                        } else {
                                            $set('debit', 0);
                                        }
                                    }),

                                Forms\Components\Select::make('account_id')
                                    ->label(trans_dash('accounting.account', 'Account'))
                                    ->options($accountOptions)
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('debit')
                                    ->label(trans_dash('accounting.debit_amount', 'Debit Amount'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->visible(fn ($get) => $get('type') === 'debit')
                                    ->required(fn ($get) => $get('type') === 'debit')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($defaultCurrencyId) {
                                        if ($state > 0) {
                                            $set('credit', 0);
                                        }
                                        $currencyId = $get('currency_id');
                                        $exchangeRate = $get('exchange_rate') ?? 1;
                                        if ($currencyId && $currencyId != $defaultCurrencyId && $exchangeRate) {
                                            $set('base_amount', round($state * $exchangeRate, 2));
                                        } else {
                                            $set('base_amount', $state);
                                        }
                                    }),

                                Forms\Components\TextInput::make('credit')
                                    ->label(trans_dash('accounting.credit_amount', 'Credit Amount'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->visible(fn ($get) => $get('type') === 'credit')
                                    ->required(fn ($get) => $get('type') === 'credit')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($defaultCurrencyId) {
                                        if ($state > 0) {
                                            $set('debit', 0);
                                        }
                                        $currencyId = $get('currency_id');
                                        $exchangeRate = $get('exchange_rate') ?? 1;
                                        if ($currencyId && $currencyId != $defaultCurrencyId && $exchangeRate) {
                                            $set('base_amount', round($state * $exchangeRate, 2));
                                        } else {
                                            $set('base_amount', $state);
                                        }
                                    }),

                                Forms\Components\Select::make('currency_id')
                                    ->label(trans_dash('accounting.currency', 'Currency'))
                                    ->options($currencyOptions)
                                    ->default($defaultCurrencyId)
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($defaultCurrencyId) {
                                        if ($state == $defaultCurrencyId) {
                                            $set('exchange_rate', 1);
                                        }
                                        $amount = $get('debit') > 0 ? $get('debit') : $get('credit');
                                        $exchangeRate = $get('exchange_rate') ?? 1;
                                        if ($state && $state != $defaultCurrencyId && $exchangeRate) {
                                            $set('base_amount', round($amount * $exchangeRate, 2));
                                        } else {
                                            $set('base_amount', $amount);
                                        }
                                    }),

                                Forms\Components\TextInput::make('exchange_rate')
                                    ->label(trans_dash('accounting.exchange_rate', 'Exchange Rate'))
                                    ->numeric()
                                    ->default(1)
                                    ->step(0.000001)
                                    ->visible(fn ($get) => $get('currency_id') && $get('currency_id') != $defaultCurrencyId)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($defaultCurrencyId) {
                                        $amount = $get('debit') > 0 ? $get('debit') : $get('credit');
                                        $currencyId = $get('currency_id');
                                        if ($currencyId && $currencyId != $defaultCurrencyId && $state) {
                                            $set('base_amount', round($amount * $state, 2));
                                        } else {
                                            $set('base_amount', $amount);
                                        }
                                    }),

                                Forms\Components\TextInput::make('base_amount')
                                    ->label(trans_dash('accounting.base_amount', 'Base Amount'))
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(0)
                                    ->reactive(),

                                Forms\Components\Textarea::make('description')
                                    ->label(trans_dash('accounting.description', 'Description'))
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('cost_center_id')
                                    ->label(trans_dash('accounting.cost_center', 'Cost Center'))
                                    ->options(CostCenter::active()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                Forms\Components\Select::make('project_id')
                                    ->label(trans_dash('accounting.project', 'Project'))
                                    ->options(Project::where('is_active', true)->get()->mapWithKeys(fn ($p) => [
                                        $p->id => $p->code . ' - ' . $p->name
                                    ]))
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                Forms\Components\TextInput::make('reference')
                                    ->label(trans_dash('accounting.reference', 'Reference'))
                                    ->maxLength(255),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->itemLabel(fn (array $state): ?string => 
                                ($state['account_id'] ? (Account::find($state['account_id'])?->code . ' - ' . Account::find($state['account_id'])?->name) : null) . 
                                ' (' . ($state['type'] ?? 'debit') . ': ' . 
                                number_format((float) ($state['debit'] ?? $state['credit'] ?? 0), 2) . ')'
                            )
                            ->collapsible()
                            ->reorderable()
                            ->addActionLabel(trans_dash('accounting.add_line', 'Add Line'))
                            ->minItems(2)
                            ->helperText(trans_dash('accounting.minimum_two_lines', 'At least two lines are required. Total debits must equal total credits.')),

                        Forms\Components\Placeholder::make('balance_summary')
                            ->label('')
                            ->content(function ($get) {
                                $lines = $get('lines') ?? [];
                                $totalDebits = 0;
                                $totalCredits = 0;

                                foreach ($lines as $line) {
                                    $baseAmount = (float) ($line['base_amount'] ?? 0);
                                    if ($line['type'] === 'debit' || ($line['debit'] ?? 0) > 0) {
                                        $totalDebits += $baseAmount > 0 ? $baseAmount : (float) ($line['debit'] ?? 0);
                                    } else {
                                        $totalCredits += $baseAmount > 0 ? $baseAmount : (float) ($line['credit'] ?? 0);
                                    }
                                }

                                $difference = abs($totalDebits - $totalCredits);
                                $isBalanced = $difference < 0.01;

                                $defaultCurrency = Currency::where('is_default', true)->first();
                                $currencyCode = $defaultCurrency?->code ?? 'USD';

                                return view('filament.forms.components.journal-entry-summary', [
                                    'totalDebits' => $totalDebits,
                                    'totalCredits' => $totalCredits,
                                    'difference' => $difference,
                                    'isBalanced' => $isBalanced,
                                    'currencyCode' => $currencyCode,
                                ]);
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entry_number')
                    ->label('Entry Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('journal.name')
                    ->label('Journal')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('entry_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
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

                Tables\Columns\TextColumn::make('total_debits')
                    ->label('Total Debits')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_credits')
                    ->label('Total Credits')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(trans_dash('accounting.status', 'Status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? JournalEntryStatus::from($state)->label() : 'Draft')
                    ->color(fn ($state) => $state ? JournalEntryStatus::from($state)->color() : 'gray')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_posted')
                    ->label(trans_dash('accounting.posted', 'Posted'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(trans_dash('accounting.created_by', 'Created By'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(trans_dash('accounting.status', 'Status'))
                    ->options(collect(JournalEntryStatus::cases())->mapWithKeys(fn ($status) => [
                        $status->value => $status->label()
                    ])),

                Tables\Filters\SelectFilter::make('journal_id')
                    ->label(trans_dash('accounting.journal', 'Journal'))
                    ->relationship('journal', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(trans_dash('accounting.branch', 'Branch'))
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('period_id')
                    ->label(trans_dash('accounting.period', 'Period'))
                    ->relationship('period', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_posted')
                    ->label(trans_dash('accounting.posted', 'Posted'))
                    ->placeholder(trans_dash('accounting.all', 'All'))
                    ->trueLabel(trans_dash('accounting.posted_only', 'Posted only'))
                    ->falseLabel(trans_dash('accounting.unposted_only', 'Unposted only')),

                Tables\Filters\Filter::make('entry_date')
                    ->form([
                        Forms\Components\DatePicker::make('entry_date_from')
                            ->label(trans_dash('accounting.from_date', 'From Date')),
                        Forms\Components\DatePicker::make('entry_date_to')
                            ->label(trans_dash('accounting.to_date', 'To Date')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['entry_date_from'],
                                fn ($query, $date) => $query->whereDate('entry_date', '>=', $date),
                            )
                            ->when(
                                $data['entry_date_to'],
                                fn ($query, $date) => $query->whereDate('entry_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('submit')
                    ->label(trans_dash('accounting.submit_for_approval', 'Submit for Approval'))
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (JournalEntry $record) {
                        $status = JournalEntryStatus::from($record->status ?? JournalEntryStatus::DRAFT->value);
                        if (!$status->canBeSubmitted()) {
                            throw new \Exception(trans_dash('accounting.cannot_submit', 'Entry cannot be submitted in current status.'));
                        }
                        
                        if (!$record->isBalanced()) {
                            throw new \Exception(trans_dash('accounting.entries_not_balanced', 'Entry is not balanced. Debits must equal credits.'));
                        }
                        
                        $record->update([
                            'status' => JournalEntryStatus::PENDING_APPROVAL->value,
                        ]);
                    })
                    ->visible(fn (JournalEntry $record) => 
                        JournalEntryStatus::from($record->status ?? JournalEntryStatus::DRAFT->value)->canBeSubmitted() &&
                        (auth()->user()?->can('journal_entries.submit') ?? false)
                    ),

                Tables\Actions\Action::make('approve')
                    ->label(trans_dash('accounting.approve', 'Approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label(trans_dash('accounting.notes', 'Notes'))
                            ->rows(3),
                    ])
                    ->action(function (JournalEntry $record, array $data) {
                        $status = JournalEntryStatus::from($record->status ?? JournalEntryStatus::DRAFT->value);
                        if (!$status->canBeApproved()) {
                            throw new \Exception(trans_dash('accounting.cannot_approve', 'Entry cannot be approved in current status.'));
                        }
                        
                        $record->update([
                            'status' => JournalEntryStatus::APPROVED->value,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    })
                    ->visible(fn (JournalEntry $record) => 
                        JournalEntryStatus::from($record->status ?? JournalEntryStatus::DRAFT->value)->canBeApproved() &&
                        (auth()->user()?->can('journal_entries.approve') ?? false)
                    ),

                Tables\Actions\Action::make('reject')
                    ->label(trans_dash('accounting.reject', 'Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label(trans_dash('accounting.rejection_reason', 'Rejection Reason'))
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (JournalEntry $record, array $data) {
                        $status = JournalEntryStatus::from($record->status ?? JournalEntryStatus::DRAFT->value);
                        if (!$status->canBeApproved()) {
                            throw new \Exception(trans_dash('accounting.cannot_reject', 'Entry cannot be rejected in current status.'));
                        }
                        
                        $record->update([
                            'status' => JournalEntryStatus::REJECTED->value,
                            'rejected_by' => auth()->id(),
                            'rejected_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                    })
                    ->visible(fn (JournalEntry $record) => 
                        JournalEntryStatus::from($record->status ?? JournalEntryStatus::DRAFT->value)->canBeApproved() &&
                        (auth()->user()?->can('journal_entries.reject') ?? false)
                    ),

                Tables\Actions\Action::make('post')
                    ->label(trans_dash('accounting.post', 'Post'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (JournalEntry $record) {
                        $status = JournalEntryStatus::from($record->status ?? JournalEntryStatus::DRAFT->value);
                        if (!$status->canBePosted()) {
                            throw new \Exception(trans_dash('accounting.cannot_post', 'Entry must be approved before posting.'));
                        }
                        
                        if ($record->is_posted) {
                            throw new \Exception(trans_dash('accounting.already_posted', 'Entry is already posted.'));
                        }
                        
                        if (!$record->isBalanced()) {
                            throw new \Exception(trans_dash('accounting.entries_not_balanced', 'Entry is not balanced. Debits must equal credits.'));
                        }
                        
                        $record->update([
                            'is_posted' => true,
                            'posted_at' => now(),
                            'status' => JournalEntryStatus::POSTED->value,
                        ]);
                    })
                    ->visible(fn (JournalEntry $record) => 
                        !$record->is_posted && 
                        JournalEntryStatus::from($record->status ?? JournalEntryStatus::DRAFT->value)->canBePosted() &&
                        (auth()->user()?->can('journal_entries.post') ?? false)
                    ),

                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(fn (JournalEntry $record) => 
                        !$record->is_posted && 
                        JournalEntryStatus::from($record->status ?? JournalEntryStatus::DRAFT->value)->canBeEdited() &&
                        (auth()->user()?->can('journal_entries.update') ?? false)
                    ),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (JournalEntry $record) => 
                        !$record->is_posted && 
                        JournalEntryStatus::from($record->status ?? JournalEntryStatus::DRAFT->value)->canBeDeleted() &&
                        (auth()->user()?->can('journal_entries.delete') ?? false)
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('journal_entries.delete') ?? false),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                // Eager load relationships to prevent N+1 queries
                return $query->with([
                    'journal',
                    'branch',
                    'costCenter',
                    'user',
                    'fiscalYear',
                    'period',
                ]);
            })
            ->defaultSort('entry_date', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(trans_dash('accounting.entry_information', 'Entry Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('entry_number')
                            ->label(trans_dash('accounting.entry_number', 'Entry Number'))
                            ->badge()
                            ->color('primary'),

                        Infolists\Components\TextEntry::make('journal.name')
                            ->label(trans_dash('accounting.journal', 'Journal')),

                        Infolists\Components\TextEntry::make('entry_date')
                            ->label(trans_dash('accounting.entry_date', 'Entry Date'))
                            ->date(),

                        Infolists\Components\TextEntry::make('fiscal_year.name')
                            ->label(trans_dash('accounting.fiscal_year', 'Fiscal Year'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('period.name')
                            ->label(trans_dash('accounting.period', 'Period'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('reference')
                            ->label(trans_dash('accounting.reference', 'Reference'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('description')
                            ->label(trans_dash('accounting.description', 'Description'))
                            ->placeholder('-')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('branch.name')
                            ->label(trans_dash('accounting.branch', 'Branch')),

                        Infolists\Components\TextEntry::make('cost_center.name')
                            ->label(trans_dash('accounting.cost_center', 'Cost Center'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('status')
                            ->label(trans_dash('accounting.status', 'Status'))
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? JournalEntryStatus::from($state)->label() : 'Draft')
                            ->color(fn ($state) => $state ? JournalEntryStatus::from($state)->color() : 'gray'),

                        Infolists\Components\IconEntry::make('is_posted')
                            ->label(trans_dash('accounting.posted', 'Posted'))
                            ->boolean(),

                        Infolists\Components\TextEntry::make('user.name')
                            ->label(trans_dash('accounting.created_by', 'Created By')),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label(trans_dash('accounting.created_at', 'Created At'))
                            ->dateTime(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(trans_dash('accounting.journal_entry_lines', 'Journal Entry Lines'))
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('lines')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('account.code')
                                    ->label(trans_dash('accounting.account', 'Account'))
                                    ->formatStateUsing(fn ($state, $record) => 
                                        ($record->account->code ?? '') . ' - ' . ($record->account->name ?? '')
                                    ),

                                Infolists\Components\TextEntry::make('description')
                                    ->label(trans_dash('accounting.description', 'Description'))
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('debit')
                                    ->label(trans_dash('accounting.debit', 'Debit'))
                                    ->money('USD')
                                    ->default(0)
                                    ->visible(fn ($record) => $record->debit > 0),

                                Infolists\Components\TextEntry::make('credit')
                                    ->label(trans_dash('accounting.credit', 'Credit'))
                                    ->money('USD')
                                    ->default(0)
                                    ->visible(fn ($record) => $record->credit > 0),

                                Infolists\Components\TextEntry::make('base_amount')
                                    ->label(trans_dash('accounting.base_amount', 'Base Amount'))
                                    ->money('USD')
                                    ->default(0),

                                Infolists\Components\TextEntry::make('currency.code')
                                    ->label(trans_dash('accounting.currency', 'Currency'))
                                    ->placeholder('-')
                                    ->visible(fn ($record) => $record->currency_id !== null),

                                Infolists\Components\TextEntry::make('exchange_rate')
                                    ->label(trans_dash('accounting.exchange_rate', 'Exchange Rate'))
                                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 6) : '1.000000')
                                    ->visible(fn ($record) => $record->currency_id !== null),

                                Infolists\Components\TextEntry::make('cost_center.name')
                                    ->label(trans_dash('accounting.cost_center', 'Cost Center'))
                                    ->placeholder('-')
                                    ->visible(fn ($record) => $record->cost_center_id !== null),

                                Infolists\Components\TextEntry::make('project.code')
                                    ->label(trans_dash('accounting.project', 'Project'))
                                    ->formatStateUsing(fn ($state, $record) => 
                                        $record->project ? ($record->project->code . ' - ' . $record->project->name) : '-'
                                    )
                                    ->placeholder('-')
                                    ->visible(fn ($record) => $record->project_id !== null),

                                Infolists\Components\TextEntry::make('reference')
                                    ->label(trans_dash('accounting.reference', 'Reference'))
                                    ->placeholder('-')
                                    ->visible(fn ($record) => $record->reference !== null),
                            ])
                            ->columns(4)
                            ->grid(2),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make(trans_dash('accounting.summary', 'Summary'))
                    ->schema([
                        Infolists\Components\TextEntry::make('total_debits')
                            ->label(trans_dash('accounting.total_debits', 'Total Debits'))
                            ->money('USD')
                            ->color('success')
                            ->size('lg'),

                        Infolists\Components\TextEntry::make('total_credits')
                            ->label(trans_dash('accounting.total_credits', 'Total Credits'))
                            ->money('USD')
                            ->color('danger')
                            ->size('lg'),

                        Infolists\Components\TextEntry::make('balance_difference')
                            ->label(trans_dash('accounting.difference', 'Difference'))
                            ->money('USD')
                            ->size('lg')
                            ->formatStateUsing(function ($state, $record) {
                                $difference = abs($record->total_debits - $record->total_credits);
                                return $difference < 0.01 ? 
                                    trans_dash('accounting.balanced', 'Balanced') : 
                                    number_format($difference, 2);
                            })
                            ->color(function ($record) {
                                $difference = abs($record->total_debits - $record->total_credits);
                                return $difference < 0.01 ? 'success' : 'danger';
                            })
                            ->state(function ($record) {
                                return abs($record->total_debits - $record->total_credits);
                            }),
                    ])
                    ->columns(3),
            ]);
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
            'index' => Pages\ListJournalEntries::route('/'),
            'create' => Pages\CreateJournalEntry::route('/create'),
            'view' => Pages\ViewJournalEntry::route('/{record}'),
            'edit' => Pages\EditJournalEntry::route('/{record}/edit'),
            'print' => Pages\PrintJournalEntry::route('/{record}/print'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('journal_entries.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('journal_entries.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        if ($record->is_posted) {
            return false; // Posted entries cannot be edited
        }
        return auth()->user()?->can('journal_entries.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        if ($record->is_posted) {
            return false; // Posted entries cannot be deleted
        }
        return auth()->user()?->can('journal_entries.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('journal_entries.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

