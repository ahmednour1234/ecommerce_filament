<?php

namespace App\Filament\Resources\ServiceTransfer;

use App\Filament\Resources\ServiceTransfer\ServiceTransferResource\Pages;
use App\Filament\Resources\ServiceTransfer\ServiceTransferResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\ServiceTransfer;
use App\Models\MainCore\Branch;
use App\Models\Sales\Customer;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Nationality;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class ServiceTransferResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = ServiceTransfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'نقل الخدمات';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\DatePicker::make('request_date')
                            ->label('تاريخ الطلب')
                            ->required()
                            ->default(now())
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('request_no')
                            ->label('رقم الطلب')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\Select::make('branch_id')
                            ->label('الفرع')
                            ->options(function () {
                                return Cache::remember('service_transfer.branches', 21600, function () {
                                    return Branch::active()->get()->pluck('name', 'id')->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('customer_id')
                            ->label('العميل')
                            ->options(function () {
                                return Cache::remember('service_transfer.customers', 21600, function () {
                                    return Customer::active()->get()->pluck('name', 'id')->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('worker_id')
                            ->label('العاملة')
                            ->options(function () {
                                return Cache::remember('service_transfer.workers', 21600, function () {
                                    return Laborer::where('is_available', true)
                                        ->get()
                                        ->mapWithKeys(function ($worker) {
                                            return [$worker->id => "{$worker->name_ar} ({$worker->passport_number})"];
                                        })
                                        ->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('nationality_id')
                            ->label('الدولة')
                            ->options(function () {
                                return Cache::remember('service_transfer.nationalities', 21600, function () {
                                    return Nationality::where('is_active', true)
                                        ->get()
                                        ->mapWithKeys(function ($nationality) {
                                            return [$nationality->id => app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en];
                                        })
                                        ->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('package_id')
                            ->label('الباقة')
                            ->options(function () {
                                return Cache::remember('service_transfer.packages', 21600, function () {
                                    return Package::where('type', 'service_transfer')
                                        ->where('status', 'active')
                                        ->get()
                                        ->pluck('name', 'id')
                                        ->toArray();
                                });
                            })
                            ->nullable()
                            ->searchable()
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('التسعير')
                    ->schema([
                        Forms\Components\TextInput::make('base_price')
                            ->label('السعر الأساسي')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->step(0.01)
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                static::recalculateTotals($set, $get);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('external_cost')
                            ->label('التكاليف الخارجية')
                            ->numeric()
                            ->default(0)
                            ->step(0.01)
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                static::recalculateTotals($set, $get);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('government_fees')
                            ->label('الرسوم الحكومية')
                            ->numeric()
                            ->default(0)
                            ->step(0.01)
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                static::recalculateTotals($set, $get);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('tax_percent')
                            ->label('نسبة الضريبة')
                            ->numeric()
                            ->default(15)
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                static::recalculateTotals($set, $get);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('tax_value')
                            ->label('قيمة الضريبة')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('discount_percent')
                            ->label('نسبة الخصم')
                            ->numeric()
                            ->default(0)
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                static::recalculateTotals($set, $get);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('discount_reason')
                            ->label('سبب الخصم')
                            ->maxLength(255)
                            ->visible(fn (callable $get) => ($get('discount_percent') ?? 0) > 0)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('discount_value')
                            ->label('قيمة الخصم')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('total_amount')
                            ->label('المبلغ الإجمالي')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function recalculateTotals(callable $set, callable $get): void
    {
        $basePrice = (float) ($get('base_price') ?? 0);
        $externalCost = (float) ($get('external_cost') ?? 0);
        $governmentFees = (float) ($get('government_fees') ?? 0);
        $base = $basePrice + $externalCost + $governmentFees;

        $taxPercent = (float) ($get('tax_percent') ?? 15);
        $taxValue = $base * ($taxPercent / 100);

        $discountPercent = (float) ($get('discount_percent') ?? 0);
        $discountValue = $base * ($discountPercent / 100);

        $totalAmount = $base + $taxValue - $discountValue;

        $set('tax_value', number_format($taxValue, 2, '.', ''));
        $set('discount_value', number_format($discountValue, 2, '.', ''));
        $set('total_amount', number_format($totalAmount, 2, '.', ''));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request_no')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('worker.name_ar')
                    ->label('العاملة')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('request_date')
                    ->label('تاريخ الطلب')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('المبلغ الإجمالي')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('request_status')
                    ->label('حالة الطلب')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'archived',
                        'danger' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'active' => 'نشط',
                        'archived' => 'مؤرشف',
                        'refunded' => 'مسترد',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('حالة الدفع')
                    ->colors([
                        'success' => 'paid',
                        'danger' => 'unpaid',
                        'warning' => 'partial',
                        'gray' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'paid' => 'مدفوع',
                        'unpaid' => 'غير مدفوع',
                        'partial' => 'جزئي',
                        'refunded' => 'مسترد',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('الفرع')
                    ->options(function () {
                        return Branch::active()->get()->pluck('name', 'id')->toArray();
                    }),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('حالة الدفع')
                    ->options([
                        'unpaid' => 'غير مدفوع',
                        'partial' => 'جزئي',
                        'paid' => 'مدفوع',
                        'refunded' => 'مسترد',
                    ]),

                Tables\Filters\SelectFilter::make('request_status')
                    ->label('حالة الطلب')
                    ->options([
                        'active' => 'نشط',
                        'archived' => 'مؤرشف',
                        'refunded' => 'مسترد',
                    ]),

                Tables\Filters\Filter::make('request_date')
                    ->form([
                        Forms\Components\DatePicker::make('request_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('request_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['request_from'], fn ($q, $date) => $q->whereDate('request_date', '>=', $date))
                            ->when($data['request_until'], fn ($q, $date) => $q->whereDate('request_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('archive')
                    ->label('أرشفة')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (ServiceTransfer $record) {
                        $record->update(['request_status' => 'archived']);
                    })
                    ->visible(fn (ServiceTransfer $record) => $record->request_status === 'active')
                    ->visible(fn () => auth()->user()?->can('service_transfer.archive') ?? false),
                Tables\Actions\Action::make('refund')
                    ->label('استرداد')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (ServiceTransfer $record) {
                        $record->update([
                            'request_status' => 'refunded',
                            'payment_status' => 'refunded',
                        ]);
                    })
                    ->visible(fn (ServiceTransfer $record) => $record->request_status === 'active')
                    ->visible(fn () => auth()->user()?->can('service_transfer.refund') ?? false),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceTransfers::route('/'),
            'create' => Pages\CreateServiceTransfer::route('/create'),
            'view' => Pages\ViewServiceTransfer::route('/{record}'),
            'edit' => Pages\EditServiceTransfer::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('service_transfer.view') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('service_transfer.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('service_transfer.view') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('service_transfer.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('service_transfer.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
