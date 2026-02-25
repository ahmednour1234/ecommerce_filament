<?php

namespace App\Filament\Resources\Messaging;

use App\Filament\Resources\Messaging\ContactMessageResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Messaging\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'قسم الرسائل';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'رسائل الاتصال بنا';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(tr('forms.contact_messages.name', [], null, 'dashboard') ?: 'الاسم')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label(tr('forms.contact_messages.phone', [], null, 'dashboard') ?: 'الهاتف')
                    ->tel()
                    ->maxLength(50),

                Forms\Components\TextInput::make('email')
                    ->label(tr('forms.contact_messages.email', [], null, 'dashboard') ?: 'البريد')
                    ->email()
                    ->maxLength(255),

                Forms\Components\TextInput::make('subject')
                    ->label(tr('forms.contact_messages.subject', [], null, 'dashboard') ?: 'الموضوع')
                    ->maxLength(255),

                Forms\Components\Textarea::make('message')
                    ->label(tr('forms.contact_messages.message', [], null, 'dashboard') ?: 'الرسالة')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_read')
                    ->label(tr('tables.contact_messages.is_read', [], null, 'dashboard') ?: 'مقروء')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.contact_messages.name', [], null, 'dashboard') ?: 'الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label(tr('tables.contact_messages.phone', [], null, 'dashboard') ?: 'الهاتف')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(tr('tables.contact_messages.email', [], null, 'dashboard') ?: 'البريد')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label(tr('tables.contact_messages.subject', [], null, 'dashboard') ?: 'الموضوع')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('message')
                    ->label(tr('tables.contact_messages.message', [], null, 'dashboard') ?: 'الرسالة')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_read')
                    ->label(tr('tables.contact_messages.is_read', [], null, 'dashboard') ?: 'مقروء')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label(tr('tables.contact_messages.is_read', [], null, 'dashboard') ?: 'مقروء')
                    ->placeholder('الكل')
                    ->trueLabel('مقروء')
                    ->falseLabel('غير مقروء'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('mark_read')
                    ->label(tr('actions.mark_read', [], null, 'dashboard') ?: 'تحديد كمقروء')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (ContactMessage $record) => !$record->is_read)
                    ->action(fn (ContactMessage $record) => $record->update(['is_read' => true]))
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('mark_unread')
                    ->label(tr('actions.mark_unread', [], null, 'dashboard') ?: 'تحديد كغير مقروء')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (ContactMessage $record) => $record->is_read)
                    ->action(fn (ContactMessage $record) => $record->update(['is_read' => false]))
                    ->requiresConfirmation(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('delete_contact_messages') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('delete_contact_messages') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_any_contact_messages') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        return auth()->user()?->can('view_contact_messages') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('update_contact_messages') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('delete_contact_messages') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
