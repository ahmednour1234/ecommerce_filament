<?php

namespace App\Filament\Resources\Messaging;

use App\Filament\Resources\Messaging\SmsTemplateResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Messaging\SmsTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class SmsTemplateResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = SmsTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'قسم الرسائل';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'قوالب الرسائل';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_ar')
                    ->label(tr('forms.sms_templates.name_ar', [], null, 'dashboard') ?: 'الاسم (عربي)')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('body_ar')
                    ->label(tr('forms.sms_templates.body_ar', [], null, 'dashboard') ?: 'النص (عربي)')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('name_en')
                    ->label(tr('forms.sms_templates.name_en', [], null, 'dashboard') ?: 'الاسم (إنجليزي)')
                    ->maxLength(255),

                Forms\Components\Textarea::make('body_en')
                    ->label(tr('forms.sms_templates.body_en', [], null, 'dashboard') ?: 'النص (إنجليزي)')
                    ->rows(5)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->label(tr('forms.sms_templates.is_active', [], null, 'dashboard') ?: 'نشط')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label(tr('tables.sms_templates.name_ar', [], null, 'dashboard') ?: 'الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('body_ar')
                    ->label(tr('tables.sms_templates.body_ar', [], null, 'dashboard') ?: 'النص')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.sms_templates.is_active', [], null, 'dashboard') ?: 'نشط')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.sms_templates.is_active', [], null, 'dashboard') ?: 'نشط')
                    ->placeholder('الكل')
                    ->trueLabel('نشط')
                    ->falseLabel('غير نشط'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                TableDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('manage_sms_templates') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('manage_sms_templates') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('manage_sms_templates') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('manage_sms_templates') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
