<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\SettingResource\Pages;
use App\Filament\Resources\MainCore\SettingResource\RelationManagers;
use App\Models\MainCore\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationGroup = 'MainCore';
    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        // Get available setting keys for selection
        $availableKeys = [
            'app.name' => 'Application Name',
            'app.url' => 'Application URL',
            'app.languages' => 'Available Languages',
            'app.default_language' => 'Default Language',
            'app.default_currency' => 'Default Currency',
            'app.timezone' => 'Timezone',
            'mail.from.address' => 'Mail From Address',
            'mail.from.name' => 'Mail From Name',
        ];

        return $form->schema([
            Forms\Components\Select::make('key')
                ->label('Setting Key')
                ->options($availableKeys)
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn ($state, callable $set) => $set('value', null))
                ->unique(ignoreRecord: true)
                ->helperText('Select a setting key. Common keys: ' . implode(', ', array_keys($availableKeys))),

            Forms\Components\TextInput::make('group')
                ->maxLength(50)
                ->default('app')
                ->helperText('Example: app, mail, payment, ui'),

            Forms\Components\Select::make('type')
                ->options([
                    'string' => 'String',
                    'int'    => 'Integer',
                    'bool'   => 'Boolean',
                    'array'  => 'Array/JSON',
                ])
                ->default('string')
                ->reactive(),

            // Dynamic value field based on key
            Forms\Components\TextInput::make('value')
                ->label('Value')
                ->required()
                ->visible(fn (Forms\Get $get) => 
                    $get('key') !== 'app.languages' && 
                    in_array($get('type'), ['string', 'int'])
                )
                ->maxLength(255),

            Forms\Components\Select::make('value')
                ->label('Value')
                ->required()
                ->visible(fn (Forms\Get $get) => $get('key') === 'app.languages')
                ->multiple()
                ->options(
                    \App\Models\MainCore\Language::where('is_active', true)
                        ->pluck('name', 'code')
                )
                ->searchable()
                ->preload()
                ->dehydrateStateUsing(fn ($state) => is_array($state) ? json_encode($state) : $state),

            Forms\Components\Textarea::make('value')
                ->label('Value')
                ->required()
                ->visible(fn (Forms\Get $get) => $get('type') === 'array')
                ->rows(4)
                ->helperText('For JSON/array, use valid JSON.'),

            Forms\Components\Toggle::make('value')
                ->label('Value')
                ->required()
                ->visible(fn (Forms\Get $get) => $get('type') === 'bool'),

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
