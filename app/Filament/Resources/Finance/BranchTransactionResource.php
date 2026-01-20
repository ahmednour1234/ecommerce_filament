<?php

namespace App\Filament\Resources\Finance;

use App\Filament\Resources\Finance\BranchTransactionResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\FinanceModuleGate;
use App\Models\Finance\BranchTransaction;
use App\Models\Finance\FinanceType;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Currency;
use App\Services\MainCore\CurrencyService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchTransactionResource extends Resource
{
    use TranslatableNavigation, FinanceModuleGate;

    protected static ?string $model = BranchTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery()
            ->with(['branch', 'country', 'currency', 'financeType', 'creator']);

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
        $userBranches = $user?->branches()->pluck('branches.id')->toArray();
        $canViewAllBranches = $user?->hasRole('super_admin') || $user?->can('finance.view_all_branches');

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('finance_type_id')
                            ->label('Type')
                            ->options(function ($get) {
                                $query = FinanceType::query()->where('is_active', true);
                                if ($get('kind_filter')) {
                                    $query->where('kind', $get('kind_filter'));
                                }
                                return $query->get()->pluck('name_text', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('kind_filter', null)),

                        Forms\Components\Select::make('kind_filter')
                            ->label('Filter by Kind')
                            ->options([
                                'income' => 'Income',
                                'expense' => 'Expense',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $set('finance_type_id', null);
                                }
                            })
                            ->dehydrated(false),

                        Forms\Components\DatePicker::make('trx_date')
                            ->label('Transaction Date')
                            ->required()
                            ->default(now()),

                        Forms\Components\Select::make('branch_id')
                            ->label('Branch')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn () => !$canViewAllBranches)
                            ->default(fn () => !$canViewAllBranches && !empty($userBranches) ? $userBranches[0] : null)
                            ->visible(fn () => $canViewAllBranches || !empty($userBranches)),

                        Forms\Components\Select::make('country_id')
                            ->label('Country')
                            ->relationship('country', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name_text ?? $record->name['en'] ?? '')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('currency_id')
                            ->label('Currency')
                            ->relationship('currency', 'code')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default($defaultCurrencyId),

                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->step(0.01),

                        Forms\Components\TextInput::make('payment_method')
                            ->label('Payment Method')
                            ->maxLength(50)
                            ->nullable(),

                        Forms\Components\TextInput::make('recipient_name')
                            ->label('Recipient Name')
                            ->maxLength(150)
                            ->nullable(),

                        Forms\Components\TextInput::make('reference_no')
                            ->label('Reference No')
                            ->maxLength(100)
                            ->nullable(),

                        Forms\Components\FileUpload::make('attachment_path')
                            ->label('Attachment')
                            ->disk('public')
                            ->directory('finance/transactions')
                            ->openable()
                            ->downloadable()
                            ->preserveFilenames()
                            ->maxSize(8192)
                            ->nullable(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
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
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('financeType.kind')
                    ->label('Kind')
                    ->badge()
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('financeType.name_text')
                    ->label('Type')
                    ->getStateUsing(fn ($record) => $record->financeType?->name_text)
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state, $record) => number_format((float) $state, 2) . ' ' . ($record->currency?->code ?? ''))
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency.code')
                    ->label('Currency')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reference_no')
                    ->label('Reference')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label('Recipient')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('kind')
                    ->label('Kind')
                    ->options([
                        'income' => 'Income',
                        'expense' => 'Expense',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            return $query->whereHas('financeType', function ($q) use ($data) {
                                $q->where('kind', $data['value']);
                            });
                        }
                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('finance_type_id')
                    ->label('Type')
                    ->options(FinanceType::where('is_active', true)->get()->pluck('name_text', 'id'))
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('currency_id')
                    ->label('Currency')
                    ->relationship('currency', 'code')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('trx_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('to')
                            ->label('To'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('trx_date', '>=', $date))
                            ->when($data['to'] ?? null, fn (Builder $q, $date) => $q->whereDate('trx_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.update_transactions') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.delete_transactions') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.delete_transactions') ?? false),
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
        return $user?->hasRole('super_admin') || $user?->can('finance.view_transactions') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('finance.create_transactions') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('finance.update_transactions') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('finance.delete_transactions') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('finance.delete_transactions') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
