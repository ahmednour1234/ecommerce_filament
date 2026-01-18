<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\NotificationTemplateResource\Pages;
use App\Filament\Resources\MainCore\NotificationTemplateResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\NotificationTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Concerns\NotificationModuleGate;

class NotificationTemplateResource extends Resource
{
    use TranslatableNavigation,NotificationModuleGate;
    protected static ?string $model = NotificationTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Notifications';
    protected static ?string $navigationTranslationKey = 'menu.notifications.templates';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label(tr('forms.notification_templates.key.label', [], null, 'dashboard'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('channel_id')
                    ->label(tr('forms.notification_templates.channel_id.label', [], null, 'dashboard'))
                    ->relationship('channel', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('language_id')
                    ->label(tr('forms.notification_templates.language_id.label', [], null, 'dashboard'))
                    ->relationship('language', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('subject')
                    ->label(tr('forms.notification_templates.subject.label', [], null, 'dashboard'))
                    ->maxLength(255),
                Forms\Components\Textarea::make('body_text')
                    ->label(tr('forms.notification_templates.body_text.label', [], null, 'dashboard'))
                    ->rows(5)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('body_html')
                    ->label(tr('forms.notification_templates.body_html.label', [], null, 'dashboard'))
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('variables')
                    ->label(tr('forms.notification_templates.variables.label', [], null, 'dashboard'))
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label(tr('forms.notification_templates.is_active.label', [], null, 'dashboard'))
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label(tr('tables.notification_templates.key', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('channel.name')
                    ->label(tr('tables.notification_templates.channel', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('language.name')
                    ->label(tr('tables.notification_templates.language', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label(tr('tables.notification_templates.subject', [], null, 'dashboard'))
                    ->searchable()
                    ->limit(50),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.notification_templates.is_active', [], null, 'dashboard'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.notification_templates.created_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(tr('tables.notification_templates.updated_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('channel_id')
                    ->label(tr('tables.notification_templates.filters.channel', [], null, 'dashboard'))
                    ->relationship('channel', 'name'),
                Tables\Filters\SelectFilter::make('language_id')
                    ->label(tr('tables.notification_templates.filters.language', [], null, 'dashboard'))
                    ->relationship('language', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.notification_templates.filters.is_active', [], null, 'dashboard'))
                    ->placeholder(tr('common.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.notification_templates.filters.active_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.notification_templates.filters.inactive_only', [], null, 'dashboard')),
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
            'index' => Pages\ListNotificationTemplates::route('/'),
            'create' => Pages\CreateNotificationTemplate::route('/create'),
            'edit' => Pages\EditNotificationTemplate::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('notification_templates.view_any') ?? true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

