<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\BloodTypeResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\BloodType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class BloodTypeResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = BloodType::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?string $navigationLabel = 'فصائل الدم';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(tr('forms.hr_blood_types.name.label', [], null, 'dashboard') ?: 'Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label(tr('forms.hr_blood_types.code.label', [], null, 'dashboard') ?: 'Code')
                            ->maxLength(10)
                            ->nullable(),

                        Forms\Components\Toggle::make('active')
                            ->label(tr('forms.hr_blood_types.active.label', [], null, 'dashboard') ?: 'Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.hr_blood_types.name', [], null, 'dashboard') ?: 'Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label(tr('tables.hr_blood_types.code', [], null, 'dashboard') ?: 'Code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('active')
                    ->label(tr('tables.hr_blood_types.active', [], null, 'dashboard') ?: 'Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.hr_blood_types.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(tr('tables.hr_blood_types.updated_at', [], null, 'dashboard') ?: 'Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label(tr('tables.hr_blood_types.filters.active', [], null, 'dashboard') ?: 'Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_blood_types.update') ?? false),
                TableDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_blood_types.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_blood_types.delete') ?? false),
                ]),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListBloodTypes::route('/'),
            'create' => Pages\CreateBloodType::route('/create'),
            'edit' => Pages\EditBloodType::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_blood_types.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_blood_types.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_blood_types.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_blood_types.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('hr_blood_types.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

