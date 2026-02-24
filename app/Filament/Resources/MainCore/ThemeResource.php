<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\ThemeResource\Pages;
use App\Filament\Resources\MainCore\ThemeResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\Theme;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;



class ThemeResource extends Resource
{
    use TranslatableNavigation;
    protected static ?string $model = Theme::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'app_management';
    protected static ?string $navigationLabel = 'النواة الرئيسية';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Theme Information')
                ->description('Configure the theme name and default status')
                ->icon('heroicon-o-information-circle')
                ->schema([
            Forms\Components\TextInput::make('name')
                        ->label('Theme Name')
                ->required()
                        ->maxLength(100)
                        ->placeholder('Enter theme name')
                        ->columnSpanFull(),
                    
                    Forms\Components\Toggle::make('is_default')
                        ->label('Set as Default Theme')
                        ->helperText('Only one theme can be default at a time')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Color Scheme')
                ->description('Customize the color palette for your theme')
                ->icon('heroicon-o-swatch')
                ->schema([
            Forms\Components\ColorPicker::make('primary_color')
                        ->label('Primary Color')
                        ->required()
                        ->helperText('Main color used throughout the dashboard')
                        ->default('#F59E0B'),

                    Forms\Components\ColorPicker::make('secondary_color')
                        ->label('Secondary Color')
                        ->helperText('Secondary accent color')
                        ->default('#0EA5E9'),
                    
                    Forms\Components\ColorPicker::make('accent_color')
                        ->label('Accent Color')
                        ->helperText('Accent color for highlights')
                        ->default('#22C55E'),
                ])
                ->columns(3),

            Forms\Components\Section::make('Branding')
                ->description('Upload logos for light and dark modes')
                ->icon('heroicon-o-photo')
                ->schema([
                    \App\Filament\Forms\Components\FileUpload::makeImage('logo_light')
                        ->label('Light Mode Logo')
                ->directory('themes/logos')
                        ->helperText('Logo displayed on light backgrounds (recommended: transparent PNG, max 5MB)')
                        ->imagePreviewHeight('200')
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            null,
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->columnSpanFull(),

                    \App\Filament\Forms\Components\FileUpload::makeImage('logo_dark')
                        ->label('Dark Mode Logo')
                ->directory('themes/logos')
                        ->helperText('Logo displayed on dark backgrounds (recommended: transparent PNG, max 5MB)')
                        ->imagePreviewHeight('200')
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            null,
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_light')
                    ->label('Light Logo')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(url('/images/placeholder-logo.png')),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Theme Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                
                Tables\Columns\ColorColumn::make('primary_color')
                    ->label('Primary Color'),
                
                Tables\Columns\ColorColumn::make('secondary_color')
                    ->label('Secondary Color'),
                
                Tables\Columns\ColorColumn::make('accent_color')
                    ->label('Accent Color'),
                
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
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
