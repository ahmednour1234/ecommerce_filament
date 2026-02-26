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
use App\Filament\Actions\EditAction;


class UserPreferenceResource extends Resource
{
    protected static ?string $model = UserPreference::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = null;

    // No permissions required - accessible to all authenticated users
    public static function canViewAny(): bool
    {
        return true; // Always accessible
    }

    public static function canView(mixed $record): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(mixed $record): bool
    {
        return true;
    }

    public static function canDelete(mixed $record): bool
    {
        return true;
    }

    // Hide from sidebar navigation
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        // Common timezones
        $timezones = [
            'UTC' => 'UTC',
            'America/New_York' => 'America/New_York (EST)',
            'America/Chicago' => 'America/Chicago (CST)',
            'America/Denver' => 'America/Denver (MST)',
            'America/Los_Angeles' => 'America/Los_Angeles (PST)',
            'Europe/London' => 'Europe/London (GMT)',
            'Europe/Paris' => 'Europe/Paris (CET)',
            'Asia/Dubai' => 'Asia/Dubai (GST)',
            'Asia/Kolkata' => 'Asia/Kolkata (IST)',
            'Asia/Tokyo' => 'Asia/Tokyo (JST)',
            'Asia/Shanghai' => 'Asia/Shanghai (CST)',
            'Australia/Sydney' => 'Australia/Sydney (AEDT)',
        ];

        // Common date formats
        $dateFormats = [
            'Y-m-d' => 'YYYY-MM-DD (2024-01-15)',
            'd/m/Y' => 'DD/MM/YYYY (15/01/2024)',
            'm/d/Y' => 'MM/DD/YYYY (01/15/2024)',
            'd-m-Y' => 'DD-MM-YYYY (15-01-2024)',
            'Y/m/d' => 'YYYY/MM/DD (2024/01/15)',
            'd M Y' => 'DD MMM YYYY (15 Jan 2024)',
            'D, d M Y' => 'Day, DD MMM YYYY (Mon, 15 Jan 2024)',
        ];

        // Common time formats
        $timeFormats = [
            'H:i' => '24-hour (14:30)',
            'h:i A' => '12-hour with AM/PM (02:30 PM)',
            'H:i:s' => '24-hour with seconds (14:30:45)',
            'h:i:s A' => '12-hour with seconds and AM/PM (02:30:45 PM)',
        ];

        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->default(fn () => auth()->id())
                    ->disabled(fn () => !auth()->user()?->hasRole('super_admin')),
                Forms\Components\Select::make('language_id')
                    ->relationship('language', 'name')
                    ->label('Language')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('theme_id')
                    ->relationship('theme', 'name')
                    ->label('Theme')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('timezone')
                    ->label('Timezone')
                    ->options($timezones)
                    ->searchable()
                    ->default('UTC'),
                Forms\Components\Select::make('date_format')
                    ->label('Date Format')
                    ->options($dateFormats)
                    ->default('Y-m-d'),
                Forms\Components\Select::make('time_format')
                    ->label('Time Format')
                    ->options($timeFormats)
                    ->default('H:i'),
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
                EditAction::make(),
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

