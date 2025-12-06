<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\ThemeResource\Pages;
use App\Filament\Resources\MainCore\ThemeResource\RelationManagers;
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
    protected static ?string $model = Theme::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('primary_color')
                    ->maxLength(20)
                    ->default(null),
                Forms\Components\TextInput::make('secondary_color')
                    ->maxLength(20)
                    ->default(null),
                Forms\Components\TextInput::make('accent_color')
                    ->maxLength(20)
                    ->default(null),
                Forms\Components\TextInput::make('logo_light')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('logo_dark')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Toggle::make('is_default')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('primary_color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('secondary_color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('accent_color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('logo_light')
                    ->searchable(),
                Tables\Columns\TextColumn::make('logo_dark')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListThemes::route('/'),
            'create' => Pages\CreateTheme::route('/create'),
            'edit' => Pages\EditTheme::route('/{record}/edit'),
        ];
    }
}
