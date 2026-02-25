<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\EmployeeCommissionTierResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\EmployeeCommissionTier;
use App\Models\HR\Commission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeCommissionTierResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = EmployeeCommissionTier::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?string $navigationLabel = 'شرائح عمولات الموظفين';
    protected static ?int $navigationSort = 31;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('commission_id')
                            ->label(tr('fields.commission_tier.commission_id', [], null, 'dashboard') ?: 'Commission')
                            ->relationship('commission', 'name_ar', fn (Builder $query) => $query->active())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('contracts_from')
                            ->label(tr('fields.commission_tier.contracts_from', [], null, 'dashboard') ?: 'Contracts From')
                            ->numeric()
                            ->required()
                            ->minValue(0),

                        Forms\Components\TextInput::make('contracts_to')
                            ->label(tr('fields.commission_tier.contracts_to', [], null, 'dashboard') ?: 'Contracts To')
                            ->numeric()
                            ->required()
                            ->minValue(0),

                        Forms\Components\TextInput::make('amount_per_contract')
                            ->label(tr('fields.commission_tier.amount_per_contract', [], null, 'dashboard') ?: 'Amount Per Contract')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->prefix('$'),

                        Forms\Components\Toggle::make('is_active')
                            ->label(tr('fields.commission_tier.is_active', [], null, 'dashboard') ?: 'Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('commission.name_ar')
                    ->label(tr('tables.hr_employee_commission_tiers.commission', [], null, 'dashboard') ?: 'Commission')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contracts_from')
                    ->label(tr('tables.hr_employee_commission_tiers.contracts_from', [], null, 'dashboard') ?: 'From')
                    ->sortable(),

                Tables\Columns\TextColumn::make('contracts_to')
                    ->label(tr('tables.hr_employee_commission_tiers.contracts_to', [], null, 'dashboard') ?: 'To')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount_per_contract')
                    ->label(tr('tables.hr_employee_commission_tiers.amount_per_contract', [], null, 'dashboard') ?: 'Amount Per Contract')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.hr_employee_commission_tiers.is_active', [], null, 'dashboard') ?: 'Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('commission_id')
                    ->label(tr('fields.commission_tier.commission_id', [], null, 'dashboard') ?: 'Commission')
                    ->relationship('commission', 'name_ar')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.hr_employee_commission_tiers.is_active', [], null, 'dashboard') ?: 'Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.delete') ?? false),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.restore') ?? false),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.delete') ?? false),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.restore') ?? false),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.delete') ?? false),
                ]),
            ])
            ->defaultSort('commission_id');
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
            'index' => Pages\ListEmployeeCommissionTiers::route('/'),
            'create' => Pages\CreateEmployeeCommissionTier::route('/create'),
            'edit' => Pages\EditEmployeeCommissionTier::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_employee_commission_tiers.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_employee_commission_tiers.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_employee_commission_tiers.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_employee_commission_tiers.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('hr_employee_commission_tiers.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
