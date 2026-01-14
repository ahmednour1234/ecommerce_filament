<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\CostCenterResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\CostCenter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Concerns\AccountingModuleGate;

class CostCenterResource extends Resource
{
    use TranslatableNavigation,AccountingModuleGate;

    protected static ?string $model = CostCenter::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.cost_centers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('forms.cost_centers.sections.basic_information', [], null, 'dashboard'))
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(tr('forms.cost_centers.code.label', [], null, 'dashboard'))
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText(tr('forms.cost_centers.code.helper', [], null, 'dashboard')),

                        Forms\Components\TextInput::make('name')
                            ->label(tr('forms.cost_centers.name.label', [], null, 'dashboard'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label(tr('forms.cost_centers.type.label', [], null, 'dashboard'))
                            ->options([
                                'department' => tr('forms.cost_centers.type.options.department', [], null, 'dashboard'),
                                'project' => tr('forms.cost_centers.type.options.project', [], null, 'dashboard'),
                                'location' => tr('forms.cost_centers.type.options.location', [], null, 'dashboard'),
                                'other' => tr('forms.cost_centers.type.options.other', [], null, 'dashboard'),
                            ])
                            ->searchable()
                            ->nullable()
                            ->helperText(tr('forms.cost_centers.type.helper', [], null, 'dashboard')),

                        Forms\Components\Select::make('parent_id')
                            ->label(tr('forms.cost_centers.parent_id.label', [], null, 'dashboard'))
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText(tr('forms.cost_centers.parent_id.helper', [], null, 'dashboard')),

                        Forms\Components\Textarea::make('description')
                            ->label(tr('forms.cost_centers.description.label', [], null, 'dashboard'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label(tr('forms.cost_centers.is_active.label', [], null, 'dashboard'))
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(tr('tables.cost_centers.code', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.cost_centers.name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(tr('tables.cost_centers.type', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state) => $state ? tr('forms.cost_centers.type.options.' . $state, [], null, 'dashboard') : '')
                    ->badge()
                    ->colors([
                        'primary' => 'department',
                        'success' => 'project',
                        'warning' => 'location',
                        'gray' => 'other',
                    ])
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label(tr('tables.cost_centers.parent', [], null, 'dashboard'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.cost_centers.active', [], null, 'dashboard'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(tr('tables.cost_centers.filters.type', [], null, 'dashboard'))
                    ->options([
                        'department' => tr('forms.cost_centers.type.options.department', [], null, 'dashboard'),
                        'project' => tr('forms.cost_centers.type.options.project', [], null, 'dashboard'),
                        'location' => tr('forms.cost_centers.type.options.location', [], null, 'dashboard'),
                        'other' => tr('forms.cost_centers.type.options.other', [], null, 'dashboard'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.cost_centers.filters.active', [], null, 'dashboard'))
                    ->placeholder(tr('tables.cost_centers.filters.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.cost_centers.filters.active_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.cost_centers.filters.inactive_only', [], null, 'dashboard')),

                Tables\Filters\SelectFilter::make('parent_id')
                    ->label(tr('tables.cost_centers.filters.parent_cost_center', [], null, 'dashboard'))
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('cost_centers.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('cost_centers.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('cost_centers.delete') ?? false),
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
            'index' => Pages\ListCostCenters::route('/'),
            'create' => Pages\CreateCostCenter::route('/create'),
            'edit' => Pages\EditCostCenter::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('cost_centers.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('cost_centers.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('cost_centers.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('cost_centers.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('cost_centers.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

