<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\UserPreferenceResource\Pages;
use App\Filament\Resources\MainCore\UserPreferenceResource\RelationManagers;
use App\Models\MainCore\UserPreference;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserPreferenceResource extends Resource
{
    protected static ?string $model = UserPreference::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'MainCore';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('language_id')
                    ->relationship('language', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('theme_id')
                    ->relationship('theme', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('timezone')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('date_format')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('time_format')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('language.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('theme.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('timezone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_format')
                    ->searchable(),
                Tables\Columns\TextColumn::make('time_format')
                    ->searchable(),
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
            'index' => Pages\ListUserPreferences::route('/'),
            'create' => Pages\CreateUserPreference::route('/create'),
            'edit' => Pages\EditUserPreference::route('/{record}/edit'),
        ];
    }
}

