<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'system';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'sidebar.system.users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(tr('forms.users.name', [], null, 'dashboard') ?: 'Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label(tr('forms.users.email', [], null, 'dashboard') ?: 'Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('password')
                    ->label(tr('forms.users.password', [], null, 'dashboard') ?: 'Password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create'),

                Forms\Components\Select::make('roles')
                    ->label(tr('forms.users.roles', [], null, 'dashboard') ?: 'Roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->options(Role::pluck('name', 'id'))
                    ->preload(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(tr('tables.common.id', [], null, 'dashboard') ?: 'Id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.users.name', [], null, 'dashboard') ?: 'Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(tr('tables.users.email', [], null, 'dashboard') ?: 'Email')
                    ->searchable(),
                Tables\Columns\TagsColumn::make('roles.name')
                    ->label(tr('tables.users.roles', [], null, 'dashboard') ?: 'Roles'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.common.created_at', [], null, 'dashboard') ?: 'Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /*** صلاحيات Filament مبنية على Spatie ***/
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('users.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('users.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('users.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('users.delete') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
