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

class CostCenterResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = CostCenter::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationTranslationKey = 'menu.accounting.cost_centers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Code')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique code for the cost center'),

                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'department' => 'Department',
                                'project' => 'Project',
                                'location' => 'Location',
                                'other' => 'Other',
                            ])
                            ->searchable()
                            ->nullable()
                            ->helperText('Type of cost center (e.g., department, project, location)'),

                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Cost Center')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Optional: Select a parent cost center if this is a sub-cost center'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
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
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
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
                    ->label('Parent')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'department' => 'Department',
                        'project' => 'Project',
                        'location' => 'Location',
                        'other' => 'Other',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent Cost Center')
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

