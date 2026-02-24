<?php

namespace App\Filament\Resources\Recruitment;

use App\Filament\Resources\Recruitment\NationalityResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Recruitment\Nationality;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NationalityResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Nationality::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'المرشحين';
    protected static ?string $navigationLabel = 'الجنسيات';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label(tr('recruitment.fields.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name_en')
                            ->label(tr('recruitment.fields.name_en', [], null, 'dashboard') ?: 'Name (English)')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('code')
                            ->label(tr('recruitment.fields.code', [], null, 'dashboard') ?: 'Code')
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_active')
                            ->label(tr('recruitment.fields.active', [], null, 'dashboard') ?: 'Active')
                            ->default(true)
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label(tr('recruitment.fields.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_en')
                    ->label(tr('recruitment.fields.name_en', [], null, 'dashboard') ?: 'Name (English)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label(tr('recruitment.fields.code', [], null, 'dashboard') ?: 'Code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('recruitment.fields.active', [], null, 'dashboard') ?: 'Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('recruitment.fields.active', [], null, 'dashboard') ?: 'Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(tr('actions.view', [], null, 'dashboard') ?: 'View'),
                Tables\Actions\EditAction::make()
                    ->label(tr('actions.edit', [], null, 'dashboard') ?: 'Edit')
                    ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('recruitment.nationalities.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->label(tr('actions.delete', [], null, 'dashboard') ?: 'Delete')
                    ->visible(fn () => auth()->user()?->can('recruitment.nationalities.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('recruitment.nationalities.delete') ?? false),
                ]),
            ])
            ->defaultSort('name_en');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNationalities::route('/'),
            'create' => Pages\CreateNationality::route('/create'),
            'view' => Pages\ViewNationality::route('/{record}'),
            'edit' => Pages\EditNationality::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.nationalities.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.nationalities.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.nationalities.view') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.nationalities.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('recruitment.nationalities.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
