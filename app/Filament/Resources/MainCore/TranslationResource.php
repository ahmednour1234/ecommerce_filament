<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\TranslationResource\Pages;
use App\Filament\Resources\MainCore\TranslationResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\Translation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TranslationResource extends Resource
{
    use TranslatableNavigation;
    protected static ?string $model = Translation::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationTranslationKey = 'menu.settings.translations';

    protected static ?string $navigationLabel = 'Translations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->label('Translation Key')
                    ->helperText('e.g., dashboard.welcome, auth.login'),
                Forms\Components\TextInput::make('group')
                    ->required()
                    ->maxLength(255)
                    ->default('dashboard')
                    ->label('Group')
                    ->helperText('Group name like: dashboard, auth, validation, etc.'),
                Forms\Components\Select::make('language_id')
                    ->relationship('language', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Language'),
                Forms\Components\Textarea::make('value')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull()
                    ->label('Translation Value'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('language.name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('value')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),
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
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'dashboard' => 'Dashboard',
                        'auth' => 'Authentication',
                        'validation' => 'Validation',
                        'common' => 'Common',
                    ]),
                Tables\Filters\SelectFilter::make('language_id')
                    ->relationship('language', 'name'),
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
            'index' => Pages\ListTranslations::route('/'),
            'create' => Pages\CreateTranslation::route('/create'),
            'edit' => Pages\EditTranslation::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('translations.view_any') ?? true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

