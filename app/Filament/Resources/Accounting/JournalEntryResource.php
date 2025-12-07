<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Resources\Accounting\JournalEntryResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\Account;
use App\Models\Accounting\Journal;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use Filament\Forms;
use Filament\Forms\Form;
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
        return $form
            ->schema([
                Forms\Components\Section::make('Entry Information')
                    ->schema([
                        Forms\Components\Select::make('journal_id')
                            ->label('Journal')
                            ->relationship('journal', 'name')
                            ->options(Journal::active()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Auto-generate entry number based on journal
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
                            ->label('Entry Number')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText('Auto-generated entry number'),

                        Forms\Components\DatePicker::make('entry_date')
                            ->label('Entry Date')
                            ->required()
                            ->default(now())
                            ->displayFormat('Y-m-d'),

                        Forms\Components\TextInput::make('reference')
                            ->label('Reference')
                            ->maxLength(255)
                            ->helperText('External reference number (optional)'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->columnSpanFull(),

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

                        Forms\Components\Toggle::make('is_posted')
                            ->label('Posted')
                            ->default(false)
                            ->disabled(fn ($record) => $record && $record->is_posted)
                            ->helperText('Once posted, entry cannot be modified'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Journal Entry Lines')
                    ->schema([
                        Forms\Components\Repeater::make('lines')
                            ->relationship('lines')
                            ->schema([
                                Forms\Components\Select::make('account_id')
                                    ->label('Account')
                                    ->relationship('account', 'name', fn ($query) => 
                                        $query->where('is_active', true)->where('allow_manual_entry', true)
                                    )
                                    ->options(Account::active()->where('allow_manual_entry', true)->get()->mapWithKeys(function ($account) {
                                        return [$account->id => $account->code . ' - ' . $account->name];
                                    }))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->reactive(),

                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('debit')
                                    ->label('Debit')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        // If debit is set, clear credit
                                        if ($state > 0) {
                                            $set('credit', 0);
                                        }
                                    }),

                                Forms\Components\TextInput::make('credit')
                                    ->label('Credit')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        // If credit is set, clear debit
                                        if ($state > 0) {
                                            $set('debit', 0);
                                        }
                                    }),

                                Forms\Components\Select::make('branch_id')
                                    ->label('Branch')
                                    ->relationship('branch', 'name')
                                    ->options(Branch::active()->pluck('name', 'id'))
                                    ->default(fn ($get) => $get('../../branch_id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('cost_center_id')
                                    ->label('Cost Center')
                                    ->relationship('costCenter', 'name')
                                    ->options(CostCenter::active()->pluck('name', 'id'))
                                    ->default(fn ($get) => $get('../../cost_center_id'))
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                            ])
                            ->columns(2)
                            ->defaultItems(2)
                            ->minItems(2)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                $state['account_id'] ? Account::find($state['account_id'])?->name : null
                            )
                            ->addActionLabel('Add Line')
                            ->reorderable()
                            ->required(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Balance Summary')
                    ->schema([
                        Forms\Components\Placeholder::make('total_debits')
                            ->label('Total Debits')
                            ->content(fn ($get) => 
                                number_format(
                                    collect($get('lines') ?? [])->sum('debit') ?? 0,
                                    2
                                )
                            ),

                        Forms\Components\Placeholder::make('total_credits')
                            ->label('Total Credits')
                            ->content(fn ($get) => 
                                number_format(
                                    collect($get('lines') ?? [])->sum('credit') ?? 0,
                                    2
                                )
                            ),

                        Forms\Components\Placeholder::make('balance_difference')
                            ->label('Balance Difference')
                            ->content(function ($get) {
                                $debits = collect($get('lines') ?? [])->sum('debit') ?? 0;
                                $credits = collect($get('lines') ?? [])->sum('credit') ?? 0;
                                $diff = abs($debits - $credits);
                                $color = $diff < 0.01 ? 'text-success-600' : 'text-danger-600';
                                return '<span class="' . $color . '">' . number_format($diff, 2) . '</span>';
                            })
                            ->extraAttributes(['class' => 'text-lg font-semibold']),
                    ])
                    ->columns(3)
                    ->visible(fn ($get) => !empty($get('lines'))),
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

                Tables\Columns\IconColumn::make('is_posted')
                    ->label('Posted')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('journal_id')
                    ->label('Journal')
                    ->relationship('journal', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_posted')
                    ->label('Posted')
                    ->placeholder('All')
                    ->trueLabel('Posted only')
                    ->falseLabel('Unposted only'),

                Tables\Filters\Filter::make('entry_date')
                    ->form([
                        Forms\Components\DatePicker::make('entry_date_from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('entry_date_to')
                            ->label('To Date'),
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
                Tables\Actions\Action::make('post')
                    ->label('Post')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (JournalEntry $record) {
                        if ($record->is_posted) {
                            throw new \Exception('Entry is already posted.');
                        }
                        
                        if (!$record->isBalanced()) {
                            throw new \Exception('Entry is not balanced. Debits must equal credits.');
                        }
                        
                        $record->update([
                            'is_posted' => true,
                            'posted_at' => now(),
                        ]);
                    })
                    ->visible(fn (JournalEntry $record) => !$record->is_posted && auth()->user()?->can('journal_entries.post') ?? false),

                Tables\Actions\EditAction::make()
                    ->visible(fn (JournalEntry $record) => !$record->is_posted && (auth()->user()?->can('journal_entries.update') ?? false)),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (JournalEntry $record) => !$record->is_posted && (auth()->user()?->can('journal_entries.delete') ?? false)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('journal_entries.delete') ?? false),
                ]),
            ])
            ->defaultSort('entry_date', 'desc');
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
            'edit' => Pages\EditJournalEntry::route('/{record}/edit'),
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

