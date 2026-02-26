<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\SalaryComponentResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\SalaryComponent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class SalaryComponentResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = SalaryComponent::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?string $navigationLabel = 'المكونات المالية';
    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(trans_dash('forms.salary_components.name') ?: 'Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label(trans_dash('forms.salary_components.code') ?: 'Code')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique code for this component'),

                        Forms\Components\Select::make('type')
                            ->label(trans_dash('forms.salary_components.type') ?: 'Type')
                            ->options([
                                'earning' => trans_dash('forms.salary_components.type.earning') ?: 'Earning',
                                'deduction' => trans_dash('forms.salary_components.type.deduction') ?: 'Deduction',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Toggle::make('is_fixed')
                            ->label(trans_dash('forms.salary_components.is_fixed') ?: 'Fixed Amount')
                            ->default(true),

                        Forms\Components\Toggle::make('taxable')
                            ->label(trans_dash('forms.salary_components.taxable') ?: 'Taxable')
                            ->default(false),

                        Forms\Components\TextInput::make('default_amount')
                            ->label(trans_dash('forms.salary_components.default_amount') ?: 'Default Amount')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0),

                        Forms\Components\Textarea::make('description')
                            ->label(trans_dash('forms.salary_components.description') ?: 'Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label(trans_dash('forms.salary_components.is_active') ?: 'Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(trans_dash('tables.salary_components.name') ?: 'Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label(trans_dash('tables.salary_components.code') ?: 'Code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label(trans_dash('tables.salary_components.type') ?: 'Type')
                    ->colors([
                        'success' => 'earning',
                        'danger' => 'deduction',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'earning' => trans_dash('tables.salary_components.earnings') ?: 'Earning',
                        'deduction' => trans_dash('tables.salary_components.deductions') ?: 'Deduction',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_fixed')
                    ->label(trans_dash('tables.salary_components.is_fixed') ?: 'Fixed')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('taxable')
                    ->label(trans_dash('tables.salary_components.taxable') ?: 'Taxable')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('default_amount')
                    ->label(trans_dash('tables.salary_components.default_amount') ?: 'Default Amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(trans_dash('tables.salary_components.status') ?: 'Status')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(trans_dash('tables.salary_components.type') ?: 'Type')
                    ->options([
                        'earning' => trans_dash('tables.salary_components.earnings') ?: 'Earnings',
                        'deduction' => trans_dash('tables.salary_components.deductions') ?: 'Deductions',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(trans_dash('tables.salary_components.status') ?: 'Status')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_components.update') ?? false),
                TableDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_components.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_components.delete') ?? false),
                ]),
            ])
            ->defaultSort('type')
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalaryComponents::route('/'),
            'create' => Pages\CreateSalaryComponent::route('/create'),
            'edit' => Pages\EditSalaryComponent::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('hr_components.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_components.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_components.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_components.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
