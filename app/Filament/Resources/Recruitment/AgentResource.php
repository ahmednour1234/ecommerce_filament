<?php

namespace App\Filament\Resources\Recruitment;

use App\Filament\Resources\Recruitment\AgentResource\Pages;
use App\Filament\Resources\Recruitment\AgentResource\RelationManagers;
use App\Filament\Resources\Recruitment\AgentLaborPriceResource;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\Country;
use App\Models\Recruitment\Agent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class AgentResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Agent::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'agents';
    protected static ?string $navigationLabel = 'الوكلاء';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('recruitment.sections.basic_info', [], null, 'dashboard') ?: 'Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(tr('recruitment.fields.code', [], null, 'dashboard') ?: 'Code')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name_ar')
                            ->label(tr('recruitment.fields.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name_en')
                            ->label(tr('recruitment.fields.name_en', [], null, 'dashboard') ?: 'Name (English)')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('email')
                            ->label(tr('recruitment.fields.email', [], null, 'dashboard') ?: 'Email')
                            ->email()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\Select::make('country_id')
                            ->label(tr('recruitment.fields.country', [], null, 'dashboard') ?: 'Country')
                            ->options(function () {
                                return Country::where('is_active', true)
                                    ->get()
                                    ->pluck('name_text', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment.sections.contact', [], null, 'dashboard') ?: 'Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('phone_1')
                            ->label(tr('recruitment.fields.phone_1', [], null, 'dashboard') ?: 'Phone 1')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('phone_2')
                            ->label(tr('recruitment.fields.phone_2', [], null, 'dashboard') ?: 'Phone 2')
                            ->maxLength(50)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('mobile')
                            ->label(tr('recruitment.fields.mobile', [], null, 'dashboard') ?: 'Mobile')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('fax')
                            ->label(tr('recruitment.fields.fax', [], null, 'dashboard') ?: 'Fax')
                            ->maxLength(50)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('responsible_name')
                            ->label(tr('recruitment.fields.responsible_name', [], null, 'dashboard') ?: 'Responsible Name')
                            ->maxLength(255)
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment.sections.location', [], null, 'dashboard') ?: 'Location')
                    ->schema([
                        Forms\Components\TextInput::make('city_ar')
                            ->label(tr('recruitment.fields.city_ar', [], null, 'dashboard') ?: 'City (Arabic)')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('city_en')
                            ->label(tr('recruitment.fields.city_en', [], null, 'dashboard') ?: 'City (English)')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('address_ar')
                            ->label(tr('recruitment.fields.address_ar', [], null, 'dashboard') ?: 'Address (Arabic)')
                            ->rows(2)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('address_en')
                            ->label(tr('recruitment.fields.address_en', [], null, 'dashboard') ?: 'Address (English)')
                            ->rows(2)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment.sections.identity', [], null, 'dashboard') ?: 'Identity & Banking')
                    ->schema([
                        Forms\Components\TextInput::make('license_number')
                            ->label(tr('recruitment.fields.license_number', [], null, 'dashboard') ?: 'License Number')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('passport_number')
                            ->label(tr('recruitment.fields.passport_number', [], null, 'dashboard') ?: 'Passport Number')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('passport_issue_date')
                            ->label(tr('recruitment.fields.passport_issue_date', [], null, 'dashboard') ?: 'Passport Issue Date')
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('passport_issue_place')
                            ->label(tr('recruitment.fields.passport_issue_place', [], null, 'dashboard') ?: 'Passport Issue Place')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('bank_sender')
                            ->label(tr('recruitment.fields.bank_sender', [], null, 'dashboard') ?: 'Bank Sender')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('account_number')
                            ->label(tr('recruitment.fields.account_number', [], null, 'dashboard') ?: 'Account Number')
                            ->maxLength(255)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment.sections.login', [], null, 'dashboard') ?: 'Login Credentials')
                    ->schema([
                        Forms\Components\TextInput::make('username')
                            ->label(tr('recruitment.fields.username', [], null, 'dashboard') ?: 'Username')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('password')
                            ->label(tr('recruitment.fields.password', [], null, 'dashboard') ?: 'Password')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation, ?Agent $record) => $operation === 'create' && $record?->username)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make(tr('recruitment.sections.notes', [], null, 'dashboard') ?: 'Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(tr('recruitment.fields.notes', [], null, 'dashboard') ?: 'Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(tr('recruitment.fields.code', [], null, 'dashboard') ?: 'Code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_ar')
                    ->label(tr('recruitment.fields.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_en')
                    ->label(tr('recruitment.fields.name_en', [], null, 'dashboard') ?: 'Name (English)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('license_number')
                    ->label(tr('recruitment.fields.license_number', [], null, 'dashboard') ?: 'License Number')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone_1')
                    ->label(tr('recruitment.fields.phone_1', [], null, 'dashboard') ?: 'Phone 1')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->label(tr('recruitment.fields.country', [], null, 'dashboard') ?: 'Country')
                    ->formatStateUsing(fn ($state, $record) => $record->country?->name_text ?? ($record->country?->name['en'] ?? ''))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(tr('actions.view', [], null, 'dashboard') ?: 'View'),
                Tables\Actions\EditAction::make()
                    ->label(tr('actions.edit', [], null, 'dashboard') ?: 'Edit')
                    ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('recruitment.agents.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->label(tr('actions.delete', [], null, 'dashboard') ?: 'Delete')
                    ->visible(fn () => auth()->user()?->can('recruitment.agents.delete') ?? false),
                Tables\Actions\Action::make('labor_prices')
                    ->label(tr('general.actions.labor_prices', [], null, 'dashboard') ?: 'Labor Prices')
                    ->icon('heroicon-o-currency-dollar')
                    ->url(fn (Agent $record) => AgentLaborPriceResource::getUrl('index', ['tableFilters' => ['agent_id' => ['value' => $record->id]]])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('recruitment.agents.delete') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['country']);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AgentLaborPricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgents::route('/'),
            'create' => Pages\CreateAgent::route('/create'),
            'view' => Pages\ViewAgent::route('/{record}'),
            'edit' => Pages\EditAgent::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.agents.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.agents.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.agents.view') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.agents.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('recruitment.agents.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
