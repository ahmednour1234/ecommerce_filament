<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Accounting\VoucherResource\Pages;
use App\Models\Accounting\Voucher;
use App\Models\Accounting\Account;
use App\Models\Accounting\Journal;
use App\Models\Accounting\JournalEntry;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\Accounting\VoucherSignature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class VoucherResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationTranslationKey = 'menu.accounting.vouchers';



    public static function getNavigationLabel(): string
    {
        return trans_dash('menu.accounting.voucher', 'سندات');
    }

    public static function getLabel(): string
    {
        return trans_dash('menu.accounting.vouchers', 'سندات');
    }

    public static function getPluralLabel(): string
    {
        return trans_dash('menu.accounting.vouchers', 'سندات');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(trans_dash('vouchers.sections.info', 'Voucher Information'))
                ->schema([
                    Forms\Components\Select::make('type')
                        ->label(trans_dash('vouchers.fields.type', 'Voucher Type'))
                        ->options([
                            'payment' => trans_dash('vouchers.types.payment', 'Payment Voucher (سند صرف)'),
                            'receipt' => trans_dash('vouchers.types.receipt', 'Receipt Voucher (سند قبض)'),
                        ])
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $prefix = $state === 'payment' ? 'PV' : 'RV';
                            $last = Voucher::where('type', $state)->latest('id')->first();
                            $number = $last ? ((int) substr($last->voucher_number, -6)) + 1 : 1;
                            $set('voucher_number', $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT));
                        }),

                    Forms\Components\TextInput::make('voucher_number')
                        ->label(trans_dash('vouchers.fields.number', 'Voucher Number'))
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true)
                        ->helperText(trans_dash('vouchers.helpers.auto_number', 'Auto-generated voucher number')),

                    Forms\Components\DatePicker::make('voucher_date')
                        ->label(trans_dash('vouchers.fields.date', 'Voucher Date'))
                        ->required()
                        ->default(now())
                        ->displayFormat('Y-m-d'),

                    Forms\Components\TextInput::make('amount')
                        ->label(trans_dash('vouchers.fields.amount', 'Amount'))
                        ->numeric()
                        ->required()
                        ->minValue(0.01)
                        ->step(0.01),

                    Forms\Components\Select::make('account_id')
                        ->label(trans_dash('accounting.account', 'Account'))
                        ->options(Account::active()->get()->mapWithKeys(fn ($a) => [$a->id => $a->code . ' - ' . $a->name])->toArray())
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('branch_id')
                        ->label(trans_dash('accounting.branch', 'Branch'))
                        ->options(Branch::active()->pluck('name', 'id')->toArray())
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('cost_center_id')
                        ->label(trans_dash('accounting.cost_center', 'Cost Center'))
                        ->options(CostCenter::active()->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\Textarea::make('description')
                        ->label(trans_dash('vouchers.fields.description', 'Description'))
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('reference')
                        ->label(trans_dash('vouchers.fields.reference', 'Reference'))
                        ->maxLength(255)
                        ->helperText(trans_dash('vouchers.helpers.reference', 'External reference number (optional)')),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('voucher_number')
                    ->label(trans_dash('vouchers.fields.number', 'Voucher Number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label(trans_dash('vouchers.fields.type', 'Type'))
                    ->colors([
                        'danger' => 'payment',
                        'success' => 'receipt',
                    ])
                    ->formatStateUsing(fn (string $state): string =>
                        $state === 'payment'
                            ? trans_dash('vouchers.types.payment_short', 'Payment')
                            : trans_dash('vouchers.types.receipt_short', 'Receipt')
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('voucher_date')
                    ->label(trans_dash('vouchers.fields.date', 'Date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(trans_dash('vouchers.fields.amount', 'Amount'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('account.code')
                    ->label(trans_dash('tables.account.code', 'Account Code'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('account.name')
                    ->label(trans_dash('accounting.account', 'Account'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(trans_dash('accounting.branch', 'Branch'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('costCenter.name')
                    ->label(trans_dash('accounting.cost_center', 'Cost Center'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('journal_entry_id')
                    ->label(trans_dash('vouchers.fields.journal_entry', 'Journal Entry'))
                    ->boolean()
                    ->getStateUsing(fn (Voucher $record) => ! is_null($record->journal_entry_id))
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(trans_dash('filters.type', 'Type'))
                    ->options([
                        'payment' => trans_dash('vouchers.types.payment_short', 'Payment'),
                        'receipt' => trans_dash('vouchers.types.receipt_short', 'Receipt'),
                    ]),

                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(trans_dash('accounting.branch', 'Branch'))
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('account_id')
                    ->label(trans_dash('accounting.account', 'Account'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                // ✅ View Page
                Tables\Actions\ViewAction::make()
                    ->label(trans_dash('common.view', 'View'))
                    ->visible(fn () => auth()->user()?->can('vouchers.view') ?? true),

                // ✅ Print (stream)
                Tables\Actions\Action::make('print_voucher')
                    ->label(trans_dash('vouchers.actions.print_voucher', 'Print Voucher'))
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->modalHeading(trans_dash('vouchers.actions.print_voucher', 'Print Voucher'))
                    ->modalSubmitActionLabel(trans_dash('vouchers.actions.print', 'Print'))
                    ->form(fn (Voucher $record) => static::signaturePickerForm($record))
                    ->action(function (Voucher $record, array $data) {
                        $signatureIds = static::extractSignatureIdsOrFail($data);

                        $service = app(\App\Services\Accounting\VoucherPrintService::class);
                        return $service->streamPdf($record, $signatureIds); // stream for print
                    }),

                // ✅ Export PDF (download)
                Tables\Actions\Action::make('export_pdf')
                    ->label(trans_dash('vouchers.actions.export_pdf', 'Export PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->modalHeading(trans_dash('vouchers.actions.export_pdf', 'Export PDF'))
                    ->modalSubmitActionLabel(trans_dash('vouchers.actions.export', 'Export'))
                    ->form(fn (Voucher $record) => static::signaturePickerForm($record))
                    ->action(function (Voucher $record, array $data) {
                        $signatureIds = static::extractSignatureIdsOrFail($data);

                        $service = app(\App\Services\Accounting\VoucherPrintService::class);
                        return $service->downloadPdf($record, $signatureIds);
                    }),

                // ✅ Export Excel (CSV) (download)
                Tables\Actions\Action::make('export_excel')
                    ->label(trans_dash('vouchers.actions.export_excel', 'Export Excel'))
                    ->icon('heroicon-o-table-cells')
                    ->color('warning')
                    ->action(function (Voucher $record) {
                        $service = app(\App\Services\Accounting\VoucherPrintService::class);
                        return $service->downloadCsv($record);
                    }),

                Tables\Actions\Action::make('create_journal_entry')
                    ->label(trans_dash('vouchers.actions.create_journal_entry', 'Create Journal Entry'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Voucher $record) => static::createJournalEntryForVoucher($record))
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

    /**
     * ✅ Dynamic signature form (0-6 selects) filtered by voucher type
     */
    protected static function signaturePickerForm(Voucher $record): array
    {
        $voucherType = $record->type;

        $signatureOptions = VoucherSignature::query()
            ->where('is_active', true)
            ->when($voucherType, function ($q) use ($voucherType) {
                // expected values: both|payment|receipt or null
                $q->where(function ($qq) use ($voucherType) {
                    $qq->whereNull('type')
                        ->orWhere('type', 'both')
                        ->orWhere('type', $voucherType);
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($sig) => [$sig->id => ($sig->name . ($sig->title ? " - {$sig->title}" : ''))])
            ->toArray();

        $fields = [
            Forms\Components\Select::make('number_of_signatures')
                ->label(trans_dash('vouchers.signatures.choose_count', 'Number of Signatures'))
                ->options(array_combine(range(0, 6), range(0, 6)))
                ->default(0)
                ->required()
                ->live()
                ->helperText(trans_dash('vouchers.signatures.choose_count_helper', 'Select how many signatures to include (0-6)')),
        ];

        for ($i = 1; $i <= 6; $i++) {
            $fields[] = Forms\Components\Select::make("signature_{$i}")
                ->label(trans_dash("vouchers.signatures.signature_{$i}", "Signature {$i}"))
                ->options($signatureOptions)
                ->searchable()
                ->preload()
                ->visible(fn ($get) => (int) $get('number_of_signatures') >= $i)
                ->required(fn ($get) => (int) $get('number_of_signatures') >= $i);
        }

        return $fields;
    }

    protected static function extractSignatureIdsOrFail(array $data): array
    {
        $signatureIds = [];
        $count = (int) ($data['number_of_signatures'] ?? 0);

        for ($i = 1; $i <= $count; $i++) {
            $key = "signature_{$i}";
            if (! empty($data[$key])) {
                $signatureIds[] = (int) $data[$key];
            }
        }

        if (count($signatureIds) !== count(array_unique($signatureIds))) {
            throw ValidationException::withMessages([
                'signatures' => trans_dash('vouchers.signatures.no_duplicates', 'Duplicate signatures are not allowed'),
            ]);
        }

        return $signatureIds;
    }

    protected static function createJournalEntryForVoucher(Voucher $record): void
    {
        if ($record->journal_entry_id) {
            throw new \Exception('Journal entry already exists for this voucher.');
        }

        $journal = Journal::where('type', 'cash')->first() ?? Journal::where('type', 'general')->first();

        if (! $journal) {
            throw new \Exception('No suitable journal found. Please create a cash or general journal first.');
        }

        $prefix = strtoupper(substr($journal->code, 0, 3));
        $lastEntry = JournalEntry::where('journal_id', $journal->id)->latest('id')->first();
        $number = $lastEntry ? ((int) substr($lastEntry->entry_number, -6)) + 1 : 1;
        $entryNumber = $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);

        $journalEntry = JournalEntry::create([
            'journal_id' => $journal->id,
            'entry_number' => $entryNumber,
            'entry_date' => $record->voucher_date,
            'reference' => $record->voucher_number,
            'description' => ($record->type === 'payment' ? 'Payment Voucher' : 'Receipt Voucher') . ': ' . ($record->description ?? ''),
            'branch_id' => $record->branch_id,
            'cost_center_id' => $record->cost_center_id,
            'user_id' => auth()->id(),
            'is_posted' => false,
        ]);

        $cashAccount = Account::where('code', '1000')
            ->orWhere('name', 'like', '%cash%')
            ->first();

        if (! $cashAccount) {
            throw new \Exception('Cash account not found. Please create account with code 1000 or name containing "cash".');
        }

        if ($record->type === 'payment') {
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
                'description' => 'Payment for: ' . ($record->description ?? ''),
                'branch_id' => $record->branch_id,
                'cost_center_id' => $record->cost_center_id,
            ]);
        } else {
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
                'description' => 'Receipt for: ' . ($record->description ?? ''),
                'branch_id' => $record->branch_id,
                'cost_center_id' => $record->cost_center_id,
            ]);
        }

        $record->update(['journal_entry_id' => $journalEntry->id]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'view'   => Pages\ViewVoucher::route('/{record}'),        // ✅ new
            'edit'   => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('vouchers.view_any') ?? true;
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

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
