<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Forms\Components\FileUpload;
use App\Filament\Resources\Accounting\BankGuaranteeResource\Pages;
use App\Models\Accounting\Account;
use App\Models\Accounting\BankGuarantee;
use App\Models\MainCore\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class BankGuaranteeResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = BankGuarantee::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.bank_guarantees';

    public static function form(Form $form): Form
    {
        $accounts = Account::active()->where('allow_manual_entry', true)->get();
        $accountOptions = $accounts->mapWithKeys(function ($account) {
            return [$account->id => $account->code . ' - ' . $account->name];
        })->toArray();

        return $form
            ->schema([
                Forms\Components\Section::make(tr('forms.bank_guarantees.sections.basic_information', [], null, 'dashboard'))
                    ->schema([
                        Forms\Components\TextInput::make('guarantee_number')
                            ->label(tr('forms.bank_guarantees.fields.guarantee_number', [], null, 'dashboard'))
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->nullable()
                            ->helperText(tr('helpers.auto_generate_number', [], null, 'dashboard')),

                        Forms\Components\TextInput::make('beneficiary_name')
                            ->label(tr('forms.bank_guarantees.fields.beneficiary_name', [], null, 'dashboard'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('issue_date')
                            ->label(tr('forms.bank_guarantees.fields.issue_date', [], null, 'dashboard'))
                            ->required()
                            ->default(now())
                            ->displayFormat('Y-m-d'),

                        Forms\Components\DatePicker::make('start_date')
                            ->label(tr('forms.bank_guarantees.fields.start_date', [], null, 'dashboard'))
                            ->required()
                            ->default(now())
                            ->displayFormat('Y-m-d'),

                        Forms\Components\DatePicker::make('end_date')
                            ->label(tr('forms.bank_guarantees.fields.end_date', [], null, 'dashboard'))
                            ->required()
                            ->after('start_date')
                            ->displayFormat('Y-m-d'),

                        Forms\Components\Select::make('branch_id')
                            ->label(tr('forms.bank_guarantees.fields.branch', [], null, 'dashboard'))
                            ->options(Branch::active()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('status')
                            ->label(tr('forms.bank_guarantees.fields.status', [], null, 'dashboard'))
                            ->options([
                                'active' => tr('forms.bank_guarantees.status.active', [], null, 'dashboard'),
                                'expired' => tr('forms.bank_guarantees.status.expired', [], null, 'dashboard'),
                                'cancelled' => tr('forms.bank_guarantees.status.cancelled', [], null, 'dashboard'),
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('forms.bank_guarantees.sections.financial_information', [], null, 'dashboard'))
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label(tr('forms.bank_guarantees.fields.amount', [], null, 'dashboard'))
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->prefix(tr('forms.bank_guarantees.currency_symbol', [], null, 'dashboard')),

                        Forms\Components\TextInput::make('bank_fees')
                            ->label(tr('forms.bank_guarantees.fields.bank_fees', [], null, 'dashboard'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix(tr('forms.bank_guarantees.currency_symbol', [], null, 'dashboard'))
                            ->helperText(tr('helpers.fees_zero_if_none', [], null, 'dashboard'))
                            ->reactive(),

                        Forms\Components\Select::make('original_guarantee_account_id')
                            ->label(tr('forms.bank_guarantees.fields.original_guarantee_account', [], null, 'dashboard'))
                            ->options($accountOptions)
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('bank_account_id')
                            ->label(tr('forms.bank_guarantees.fields.bank_account', [], null, 'dashboard'))
                            ->options($accountOptions)
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('bank_fees_account_id')
                            ->label(tr('forms.bank_guarantees.fields.bank_fees_account', [], null, 'dashboard'))
                            ->options($accountOptions)
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(fn (Forms\Get $get) => (float) ($get('bank_fees') ?? 0) > 0)
                            ->required(fn (Forms\Get $get) => (float) ($get('bank_fees') ?? 0) > 0),

                        Forms\Components\Select::make('bank_fees_debit_account_id')
                            ->label(tr('forms.bank_guarantees.fields.bank_fees_debit_account', [], null, 'dashboard'))
                            ->options($accountOptions)
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(fn (Forms\Get $get) => (float) ($get('bank_fees') ?? 0) > 0)
                            ->helperText(tr('helpers.fees_debit_optional', [], null, 'dashboard')),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('forms.bank_guarantees.sections.attachments', [], null, 'dashboard'))
                    ->schema([
                        FileUpload::document('attachment_path', 'bank-guarantees')
                            ->label(tr('forms.bank_guarantees.fields.attachment', [], null, 'dashboard'))
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'image/*',
                            ])
                            ->maxSize(10240)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(tr('forms.bank_guarantees.sections.notes', [], null, 'dashboard'))
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(tr('forms.bank_guarantees.fields.notes', [], null, 'dashboard'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('guarantee_number')
                    ->label(tr('tables.bank_guarantees.guarantee_number', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('beneficiary_name')
                    ->label(tr('tables.bank_guarantees.beneficiary_name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('tables.bank_guarantees.amount', [], null, 'dashboard'))
                    ->money(\App\Support\Money::defaultCurrencyCode())
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(tr('tables.bank_guarantees.end_date', [], null, 'dashboard'))
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->end_date < now() ? 'danger' : ($record->end_date < now()->addDays(30) ? 'warning' : null)),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('tables.bank_guarantees.status', [], null, 'dashboard'))
                    ->colors([
                        'success' => 'active',
                        'danger' => 'expired',
                        'gray' => 'cancelled',
                    ])
                    ->formatStateUsing(fn ($state) => tr('forms.bank_guarantees.status.' . $state, [], null, 'dashboard'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.bank_guarantees.created_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.bank_guarantees.filters.status', [], null, 'dashboard'))
                    ->options([
                        'active' => tr('forms.bank_guarantees.status.active', [], null, 'dashboard'),
                        'expired' => tr('forms.bank_guarantees.status.expired', [], null, 'dashboard'),
                        'cancelled' => tr('forms.bank_guarantees.status.cancelled', [], null, 'dashboard'),
                    ]),

                Tables\Filters\Filter::make('end_date')
                    ->form([
                        Forms\Components\DatePicker::make('end_date_from')
                            ->label(tr('tables.bank_guarantees.filters.end_date_from', [], null, 'dashboard')),
                        Forms\Components\DatePicker::make('end_date_until')
                            ->label(tr('tables.bank_guarantees.filters.end_date_until', [], null, 'dashboard')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['end_date_from'],
                                fn ($query, $date) => $query->whereDate('end_date', '>=', $date),
                            )
                            ->when(
                                $data['end_date_until'],
                                fn ($query, $date) => $query->whereDate('end_date', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('expired_soon')
                    ->label(tr('tables.bank_guarantees.filters.expired_soon', [], null, 'dashboard'))
                    ->query(fn ($query) => $query->where('end_date', '<=', now()->addDays(30))->where('end_date', '>=', now())),

                Tables\Filters\Filter::make('beneficiary_name')
                    ->form([
                        Forms\Components\TextInput::make('beneficiary_name')
                            ->label(tr('tables.bank_guarantees.filters.beneficiary_name', [], null, 'dashboard')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['beneficiary_name'],
                            fn ($query, $name) => $query->where('beneficiary_name', 'like', "%{$name}%"),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn () => auth()->user()?->can('bank_guarantees.view') ?? false),

                Tables\Actions\Action::make('renew')
                    ->label(tr('actions.renew', [], null, 'dashboard'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn ($record) => auth()->user()?->can('bank_guarantees.renew') ?? false)
                    ->modalHeading(tr('actions.renew', [], null, 'dashboard'))
                    ->modalDescription(tr('actions.renew_description', [], null, 'dashboard'))
                    ->form([
                        Forms\Components\Placeholder::make('current_end_date')
                            ->label(tr('forms.bank_guarantees.fields.current_end_date', [], null, 'dashboard'))
                            ->content(fn ($record) => $record->end_date->format('Y-m-d')),

                        Forms\Components\DatePicker::make('new_end_date')
                            ->label(tr('forms.bank_guarantees.fields.new_end_date', [], null, 'dashboard'))
                            ->required()
                            ->after(fn ($record) => $record->end_date->format('Y-m-d'))
                            ->displayFormat('Y-m-d')
                            ->helperText(tr('forms.bank_guarantees.fields.new_end_date_helper', [], null, 'dashboard')),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('forms.bank_guarantees.fields.notes', [], null, 'dashboard'))
                            ->rows(3)
                            ->nullable(),
                    ])
                    ->action(function (array $data, $record) {
                        try {
                            $newEndDate = new \DateTime($data['new_end_date']);

                            if ($newEndDate <= $record->end_date) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    'new_end_date' => tr('validation.renewal_date_must_be_after', [], null, 'dashboard'),
                                ]);
                            }

                            $oldEndDate = $record->end_date;
                            $record->renew($newEndDate, $data['notes'] ?? null);

                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title(tr('messages.renewed_successfully', [], null, 'dashboard'))
                                ->body(tr('messages.bank_guarantee_renewed', [
                                    'old_date' => $oldEndDate->format('Y-m-d'),
                                    'new_date' => $newEndDate->format('Y-m-d'),
                                ], null, 'dashboard'))
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title(tr('messages.renewal_failed', [], null, 'dashboard'))
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('bank_guarantees.update') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('bank_guarantees.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('bank_guarantees.delete') ?? false),
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
            'index' => Pages\ListBankGuarantees::route('/'),
            'create' => Pages\CreateBankGuarantee::route('/create'),
            'view' => Pages\ViewBankGuarantee::route('/{record}'),
            'edit' => Pages\EditBankGuarantee::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('bank_guarantees.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('bank_guarantees.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        return auth()->user()?->can('bank_guarantees.view') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('bank_guarantees.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('bank_guarantees.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('bank_guarantees.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

