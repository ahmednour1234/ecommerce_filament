<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\LoanResource\Pages;
use App\Filament\Resources\HR\LoanResource\RelationManagers;
use App\Filament\Resources\HR\LoanResource\Widgets;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\HR\Loan;
use App\Models\HR\Employee;
use App\Models\HR\LoanType;
use App\Services\HR\LoanService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoanResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'HR';
    protected static ?int $navigationSort = 41;
    protected static ?string $navigationTranslationKey = 'navigation.hr_loans';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label(tr('fields.employee', [], null, 'dashboard') ?: 'Employee')
                            ->relationship('employee', 'employee_number')
                            ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->employee_number . ' - ' . $record->full_name)
                            ->searchable(['employee_number', 'first_name', 'last_name'])
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('loan_type_id')
                            ->label(tr('fields.loan_type', [], null, 'dashboard') ?: 'Loan Type')
                            ->relationship('loanType', 'name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label(tr('fields.amount', [], null, 'dashboard') ?: 'Amount')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->prefix('$')
                            ->reactive(),

                        Forms\Components\TextInput::make('installments_count')
                            ->label(tr('fields.installments', [], null, 'dashboard') ?: 'Installments Count')
                            ->numeric()
                            ->required()
                            ->integer()
                            ->minValue(1)
                            ->reactive(),

                        Forms\Components\DatePicker::make('start_date')
                            ->label(tr('fields.start_date', [], null, 'dashboard') ?: 'Start Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->reactive(),

                        Forms\Components\Textarea::make('purpose')
                            ->label(tr('fields.purpose', [], null, 'dashboard') ?: 'Purpose')
                            ->rows(3)
                            ->columnSpanFull()
                            ->nullable(),

                        Forms\Components\FileUpload::make('attachment')
                            ->label(tr('fields.attachment', [], null, 'dashboard') ?: 'Attachment')
                            ->directory('loans/attachments')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(10240)
                            ->columnSpanFull()
                            ->nullable(),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('calculate')
                                ->label(tr('actions.calculate', [], null, 'dashboard') ?: 'Calculate Loan')
                                ->icon('heroicon-o-calculator')
                                ->modalHeading(tr('actions.calculate', [], null, 'dashboard') ?: 'Loan Schedule Preview')
                                ->modalSubmitAction(false)
                                ->modalCancelActionLabel(tr('actions.close', [], null, 'dashboard') ?: 'Close')
                                ->modalContent(function (Forms\Get $get) {
                                    $amount = (float) $get('amount');
                                    $count = (int) $get('installments_count');
                                    $startDate = $get('start_date');

                                    if (!$amount || !$count || !$startDate) {
                                        return '<div class="p-4 text-center text-gray-500">Please fill in amount, installments count, and start date.</div>';
                                    }

                                    $service = app(LoanService::class);
                                    $schedule = $service->previewSchedule($amount, $count, $startDate);
                                    $installmentAmount = round($amount / $count, 2);

                                    return view('filament.resources.hr.loan-schedule-preview', [
                                        'schedule' => $schedule,
                                        'totalAmount' => $amount,
                                        'installmentAmount' => $installmentAmount,
                                        'count' => $count,
                                    ]);
                                })
                                ->disabled(fn (Forms\Get $get) => !$get('amount') || !$get('installments_count') || !$get('start_date')),
                        ])
                        ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label(tr('fields.employee', [], null, 'dashboard') ?: 'Employee')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('loanType.name')
                    ->label(tr('fields.loan_type', [], null, 'dashboard') ?: 'Loan Type')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('fields.amount', [], null, 'dashboard') ?: 'Amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('installments_count')
                    ->label(tr('fields.installments', [], null, 'dashboard') ?: 'Installments')
                    ->sortable(),

                Tables\Columns\TextColumn::make('installment_amount')
                    ->label(tr('fields.installment_amount', [], null, 'dashboard') ?: 'Installment Amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(tr('fields.start_date', [], null, 'dashboard') ?: 'Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'warning',
                        'closed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => tr("status.{$state}", [], null, 'dashboard') ?: ucfirst($state))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label(tr('fields.employee', [], null, 'dashboard') ?: 'Employee')
                    ->relationship('employee', 'employee_number')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('loan_type_id')
                    ->label(tr('fields.loan_type', [], null, 'dashboard') ?: 'Loan Type')
                    ->relationship('loanType', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'active' => tr('status.active', [], null, 'dashboard') ?: 'Active',
                        'closed' => tr('status.closed', [], null, 'dashboard') ?: 'Closed',
                    ]),

                Tables\Filters\Filter::make('start_date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(tr('fields.date_from', [], null, 'dashboard') ?: 'From Date'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label(tr('fields.date_to', [], null, 'dashboard') ?: 'To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn ($query, $date) => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn ($query, $date) => $query->whereDate('start_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr.loans.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr.loans.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr.loans.delete') ?? false),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InstallmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.loans.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.loans.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr.loans.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr.loans.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('hr.loans.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
