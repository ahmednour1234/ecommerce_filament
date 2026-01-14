<?php

namespace App\Filament\Resources\Finance;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Finance\BranchTransactionResource\Pages;
use App\Models\Finance\BranchTransaction;
use App\Services\Finance\BranchTransactionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchTransactionResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = BranchTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'sidebar.finance.branch_transactions';

    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery()
            ->with(['branch', 'country', 'currency', 'creator', 'approver', 'rejecter']);

        if (! auth()->user()?->can('branch_tx.view_all_branches')) {
            $q->where('branch_id', auth()->user()?->branch_id);
        }

        return $q;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(tr('forms.branch_tx.sections.main', [], null, 'dashboard'))
                ->schema([
                    Forms\Components\DatePicker::make('transaction_date')
                        ->label(tr('forms.branch_tx.transaction_date', [], null, 'dashboard'))
                        ->required()
                        ->default(now()),

                    Forms\Components\Select::make('type')
                        ->label(tr('forms.branch_tx.type', [], null, 'dashboard'))
                        ->options([
                            'income' => tr('forms.branch_tx.type_income', [], null, 'dashboard'),
                            'expense' => tr('forms.branch_tx.type_expense', [], null, 'dashboard'),
                        ])
                        ->required(),

                    Forms\Components\Select::make('branch_id')
                        ->label(tr('forms.branch_tx.branch', [], null, 'dashboard'))
                        ->relationship('branch', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabled(fn () => ! auth()->user()?->can('branch_tx.view_all_branches'))
                        ->default(fn () => auth()->user()?->branch_id),

                    Forms\Components\Select::make('country_id')
                        ->label(tr('forms.branch_tx.country', [], null, 'dashboard'))
                        ->relationship('country', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\TextInput::make('amount')
                        ->label(tr('forms.branch_tx.amount', [], null, 'dashboard'))
                        ->numeric()
                        ->required(),

                    Forms\Components\Select::make('currency_id')
                        ->label(tr('forms.branch_tx.currency', [], null, 'dashboard'))
                        ->relationship('currency', 'code')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('receiver_name')
                        ->label(tr('forms.branch_tx.receiver_name', [], null, 'dashboard'))
                        ->maxLength(255)
                        ->nullable(),

                    Forms\Components\TextInput::make('payment_method')
                        ->label(tr('forms.branch_tx.payment_method', [], null, 'dashboard'))
                        ->maxLength(100)
                        ->nullable(),

                    Forms\Components\TextInput::make('reference_no')
                        ->label(tr('forms.branch_tx.reference_no', [], null, 'dashboard'))
                        ->maxLength(100)
                        ->nullable(),

                    Forms\Components\FileUpload::make('attachment_path')
                        ->label(tr('forms.branch_tx.attachment', [], null, 'dashboard'))
                        ->disk('public')
                        ->directory('branch-transactions')
                        ->openable()
                        ->downloadable()
                        ->preserveFilenames()
                        ->maxSize(8192)
                        ->nullable(),

                    Forms\Components\Textarea::make('notes')
                        ->label(tr('forms.branch_tx.notes', [], null, 'dashboard'))
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
                Tables\Columns\TextColumn::make('document_no')
                    ->label(tr('tables.branch_tx.document_no', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('tables.branch_tx.branch', [], null, 'dashboard'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->label(tr('tables.branch_tx.country', [], null, 'dashboard'))
                    ->toggleable(isToggledHiddenByDefault: true),

                // ✅ FIX: $state بدل $s
                Tables\Columns\BadgeColumn::make('type')
                    ->label(tr('tables.branch_tx.type', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state) => $state === 'income'
                        ? tr('forms.branch_tx.type_income', [], null, 'dashboard')
                        : tr('forms.branch_tx.type_expense', [], null, 'dashboard')
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('tables.branch_tx.amount', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state, $record) => number_format((float) $state, 2) . ' ' . ($record->currency?->code ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount_base')
                    ->label(tr('tables.branch_tx.amount_base', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 2) : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                // ✅ FIX: $state بدل $s
                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('tables.branch_tx.status', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state) => tr('tables.branch_tx.status_' . $state, [], null, 'dashboard'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaction_date')
                    ->label(tr('tables.branch_tx.transaction_date', [], null, 'dashboard'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('receiver_name')
                    ->label(tr('tables.branch_tx.receiver_name', [], null, 'dashboard'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('tables.branch_tx.filters.branch', [], null, 'dashboard'))
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('country_id')
                    ->label(tr('tables.branch_tx.filters.country', [], null, 'dashboard'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('currency_id')
                    ->label(tr('tables.branch_tx.filters.currency', [], null, 'dashboard'))
                    ->relationship('currency', 'code')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->label(tr('tables.branch_tx.filters.type', [], null, 'dashboard'))
                    ->options([
                        'income' => tr('forms.branch_tx.type_income', [], null, 'dashboard'),
                        'expense' => tr('forms.branch_tx.type_expense', [], null, 'dashboard'),
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.branch_tx.filters.status', [], null, 'dashboard'))
                    ->options([
                        'pending' => tr('tables.branch_tx.status_pending', [], null, 'dashboard'),
                        'approved' => tr('tables.branch_tx.status_approved', [], null, 'dashboard'),
                        'rejected' => tr('tables.branch_tx.status_rejected', [], null, 'dashboard'),
                    ]),

                Tables\Filters\Filter::make('transaction_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('to'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $d) => $q->whereDate('transaction_date', '>=', $d))
                            ->when($data['to'] ?? null, fn (Builder $q, $d) => $q->whereDate('transaction_date', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('branch_tx.update') && $record->status === 'pending'),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('branch_tx.delete') && $record->status === 'pending'),

                Tables\Actions\Action::make('approve')
                    ->label(tr('actions.approve', [], null, 'dashboard'))
                    ->visible(fn ($record) => auth()->user()?->can('branch_tx.approve') && $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('approval_note')
                            ->label(tr('forms.branch_tx.approval_note', [], null, 'dashboard')),
                    ])
                    ->action(function ($record, array $data) {
                        app(BranchTransactionService::class)->approve($record, $data['approval_note'] ?? null);
                    }),

                Tables\Actions\Action::make('reject')
                    ->label(tr('actions.reject', [], null, 'dashboard'))
                    ->visible(fn ($record) => auth()->user()?->can('branch_tx.reject') && $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_note')
                            ->label(tr('forms.branch_tx.rejection_note', [], null, 'dashboard'))
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        app(BranchTransactionService::class)->reject($record, $data['rejection_note']);
                    }),

                Tables\Actions\Action::make('print')
                    ->label(tr('actions.print', [], null, 'dashboard'))
                    ->visible(fn () => auth()->user()?->can('branch_tx.print'))
                    ->url(fn ($record) => static::getUrl('print', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('transaction_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranchTransactions::route('/'),
            'create' => Pages\CreateBranchTransaction::route('/create'),
            'edit' => Pages\EditBranchTransaction::route('/{record}/edit'),
            'print' => Pages\PrintBranchTransaction::route('/{record}/print'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('branch_tx.view_any') ?? false;
    }
}
