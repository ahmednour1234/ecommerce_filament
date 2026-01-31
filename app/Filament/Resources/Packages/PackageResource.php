<?php

namespace App\Filament\Resources\Packages;

use App\Filament\Resources\Packages\PackageResource\Pages;
use App\Filament\Resources\Packages\PackageResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Package;
use App\Models\MainCore\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class PackageResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationGroup = 'Recruitment';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationTranslationKey = 'navigation.offers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(tr('fields.status', [], null, 'packages'))
                            ->options([
                                'active' => tr('status.active', [], null, 'packages'),
                                'inactive' => tr('status.inactive', [], null, 'packages'),
                            ])
                            ->default('active')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('type')
                            ->label(tr('fields.type', [], null, 'packages') ?: tr('common.type', [], null, 'dashboard'))
                            ->options([
                                'recruitment' => tr('types.recruitment', [], null, 'packages'),
                                'rental' => tr('types.rental', [], null, 'packages'),
                                'service_transfer' => tr('types.service_transfer', [], null, 'packages'),
                            ])
                            ->required()
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name')
                            ->label(tr('fields.name', [], null, 'packages'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label(tr('fields.description', [], null, 'packages'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('country_id')
                            ->label(tr('fields.country', [], null, 'packages'))
                            ->options(function () {
                                return Cache::remember('packages.countries', 21600, function () {
                                    return Country::where('is_active', true)
                                        ->get()
                                        ->pluck('name_text', 'id')
                                        ->toArray();
                                });
                            })
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('duration_type')
                            ->label(tr('fields.duration_type', [], null, 'packages'))
                            ->options([
                                'day' => tr('duration_types.day', [], null, 'packages'),
                                'month' => tr('duration_types.month', [], null, 'packages'),
                                'year' => tr('duration_types.year', [], null, 'packages'),
                            ])
                            ->required()
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('duration')
                            ->label(tr('fields.duration', [], null, 'packages'))
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('common.pricing', [], null, 'dashboard') ?: 'Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('base_price')
                            ->label(tr('fields.base_price', [], null, 'packages'))
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->step(0.01)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => static::calculateTotals($set, $get))
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('external_costs')
                            ->label(tr('fields.external_costs', [], null, 'packages'))
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->step(0.01)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => static::calculateTotals($set, $get))
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('worker_salary')
                            ->label(tr('fields.worker_salary', [], null, 'packages'))
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->step(0.01)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => static::calculateTotals($set, $get))
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('gov_fees')
                            ->label(tr('fields.gov_fees', [], null, 'packages'))
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->step(0.01)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => static::calculateTotals($set, $get))
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('tax_percent')
                            ->label(tr('fields.tax_percent', [], null, 'packages'))
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => static::calculateTotals($set, $get))
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('tax_value')
                            ->label(tr('fields.tax_value', [], null, 'packages'))
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('total')
                            ->label(tr('fields.total', [], null, 'packages'))
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    protected static function calculateTotals(callable $set, callable $get): void
    {
        $basePrice = (float) ($get('base_price') ?? 0);
        $externalCosts = (float) ($get('external_costs') ?? 0);
        $workerSalary = (float) ($get('worker_salary') ?? 0);
        $govFees = (float) ($get('gov_fees') ?? 0);

        $subtotal = $basePrice + $externalCosts + $workerSalary + $govFees;
        $taxPercent = (float) ($get('tax_percent') ?? 0);
        $taxValue = $subtotal * ($taxPercent / 100);
        $total = $subtotal + $taxValue;

        $set('tax_value', round($taxValue, 2));
        $set('total', round($total, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('fields.name', [], null, 'packages'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label(tr('fields.type', [], null, 'packages') ?: tr('common.type', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state) => tr("types.{$state}", [], null, 'packages'))
                    ->colors([
                        'primary' => 'recruitment',
                        'success' => 'rental',
                        'warning' => 'service_transfer',
                    ])
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('fields.status', [], null, 'packages'))
                    ->formatStateUsing(fn ($state) => tr("status.{$state}", [], null, 'packages'))
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('country.name_text')
                    ->label(tr('fields.country', [], null, 'packages'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->label(tr('fields.base_price', [], null, 'packages'))
                    ->money('SAR')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total')
                    ->label(tr('fields.total', [], null, 'packages'))
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('common.created_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(tr('fields.type', [], null, 'packages') ?: tr('common.type', [], null, 'dashboard'))
                    ->options([
                        'recruitment' => tr('types.recruitment', [], null, 'packages'),
                        'rental' => tr('types.rental', [], null, 'packages'),
                        'service_transfer' => tr('types.service_transfer', [], null, 'packages'),
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('fields.status', [], null, 'packages'))
                    ->options([
                        'active' => tr('status.active', [], null, 'packages'),
                        'inactive' => tr('status.inactive', [], null, 'packages'),
                    ]),

                Tables\Filters\SelectFilter::make('country_id')
                    ->label(tr('fields.country', [], null, 'packages'))
                    ->relationship('country', 'name_text')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(tr('common.view', [], null, 'dashboard')),
                Tables\Actions\EditAction::make()
                    ->label(tr('common.edit', [], null, 'dashboard'))
                    ->visible(fn (Package $record) => static::canEdit($record)),
                Tables\Actions\Action::make('export_pdf')
                    ->label(tr('buttons.export_pdf', [], null, 'packages'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (Package $record) => PackageResource::getUrl('view', ['record' => $record]) . '?export=pdf')
                    ->openUrlInNewTab()
                    ->visible(fn (Package $record) => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('packages.export_pdf') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->label(tr('common.delete', [], null, 'dashboard'))
                    ->visible(fn (Package $record) => static::canDelete($record)),
                Tables\Actions\RestoreAction::make()
                    ->label(tr('common.restore', [], null, 'dashboard') ?: 'Restore')
                    ->visible(fn (Package $record) => $record->trashed() && static::canRestore($record)),
                Tables\Actions\ForceDeleteAction::make()
                    ->label(tr('common.force_delete', [], null, 'dashboard') ?: 'Force Delete')
                    ->visible(fn (Package $record) => $record->trashed() && static::canForceDelete($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => static::canDeleteAny()),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn () => static::canRestoreAny()),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn () => static::canForceDeleteAny()),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PackageDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'view' => Pages\ViewPackage::route('/{record}'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('packages.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('packages.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('packages.view') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('packages.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('packages.delete') ?? false;
    }

    public static function canRestore(mixed $record): bool
    {
        return auth()->user()?->can('packages.restore') ?? false;
    }

    public static function canForceDelete(mixed $record): bool
    {
        return auth()->user()?->can('packages.force_delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('packages.delete') ?? false;
    }

    public static function canRestoreAny(): bool
    {
        return auth()->user()?->can('packages.restore') ?? false;
    }

    public static function canForceDeleteAny(): bool
    {
        return auth()->user()?->can('packages.force_delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
