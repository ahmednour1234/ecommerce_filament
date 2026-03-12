<?php

namespace App\Filament\Resources\Finance;

use App\Filament\Concerns\FinanceModuleGate;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Finance\BranchTransactionResource\Pages;
use App\Models\Finance\BranchTransaction;
use App\Models\Finance\FinanceType;
use App\Services\MainCore\CurrencyService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class BranchTransactionResource extends Resource
{
    use TranslatableNavigation, FinanceModuleGate;

    protected static ?string $model = BranchTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'قسم الحسابات';
    protected static ?string $navigationLabel = 'الإيرادات والمصروفات';
    protected static ?int $navigationSort = 1;


    public static function getModelLabel(): string
    {
        return tr('models.branch_transaction', [], null, 'dashboard') ?: 'Branch Transaction';
    }

    public static function getPluralModelLabel(): string
    {
        return tr('models.branch_transactions', [], null, 'dashboard') ?: 'Branch Transactions';
    }

    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery()->with(['branch', 'country', 'currency', 'financeType', 'creator']);

        $user = auth()->user();

        if ($user && !$user->hasRole('super_admin') && !$user->can('finance.view_all_branches')) {
            $branchIds = $user->branches()->pluck('branches.id')->toArray();

            if (!empty($branchIds)) {
                $q->whereIn('branch_id', $branchIds);
            } else {
                $q->whereRaw('1 = 0');
            }
        }

        return $q;
    }

    public static function form(Form $form): Form
    {
        $defaultCurrency = app(CurrencyService::class)->defaultCurrency();
        $defaultCurrencyId = $defaultCurrency?->id;

        $user = auth()->user();
        $userBranches = $user?->branches()->pluck('branches.id')->toArray() ?? [];
        $canViewAllBranches = (bool) ($user?->hasRole('super_admin') || $user?->can('finance.view_all_branches'));

        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Select::make('kind_filter')
                        ->label(tr('forms.branch_transactions.kind_filter', [], null, 'dashboard') ?: 'Filter by Kind')
                        ->options([
                            'income' => tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income',
                            'expense' => tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense',
                        ])
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('finance_type_id', null))
                        ->dehydrated(false),

                    Forms\Components\Select::make('finance_type_id')
                        ->label(tr('forms.branch_transactions.finance_type_id', [], null, 'dashboard') ?: 'Type')
                        ->options(function (callable $get) {
                            $query = FinanceType::query()->where('is_active', true);

                            $kind = $get('kind_filter');
                            if ($kind) {
                                $query->where('kind', $kind);
                            }

                            return $query->get()->pluck('name_text', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive(),

                    Forms\Components\DatePicker::make('trx_date')
                        ->label(tr('forms.branch_transactions.trx_date', [], null, 'dashboard') ?: 'Transaction Date')
                        ->required()
                        ->default(now()),

                    Forms\Components\Select::make('branch_id')
                        ->label(tr('forms.branch_transactions.branch_id', [], null, 'dashboard') ?: 'Branch')
                        ->relationship('branch', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabled(fn () => !$canViewAllBranches)
                        ->default(fn () => !$canViewAllBranches && !empty($userBranches) ? $userBranches[0] : null)
                        ->visible(fn () => $canViewAllBranches || !empty($userBranches)),

                    Forms\Components\Select::make('country_id')
                        ->label(tr('forms.branch_transactions.country_id', [], null, 'dashboard') ?: 'Country')
                        ->relationship('country', 'name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name_text ?? $record->name['en'] ?? '')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\Select::make('currency_id')
                        ->label(tr('forms.branch_transactions.currency_id', [], null, 'dashboard') ?: 'Currency')
                        ->relationship('currency', 'code')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->default($defaultCurrencyId),

                    Forms\Components\TextInput::make('amount')
                        ->label(tr('forms.branch_transactions.amount', [], null, 'dashboard') ?: 'Amount')
                        ->numeric()
                        ->required()
                        ->step(0.01),

                    Forms\Components\TextInput::make('payment_method')
                        ->label(tr('forms.branch_transactions.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                        ->maxLength(50)
                        ->nullable(),

                    Forms\Components\TextInput::make('recipient_name')
                        ->label(tr('forms.branch_transactions.recipient_name', [], null, 'dashboard') ?: 'Recipient Name')
                        ->maxLength(150)
                        ->nullable(),

                    Forms\Components\TextInput::make('reference_no')
                        ->label(tr('forms.branch_transactions.reference_no', [], null, 'dashboard') ?: 'Reference No')
                        ->maxLength(100)
                        ->nullable(),

                    Forms\Components\FileUpload::make('attachment_path')
                        ->label(tr('forms.branch_transactions.attachment_path', [], null, 'dashboard') ?: 'Attachment')
                        ->disk('public')
                        ->directory('finance/transactions')
                        ->openable()
                        ->downloadable()
                        ->preserveFilenames()
                        ->maxSize(8192)
                        ->nullable(),

                    Forms\Components\Textarea::make('notes')
                        ->label(tr('forms.branch_transactions.notes', [], null, 'dashboard') ?: 'Notes')
                        ->rows(3)
                        ->columnSpanFull()
                        ->nullable(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trx_date')
                    ->label(tr('tables.branch_transactions.trx_date', [], null, 'dashboard') ?: 'Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('tables.branch_transactions.branch', [], null, 'dashboard') ?: 'Branch')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('financeType.kind')
                    ->label(tr('tables.branch_transactions.kind', [], null, 'dashboard') ?: 'Kind')
                    ->badge()
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'income'
                        ? (tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income')
                        : (tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('financeType.name_text')
                    ->label(tr('tables.branch_transactions.type', [], null, 'dashboard') ?: 'Type')
                    ->getStateUsing(fn ($record) => $record->financeType?->name_text)
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('tables.branch_transactions.amount', [], null, 'dashboard') ?: 'Amount')
                    ->formatStateUsing(fn ($state, $record) => number_format((float) $state, 2) . ' ' . ($record->currency?->code ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency.code')
                    ->label(tr('tables.branch_transactions.currency', [], null, 'dashboard') ?: 'Currency')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reference_no')
                    ->label(tr('tables.branch_transactions.reference_no', [], null, 'dashboard') ?: 'Reference')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label(tr('tables.branch_transactions.recipient_name', [], null, 'dashboard') ?: 'Recipient')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(tr('tables.branch_transactions.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('tables.branch_transactions.status', [], null, 'dashboard') ?: 'Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => tr('fields.status_' . $state, [], null, 'dashboard') ?: ucfirst((string) $state))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('tables.branch_transactions.filters.branch', [], null, 'dashboard') ?: 'Branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('kind')
                    ->label(tr('tables.branch_transactions.filters.kind', [], null, 'dashboard') ?: 'Kind')
                    ->options([
                        'income' => tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income',
                        'expense' => tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;

                        if ($value) {
                            $query->whereHas('financeType', fn ($q) => $q->where('kind', $value));
                        }

                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('finance_type_id')
                    ->label(tr('tables.branch_transactions.filters.type', [], null, 'dashboard') ?: 'Type')
                    ->options(FinanceType::where('is_active', true)->get()->pluck('name_text', 'id'))
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('currency_id')
                    ->label(tr('tables.branch_transactions.filters.currency', [], null, 'dashboard') ?: 'Currency')
                    ->relationship('currency', 'code')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('trx_date')
                    ->label(tr('tables.branch_transactions.filters.date_range', [], null, 'dashboard') ?: 'Date Range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(tr('reports.filters.from', [], null, 'dashboard') ?: 'From'),
                        Forms\Components\DatePicker::make('to')
                            ->label(tr('reports.filters.to', [], null, 'dashboard') ?: 'To'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('trx_date', '>=', $date))
                            ->when($data['to'] ?? null, fn (Builder $q, $date) => $q->whereDate('trx_date', '<=', $date));
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.branch_transactions.filters.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'pending' => tr('fields.status_pending', [], null, 'dashboard') ?: 'Pending',
                        'approved' => tr('fields.status_approved', [], null, 'dashboard') ?: 'Approved',
                        'rejected' => tr('fields.status_rejected', [], null, 'dashboard') ?: 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(tr('actions.approve', [], null, 'dashboard') ?: 'Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label(tr('forms.branch_transactions.approval_notes', [], null, 'dashboard') ?: 'Approval Notes')
                            ->rows(3)
                            ->nullable(),
                    ])
                    ->action(function (BranchTransaction $record, array $data) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title(tr('notifications.approved', [], null, 'dashboard') ?: 'Transaction approved')
                            ->send();
                    })
                    ->visible(fn (BranchTransaction $record) => $record->status === 'pending'
                        && (auth()->user()?->hasRole('super_admin') || (auth()->user()?->can('finance.approve_transactions') ?? false))),

                Tables\Actions\Action::make('reject')
                    ->label(tr('actions.reject', [], null, 'dashboard') ?: 'Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label(tr('forms.branch_transactions.rejection_reason', [], null, 'dashboard') ?: 'Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (BranchTransaction $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejected_by' => auth()->id(),
                            'rejected_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title(tr('notifications.rejected', [], null, 'dashboard') ?: 'Transaction rejected')
                            ->send();
                    })
                    ->visible(fn (BranchTransaction $record) => $record->status === 'pending'
                        && (auth()->user()?->hasRole('super_admin') || (auth()->user()?->can('finance.reject_transactions') ?? false))),

                EditAction::make()
                    ->visible(fn (BranchTransaction $record) =>
                        (auth()->user()?->hasRole('super_admin') || (auth()->user()?->can('finance.update_transactions') ?? false))),

                TableDeleteAction::make()
                    ->visible(fn (BranchTransaction $record) =>
                        (auth()->user()?->hasRole('super_admin') || (auth()->user()?->can('finance.delete_transactions') ?? false))),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label(tr('actions.approve_all', [], null, 'dashboard') ?: 'Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('notes')
                                ->label(tr('forms.branch_transactions.approval_notes', [], null, 'dashboard') ?: 'Approval Notes')
                                ->rows(3)
                                ->nullable(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $records->each(function (BranchTransaction $record) use ($data) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'approved',
                                        'approved_by' => auth()->id(),
                                        'approved_at' => now(),
                                    ]);
                                }
                            });

                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title(tr('notifications.approved_all', [], null, 'dashboard') ?: 'Transactions approved')
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn () => auth()->user()?->hasRole('super_admin') || (auth()->user()?->can('finance.approve_transactions') ?? false)),

                    Tables\Actions\BulkAction::make('reject')
                        ->label(tr('actions.reject_all', [], null, 'dashboard') ?: 'Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label(tr('forms.branch_transactions.rejection_reason', [], null, 'dashboard') ?: 'Rejection Reason')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $records->each(function (BranchTransaction $record) use ($data) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'rejected',
                                        'rejected_by' => auth()->id(),
                                        'rejected_at' => now(),
                                        'rejection_reason' => $data['rejection_reason'],
                                    ]);
                                }
                            });

                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title(tr('notifications.rejected_all', [], null, 'dashboard') ?: 'Transactions rejected')
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn () => auth()->user()?->hasRole('super_admin') || (auth()->user()?->can('finance.reject_transactions') ?? false)),

                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('super_admin') || (auth()->user()?->can('finance.delete_transactions') ?? false)),
                ]),
            ])
            ->defaultSort('trx_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranchTransactions::route('/'),
            'create' => Pages\CreateBranchTransaction::route('/create'),
            'edit' => Pages\EditBranchTransaction::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return (bool) ($user?->hasRole('super_admin') || ($user?->can('finance.view_transactions') ?? false));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return (bool) ($user?->hasRole('super_admin') || ($user?->can('finance.create_transactions') ?? false));
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return (bool) ($user?->hasRole('super_admin') || ($user?->can('finance.update_transactions') ?? false));
    }

    public static function canDelete(mixed $record): bool
    {
        $user = auth()->user();
        return (bool) ($user?->hasRole('super_admin') || ($user?->can('finance.delete_transactions') ?? false));
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        return (bool) ($user?->hasRole('super_admin') || ($user?->can('finance.delete_transactions') ?? false));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
