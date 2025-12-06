<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\MainCore\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationGroup = 'MainCore';
    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')
                ->required()
                ->maxLength(191)
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('group')
                ->maxLength(50)
                ->helperText('Example: app, mail, payment, ui'),

            Forms\Components\Select::make('type')
                ->options([
                    'string' => 'String',
                    'int'    => 'Integer',
                    'bool'   => 'Boolean',
                    'array'  => 'Array/JSON',
                ])
                ->default('string'),

            Forms\Components\Textarea::make('value')
                ->rows(4)
                ->helperText('For JSON/array, use valid JSON.'),

            Forms\Components\Toggle::make('is_public')->label('Public'),
            Forms\Components\Toggle::make('autoload')->label('Autoload'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->searchable(),
                Tables\Columns\TextColumn::make('group'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\IconColumn::make('is_public')->boolean()->label('Public'),
                Tables\Columns\IconColumn::make('autoload')->boolean()->label('Autoload'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('settings.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('settings.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('settings.delete') ?? false),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit'   => Pages\EditSetting::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('settings.view_any') ?? false;
    }
    public static function canCreate(): bool
    {
        return auth()->user()?->can('settings.create') ?? false;
    }
    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('settings.update') ?? false;
    }
    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('settings.delete') ?? false;
    }
    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('settings.delete') ?? false;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
