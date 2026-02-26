<?php

namespace App\Filament\Resources\Messaging;

use App\Filament\Resources\Messaging\MessageContactResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Messaging\MessageContact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class MessageContactResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = MessageContact::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationGroup = 'قسم الرسائل';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'جهات الاتصال';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_ar')
                    ->label(tr('forms.message_contacts.name_ar', [], null, 'dashboard') ?: 'الاسم')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label(tr('forms.message_contacts.phone', [], null, 'dashboard') ?: 'الرقم')
                    ->required()
                    ->tel()
                    ->maxLength(50),

                Forms\Components\TextInput::make('source')
                    ->label(tr('forms.message_contacts.source', [], null, 'dashboard') ?: 'المصدر')
                    ->maxLength(255)
                    ->default('manual'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label(tr('tables.message_contacts.name_ar', [], null, 'dashboard') ?: 'الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label(tr('tables.message_contacts.phone', [], null, 'dashboard') ?: 'الرقم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('source')
                    ->label(tr('forms.message_contacts.source', [], null, 'dashboard') ?: 'المصدر')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                TableDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('delete_message_contacts') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('delete_message_contacts') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_any_message_contacts') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_message_contacts') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('update_message_contacts') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('delete_message_contacts') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
