<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 30;
    protected static ?string $navigationTranslationKey = 'menu.system.permissions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Permission Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('guard_name')
                    ->label('Guard')
                    ->default('web')
                    ->required()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('name')
                    ->label('Permission')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('permissions.update') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('permissions.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('permissions.delete') ?? false),
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
            'index'  => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit'   => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    /* ================== صلاحيات Filament مبنية على Spatie ================== */

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('permissions.view_any') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        return auth()->user()?->can('permissions.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('permissions.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('permissions.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('permissions.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('permissions.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
