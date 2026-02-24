<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\SettingResource\Pages;
use App\Filament\Resources\MainCore\SettingResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
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
    use TranslatableNavigation;
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'إعدادات النظام';
    protected static ?int $navigationSort = 5;

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
                ->label(tr('forms.settings.key.label', [], null, 'dashboard'))
                ->options($availableKeys)
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn ($state, callable $set) => $set('value', null))
                ->unique(ignoreRecord: true)
                ->helperText(tr('forms.settings.key.helper', [], null, 'dashboard')),

            Forms\Components\TextInput::make('group')
                ->label(tr('forms.settings.group.label', [], null, 'dashboard'))
                ->maxLength(50)
                ->default('app')
                ->helperText(tr('forms.settings.group.helper', [], null, 'dashboard')),

            Forms\Components\Select::make('type')
                ->label(tr('forms.settings.type.label', [], null, 'dashboard'))
                ->options([
                    'string' => tr('forms.settings.type.options.string', [], null, 'dashboard'),
                    'int'    => tr('forms.settings.type.options.int', [], null, 'dashboard'),
                    'bool'   => tr('forms.settings.type.options.bool', [], null, 'dashboard'),
                    'array'  => tr('forms.settings.type.options.array', [], null, 'dashboard'),
                ])
                ->default('string')
                ->reactive(),

            // Dynamic value field based on key
            Forms\Components\TextInput::make('value')
                ->label(tr('forms.settings.value.label', [], null, 'dashboard'))
                ->required()
                ->visible(fn (Forms\Get $get) =>
                    $get('key') !== 'app.languages' &&
                    in_array($get('type'), ['string', 'int'])
                )
                ->maxLength(255),

            Forms\Components\Select::make('value')
                ->label(tr('forms.settings.value.label', [], null, 'dashboard'))
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
                ->label(tr('forms.settings.value.label', [], null, 'dashboard'))
                ->required()
                ->visible(fn (Forms\Get $get) => $get('type') === 'array')
                ->rows(4)
                ->helperText(tr('forms.settings.value.helper', [], null, 'dashboard')),

            Forms\Components\Toggle::make('value')
                ->label(tr('forms.settings.value.label', [], null, 'dashboard'))
                ->required()
                ->visible(fn (Forms\Get $get) => $get('type') === 'bool'),

            Forms\Components\Toggle::make('is_public')->label(tr('forms.settings.is_public.label', [], null, 'dashboard')),
            Forms\Components\Toggle::make('autoload')->label(tr('forms.settings.autoload.label', [], null, 'dashboard')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label(tr('tables.settings.key', [], null, 'dashboard'))->searchable(),
                Tables\Columns\TextColumn::make('group')->label(tr('tables.settings.group', [], null, 'dashboard')),
                Tables\Columns\TextColumn::make('type')->label(tr('tables.settings.type', [], null, 'dashboard')),
                Tables\Columns\IconColumn::make('is_public')->boolean()->label(tr('tables.settings.is_public', [], null, 'dashboard')),
                Tables\Columns\IconColumn::make('autoload')->boolean()->label(tr('tables.settings.autoload', [], null, 'dashboard')),
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
