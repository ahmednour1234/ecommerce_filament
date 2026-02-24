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
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'الترجمات';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->label(tr('forms.translations.key.label', [], null, 'dashboard'))
                    ->helperText(tr('forms.translations.key.helper', [], null, 'dashboard')),
                Forms\Components\TextInput::make('group')
                    ->required()
                    ->maxLength(255)
                    ->default('dashboard')
                    ->label(tr('forms.translations.group.label', [], null, 'dashboard'))
                    ->helperText(tr('forms.translations.group.helper', [], null, 'dashboard')),
                Forms\Components\Select::make('language_id')
                    ->relationship('language', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label(tr('forms.translations.language_id.label', [], null, 'dashboard')),
                Forms\Components\Textarea::make('value')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull()
                    ->label(tr('forms.translations.value.label', [], null, 'dashboard')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label(tr('tables.translations.key', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group')
                    ->label(tr('tables.translations.group', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('language.name')
                    ->label(tr('tables.translations.language', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('value')
                    ->label(tr('tables.translations.value', [], null, 'dashboard'))
                    ->searchable()
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.translations.created_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(tr('tables.translations.updated_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label(tr('tables.translations.filters.group', [], null, 'dashboard'))
                    ->options([
                        'dashboard' => tr('tables.translations.filters.group_options.dashboard', [], null, 'dashboard'),
                        'auth' => tr('tables.translations.filters.group_options.auth', [], null, 'dashboard'),
                        'validation' => tr('tables.translations.filters.group_options.validation', [], null, 'dashboard'),
                        'common' => tr('tables.translations.filters.group_options.common', [], null, 'dashboard'),
                    ]),
                Tables\Filters\SelectFilter::make('language_id')
                    ->label(tr('tables.translations.filters.language', [], null, 'dashboard'))
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

