<?php

namespace App\Filament\Resources\Rental;

use App\Filament\Resources\Rental\RentalContractResource\Pages;
use App\Filament\Resources\Rental\RentalContractResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Rental\RentalContract;
use App\Models\MainCore\Branch;
use App\Models\Sales\Customer;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Profession;
use App\Models\MainCore\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RentalContractResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = RentalContract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'قسم التأجير';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'navigation.rental_contracts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label(tr('rental.fields.branch', [], null, 'dashboard') ?: 'Branch')
                            ->options(function () {
                                return Cache::remember('rental.branches', 21600, function () {
                                    return Branch::active()->get()->pluck('name', 'id')->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\Select::make('customer_id')
                            ->label(tr('rental.fields.customer', [], null, 'dashboard') ?: 'Customer')
                            ->options(function () {
                                return Cache::remember('rental.customers', 21600, function () {
                                    return Customer::active()->get()->pluck('name', 'id')->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\Select::make('package_id')
                            ->label(tr('rental.fields.package', [], null, 'dashboard') ?: 'Package')
                            ->options(function () {
                                return Cache::remember('rental.packages', 21600, function () {
                                    return \App\Models\Package::where('type', 'rental')
                                        ->where('status', 'active')
                                        ->get()
                                        ->pluck('name', 'id')
                                        ->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $package = \App\Models\Package::find($state);
                                    if ($package) {
                                        $set('tax_percent', $package->tax_percent ?? 0);
                                    }
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('request_no')
                            ->label(tr('rental.fields.request_no', [], null, 'dashboard') ?: 'Request No')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\Select::make('worker_id')
                            ->label(tr('rental.fields.worker', [], null, 'dashboard') ?: 'Worker')
                            ->options(function () {
                                return Cache::remember('rental.workers', 21600, function () {
                                    return Laborer::where('is_available', true)
                                        ->get()
                                        ->mapWithKeys(function ($worker) {
                                            return [$worker->id => "{$worker->name_ar} ({$worker->passport_number})"];
                                        })
                                        ->toArray();
                                });
                            })
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('country_id')
                            ->label(tr('rental.fields.country', [], null, 'dashboard') ?: 'Country')
                            ->options(function () {
                                return Cache::remember('rental.countries', 21600, function () {
                                    return Country::where('is_active', true)
                                        ->get()
                                        ->pluck('name_text', 'id')
                                        ->toArray();
                                });
                            })
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('profession_id')
                            ->label(tr('rental.fields.profession', [], null, 'dashboard') ?: 'Profession')
                            ->options(function () {
                                return Cache::remember('rental.professions', 21600, function () {
                                    return Profession::where('is_active', true)
                                        ->get()
                                        ->pluck('name_ar', 'id')
                                        ->toArray();
                                });
                            })
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('start_date')
                            ->label(tr('rental.fields.start_date', [], null, 'dashboard') ?: 'Start Date')
                            ->required()
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state && $get('duration') && $get('duration_type')) {
                                    $endDate = static::calculateEndDate($state, $get('duration'), $get('duration_type'));
                                    $set('end_date', $endDate);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\Select::make('duration_type')
                            ->label(tr('rental.fields.duration_type', [], null, 'dashboard') ?: 'Duration Type')
                            ->options([
                                'day' => tr('rental.duration_type.day', [], null, 'dashboard') ?: 'Day',
                                'month' => tr('rental.duration_type.month', [], null, 'dashboard') ?: 'Month',
                                'year' => tr('rental.duration_type.year', [], null, 'dashboard') ?: 'Year',
                            ])
                            ->required()
                            ->default('month')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($get('start_date') && $get('duration')) {
                                    $endDate = static::calculateEndDate($get('start_date'), $get('duration'), $state);
                                    $set('end_date', $endDate);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('duration')
                            ->label(tr('rental.fields.duration', [], null, 'dashboard') ?: 'Duration')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($get('start_date') && $get('duration_type')) {
                                    $endDate = static::calculateEndDate($get('start_date'), $state, $get('duration_type'));
                                    $set('end_date', $endDate);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('end_date')
                            ->label(tr('rental.fields.end_date', [], null, 'dashboard') ?: 'End Date')
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('rental.contracts.pricing', [], null, 'dashboard') ?: 'Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('tax_percent')
                            ->label(tr('rental.fields.tax_percent', [], null, 'dashboard') ?: 'Tax %')
                            ->numeric()
                            ->default(0)
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->columnSpan(1),

                        Forms\Components\Select::make('discount_type')
                            ->label(tr('rental.fields.discount_type', [], null, 'dashboard') ?: 'Discount Type')
                            ->options([
                                'none' => tr('rental.discount_type.none', [], null, 'dashboard') ?: 'None',
                                'percent' => tr('rental.discount_type.percent', [], null, 'dashboard') ?: 'Percent',
                                'fixed' => tr('rental.discount_type.fixed', [], null, 'dashboard') ?: 'Fixed',
                            ])
                            ->default('none')
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('discount_value')
                            ->label(tr('rental.fields.discount_value', [], null, 'dashboard') ?: 'Discount Value')
                            ->numeric()
                            ->default(0)
                            ->step(0.01)
                            ->minValue(0)
                            ->visible(fn (callable $get) => $get('discount_type') !== 'none')
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('subtotal')
                            ->label(tr('rental.fields.subtotal', [], null, 'dashboard') ?: 'Subtotal')
                            ->content(fn ($record) => $record ? number_format($record->subtotal, 2) : '0.00')
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('tax_value')
                            ->label(tr('rental.fields.tax_value', [], null, 'dashboard') ?: 'Tax Value')
                            ->content(fn ($record) => $record ? number_format($record->tax_value, 2) : '0.00')
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('total')
                            ->label(tr('rental.fields.total', [], null, 'dashboard') ?: 'Total')
                            ->content(fn ($record) => $record ? number_format($record->total, 2) : '0.00')
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('remaining_total')
                            ->label(tr('rental.fields.remaining_total', [], null, 'dashboard') ?: 'Remaining')
                            ->content(fn ($record) => $record ? number_format($record->remaining_total, 2) : '0.00')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(tr('rental.fields.notes', [], null, 'dashboard') ?: 'Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_no')
                    ->label(tr('rental.fields.contract_no', [], null, 'dashboard') ?: 'Contract No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label(tr('rental.fields.customer', [], null, 'dashboard') ?: 'Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('worker.name_ar')
                    ->label(tr('rental.fields.worker', [], null, 'dashboard') ?: 'Worker')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(tr('rental.fields.start_date', [], null, 'dashboard') ?: 'Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(tr('rental.fields.end_date', [], null, 'dashboard') ?: 'End Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label(tr('rental.fields.duration', [], null, 'dashboard') ?: 'Duration')
                    ->formatStateUsing(fn ($record) => "{$record->duration} " . tr("rental.duration_type.{$record->duration_type}", [], null, 'dashboard'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label(tr('rental.fields.total', [], null, 'dashboard') ?: 'Total')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('rental.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'suspended',
                        'info' => 'completed',
                        'danger' => 'cancelled',
                        'secondary' => 'returned',
                        'gray' => 'archived',
                    ])
                    ->formatStateUsing(fn ($state) => tr("rental.status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label(tr('rental.fields.payment_status', [], null, 'dashboard') ?: 'Payment Status')
                    ->colors([
                        'success' => 'paid',
                        'danger' => 'unpaid',
                        'warning' => 'partial',
                        'gray' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => tr("rental.payment_status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('rental.fields.branch', [], null, 'dashboard') ?: 'Branch')
                    ->options(function () {
                        return Branch::active()->get()->pluck('name', 'id')->toArray();
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('rental.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'active' => tr('rental.status.active', [], null, 'dashboard') ?: 'Active',
                        'suspended' => tr('rental.status.suspended', [], null, 'dashboard') ?: 'Suspended',
                        'completed' => tr('rental.status.completed', [], null, 'dashboard') ?: 'Completed',
                        'cancelled' => tr('rental.status.cancelled', [], null, 'dashboard') ?: 'Cancelled',
                        'returned' => tr('rental.status.returned', [], null, 'dashboard') ?: 'Returned',
                        'archived' => tr('rental.status.archived', [], null, 'dashboard') ?: 'Archived',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label(tr('rental.fields.payment_status', [], null, 'dashboard') ?: 'Payment Status')
                    ->options([
                        'paid' => tr('rental.payment_status.paid', [], null, 'dashboard') ?: 'Paid',
                        'unpaid' => tr('rental.payment_status.unpaid', [], null, 'dashboard') ?: 'Unpaid',
                        'partial' => tr('rental.payment_status.partial', [], null, 'dashboard') ?: 'Partial',
                        'refunded' => tr('rental.payment_status.refunded', [], null, 'dashboard') ?: 'Refunded',
                    ]),

                Tables\Filters\Filter::make('start_date')
                    ->form([
                        Forms\Components\DatePicker::make('start_from')
                            ->label('Start From'),
                        Forms\Components\DatePicker::make('start_until')
                            ->label('Start Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['start_from'], fn ($q, $date) => $q->whereDate('start_date', '>=', $date))
                            ->when($data['start_until'], fn ($q, $date) => $q->whereDate('start_date', '<=', $date));
                    }),

                Tables\Filters\SelectFilter::make('customer_id')
                    ->label(tr('rental.fields.customer', [], null, 'dashboard') ?: 'Customer')
                    ->relationship('customer', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('print_contract')
                    ->label(tr('rental.print.contract', [], null, 'dashboard') ?: 'Print Contract')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('rental.contracts.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn () => auth()->user()?->can('rental.print.contract')),
                Tables\Actions\Action::make('print_invoice')
                    ->label(tr('rental.print.invoice', [], null, 'dashboard') ?: 'Print Invoice')
                    ->icon('heroicon-o-document-text')
                    ->url(fn ($record) => route('rental.contracts.invoice', $record))
                    ->openUrlInNewTab()
                    ->visible(fn () => auth()->user()?->can('rental.print.invoice')),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
            RelationManagers\StatusLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentalContracts::route('/'),
            'create' => Pages\CreateRentalContract::route('/create'),
            'view' => Pages\ViewRentalContract::route('/{record}'),
            'edit' => Pages\EditRentalContract::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.contracts.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.contracts.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.contracts.view') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.contracts.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('rental.contracts.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    protected static function calculateEndDate($startDate, $duration, $durationType): string
    {
        if (!$startDate || !$duration || !$durationType) {
            return now()->toDateString();
        }

        $date = Carbon::parse($startDate);
        return match($durationType) {
            'day' => $date->addDays($duration)->toDateString(),
            'month' => $date->addMonths($duration)->toDateString(),
            'year' => $date->addYears($duration)->toDateString(),
            default => $date->addMonths($duration)->toDateString(),
        };
    }
}
