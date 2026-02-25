<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\CommissionResource\Pages;
use App\Filament\Resources\HR\CommissionResource\RelationManagers\EmployeeCommissionTiersRelationManager;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\Commission;
use App\Models\HR\CommissionType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommissionResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = Commission::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?string $navigationLabel = 'عمولات الموظفين';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label(tr('fields.commission.name_ar', [], null, 'dashboard') ?: 'Commission Name (Arabic)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name_en')
                            ->label(tr('fields.commission.name_en', [], null, 'dashboard') ?: 'Commission Name (English)')
                            ->maxLength(255),

                        Forms\Components\Select::make('commission_type_id')
                            ->label(tr('fields.commission.commission_type_id', [], null, 'dashboard') ?: 'Commission Type')
                            ->relationship('commissionType', 'name_ar', fn (Builder $query) => $query->active())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('value')
                            ->label(tr('fields.commission.value', [], null, 'dashboard') ?: 'Value')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->prefix('$'),

                        Forms\Components\Toggle::make('is_active')
                            ->label(tr('fields.commission.is_active', [], null, 'dashboard') ?: 'Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label(tr('tables.hr_commissions.name_ar', [], null, 'dashboard') ?: 'Commission Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('commissionType.name_ar')
                    ->label(tr('tables.hr_commissions.commission_type', [], null, 'dashboard') ?: 'Type')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('value')
                    ->label(tr('tables.hr_commissions.value', [], null, 'dashboard') ?: 'Value')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.hr_commissions.is_active', [], null, 'dashboard') ?: 'Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tiers_count')
                    ->label('Tiers')
                    ->counts('tiers')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('assignments_count')
                    ->label('Assignments')
                    ->counts('assignments')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('commission_type_id')
                    ->label(tr('fields.commission.commission_type_id', [], null, 'dashboard') ?: 'Commission Type')
                    ->relationship('commissionType', 'name_ar')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.hr_commissions.is_active', [], null, 'dashboard') ?: 'Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_commissions.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_commissions.delete') ?? false),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_commissions.restore') ?? false),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_commissions.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_commissions.delete') ?? false),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_commissions.restore') ?? false),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_commissions.delete') ?? false),
                ]),
            ])
            ->defaultSort('name_ar');
    }

    public static function getRelations(): array
    {
        return [
            EmployeeCommissionTiersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommissions::route('/'),
            'create' => Pages\CreateCommission::route('/create'),
            'edit' => Pages\EditCommission::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_commissions.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_commissions.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_commissions.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_commissions.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('hr_commissions.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
