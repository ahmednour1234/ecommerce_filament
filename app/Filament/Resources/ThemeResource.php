<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ThemeResource\Pages;
use App\Models\MainCore\Theme;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ThemeResource extends Resource
{
    protected static ?string $model = Theme::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'MainCore';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(100),

            Forms\Components\ColorPicker::make('primary_color')
                ->required(),

            Forms\Components\ColorPicker::make('secondary_color'),
            Forms\Components\ColorPicker::make('accent_color'),

            Forms\Components\FileUpload::make('logo_light')
                ->directory('themes/logos')
                ->image()
                ->imagePreviewHeight('100'),

            Forms\Components\FileUpload::make('logo_dark')
                ->directory('themes/logos')
                ->image()
                ->imagePreviewHeight('100'),

            Forms\Components\Toggle::make('is_default')
                ->label('Default theme'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\ColorColumn::make('primary_color'),
                Tables\Columns\IconColumn::make('is_default')->boolean()->label('Default'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('themes.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('themes.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('themes.delete') ?? false),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListThemes::route('/'),
            'create' => Pages\CreateTheme::route('/create'),
            'edit'   => Pages\EditTheme::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('themes.view_any') ?? false;
    }
    public static function canCreate(): bool
    {
        return auth()->user()?->can('themes.create') ?? false;
    }
    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('themes.update') ?? false;
    }
    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('themes.delete') ?? false;
    }
    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('themes.delete') ?? false;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
