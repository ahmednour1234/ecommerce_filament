<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\LanguageResource\Pages;
use App\Filament\Resources\MainCore\LanguageResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class LanguageResource extends Resource
{
    use TranslatableNavigation;
    protected static ?string $model = Language::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'اللغات';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->label(tr('forms.languages.code.label', [], null, 'dashboard'))
                ->required()
                ->maxLength(10)
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('name')
                ->label(tr('forms.languages.name.label', [], null, 'dashboard'))
                ->required()
                ->maxLength(100),

            Forms\Components\TextInput::make('native_name')
                ->label(tr('forms.languages.native_name.label', [], null, 'dashboard'))
                ->required()
                ->maxLength(100),

            Forms\Components\Toggle::make('is_default')
                ->label(tr('forms.languages.is_default.label', [], null, 'dashboard'))
                ->helperText(tr('forms.languages.is_default.helper', [], null, 'dashboard')),

            Forms\Components\Toggle::make('is_active')
                ->label(tr('forms.languages.is_active.label', [], null, 'dashboard'))
                ->default(true),

            Forms\Components\Select::make('direction')
                ->label(tr('forms.languages.direction.label', [], null, 'dashboard'))
                ->options([
                    'ltr' => tr('forms.languages.direction.options.ltr', [], null, 'dashboard'),
                    'rtl' => tr('forms.languages.direction.options.rtl', [], null, 'dashboard'),
                ])
                ->default('ltr')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label(tr('tables.languages.code', [], null, 'dashboard'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label(tr('tables.languages.name', [], null, 'dashboard'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('native_name')->label(tr('tables.languages.native_name', [], null, 'dashboard'))->sortable()->searchable(),
                Tables\Columns\IconColumn::make('is_default')->boolean()->label(tr('tables.languages.is_default', [], null, 'dashboard')),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label(tr('tables.languages.is_active', [], null, 'dashboard')),
                Tables\Columns\TextColumn::make('direction')->label(tr('tables.languages.direction', [], null, 'dashboard')),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('languages.update') ?? false),
                TableDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('languages.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('languages.delete') ?? false),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit'   => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }

    /* صلاحيات مبنية على Spatie */
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('languages.view_any') ?? false;
    }
    public static function canCreate(): bool
    {
        return auth()->user()?->can('languages.create') ?? false;
    }
    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('languages.update') ?? false;
    }
    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('languages.delete') ?? false;
    }
    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('languages.delete') ?? false;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
