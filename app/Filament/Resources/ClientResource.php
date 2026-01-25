<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\FileUpload;
use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Clients';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'general.clients.clients';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('general.clients.basic_data', [], null, 'dashboard') ?: 'Basic Data')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(tr('general.clients.client_code', [], null, 'dashboard') ?: 'Code')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name_ar')
                            ->label(tr('general.clients.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name_en')
                            ->label(tr('general.clients.name_en', [], null, 'dashboard') ?: 'Name (English)')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('national_id')
                            ->label(tr('general.clients.national_id', [], null, 'dashboard') ?: 'National ID')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('mobile')
                            ->label(tr('general.clients.mobile', [], null, 'dashboard') ?: 'Mobile')
                            ->required()
                            ->tel()
                            ->maxLength(50)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('mobile2')
                            ->label(tr('general.clients.mobile2', [], null, 'dashboard') ?: 'Mobile 2')
                            ->tel()
                            ->maxLength(50)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('email')
                            ->label(tr('general.clients.email', [], null, 'dashboard') ?: 'Email')
                            ->email()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('birth_date')
                            ->label(tr('general.clients.birth_date', [], null, 'dashboard') ?: 'Birth Date')
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\Radio::make('marital_status')
                            ->label(tr('general.clients.marital_status', [], null, 'dashboard') ?: 'Marital Status')
                            ->required()
                            ->options([
                                'single' => tr('general.clients.single', [], null, 'dashboard') ?: 'Single',
                                'married' => tr('general.clients.married', [], null, 'dashboard') ?: 'Married',
                                'divorced' => tr('general.clients.divorced', [], null, 'dashboard') ?: 'Divorced',
                                'widowed' => tr('general.clients.widowed', [], null, 'dashboard') ?: 'Widowed',
                            ])
                            ->columnSpan(1),

                        Forms\Components\Radio::make('classification')
                            ->label(tr('general.clients.classification', [], null, 'dashboard') ?: 'Classification')
                            ->required()
                            ->options([
                                'new' => tr('general.clients.new', [], null, 'dashboard') ?: 'New',
                                'vip' => tr('general.clients.vip', [], null, 'dashboard') ?: 'VIP',
                                'blocked' => tr('general.clients.blocked', [], null, 'dashboard') ?: 'Blocked',
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('general.clients.national_address', [], null, 'dashboard') ?: 'National Address')
                    ->schema([
                        Forms\Components\TextInput::make('building_no')
                            ->label(tr('general.clients.building_no', [], null, 'dashboard') ?: 'Building No')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('street_name')
                            ->label(tr('general.clients.street_name', [], null, 'dashboard') ?: 'Street Name')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('city_name')
                            ->label(tr('general.clients.city_name', [], null, 'dashboard') ?: 'City Name')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('district_name')
                            ->label(tr('general.clients.district_name', [], null, 'dashboard') ?: 'District Name')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('postal_code')
                            ->label(tr('general.clients.postal_code', [], null, 'dashboard') ?: 'Postal Code')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('additional_no')
                            ->label(tr('general.clients.additional_no', [], null, 'dashboard') ?: 'Additional No')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('unit_no')
                            ->label(tr('general.clients.unit_no', [], null, 'dashboard') ?: 'Unit No')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('building_no_en')
                            ->label(tr('general.clients.building_no_en', [], null, 'dashboard') ?: 'Building No (English)')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('street_name_en')
                            ->label(tr('general.clients.street_name_en', [], null, 'dashboard') ?: 'Street Name (English)')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('city_name_en')
                            ->label(tr('general.clients.city_name_en', [], null, 'dashboard') ?: 'City Name (English)')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('district_name_en')
                            ->label(tr('general.clients.district_name_en', [], null, 'dashboard') ?: 'District Name (English)')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('unit_no_en')
                            ->label(tr('general.clients.unit_no_en', [], null, 'dashboard') ?: 'Unit No (English)')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('full_address_ar')
                            ->label(tr('general.clients.full_address_ar', [], null, 'dashboard') ?: 'Full Address (Arabic)')
                            ->rows(3)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('full_address_en')
                            ->label(tr('general.clients.full_address_en', [], null, 'dashboard') ?: 'Full Address (English)')
                            ->rows(3)
                            ->columnSpan(1),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('generate_address')
                                ->label(tr('general.clients.generate_address', [], null, 'dashboard') ?: 'Generate Address')
                                ->icon('heroicon-o-sparkles')
                                ->action(function (Forms\Get $get, Forms\Set $set) {
                                    $buildingNo = $get('building_no');
                                    $streetName = $get('street_name');
                                    $districtName = $get('district_name');
                                    $cityName = $get('city_name');
                                    $postalCode = $get('postal_code');
                                    $additionalNo = $get('additional_no');
                                    $unitNo = $get('unit_no');

                                    $addressAr = trim(implode('، ', array_filter([
                                        $buildingNo ? 'مبنى ' . $buildingNo : null,
                                        $streetName ? 'شارع ' . $streetName : null,
                                        $districtName ? 'حي ' . $districtName : null,
                                        $cityName ? 'مدينة ' . $cityName : null,
                                        $postalCode ? 'الرمز البريدي: ' . $postalCode : null,
                                        $additionalNo ? 'إضافي: ' . $additionalNo : null,
                                        $unitNo ? 'وحدة ' . $unitNo : null,
                                    ])));

                                    $buildingNoEn = $get('building_no_en');
                                    $streetNameEn = $get('street_name_en');
                                    $districtNameEn = $get('district_name_en');
                                    $cityNameEn = $get('city_name_en');
                                    $unitNoEn = $get('unit_no_en');

                                    $addressEn = trim(implode(', ', array_filter([
                                        $buildingNoEn ? 'Building ' . $buildingNoEn : null,
                                        $streetNameEn ? 'Street ' . $streetNameEn : null,
                                        $districtNameEn ? 'District ' . $districtNameEn : null,
                                        $cityNameEn ? 'City ' . $cityNameEn : null,
                                        $postalCode ? 'Postal Code: ' . $postalCode : null,
                                        $additionalNo ? 'Additional: ' . $additionalNo : null,
                                        $unitNoEn ? 'Unit ' . $unitNoEn : null,
                                    ])));

                                    $set('full_address_ar', $addressAr);
                                    $set('full_address_en', $addressEn);
                                }),
                        ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('general.clients.housing_data', [], null, 'dashboard') ?: 'Housing Data')
                    ->schema([
                        Forms\Components\Select::make('housing_type')
                            ->label(tr('general.clients.housing_type', [], null, 'dashboard') ?: 'Housing Type')
                            ->options([
                                'villa' => tr('general.clients.villa', [], null, 'dashboard') ?: 'Villa',
                                'building' => tr('general.clients.building', [], null, 'dashboard') ?: 'Building',
                                'apartment' => tr('general.clients.apartment', [], null, 'dashboard') ?: 'Apartment',
                                'farm' => tr('general.clients.farm', [], null, 'dashboard') ?: 'Farm',
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('general.clients.other_data', [], null, 'dashboard') ?: 'Other Data')
                    ->schema([
                        FileUpload::makeImage('id_image', 'clients')
                            ->label(tr('general.clients.id_image', [], null, 'dashboard') ?: 'ID Image')
                            ->columnSpan(1),

                        FileUpload::document('other_document', 'clients')
                            ->label(tr('general.clients.other_document', [], null, 'dashboard') ?: 'Other Document')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('source')
                            ->label(tr('general.clients.source', [], null, 'dashboard') ?: 'Source')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\Select::make('office_referral')
                            ->label(tr('general.clients.office_referral', [], null, 'dashboard') ?: 'Office Referral')
                            ->options([
                                'client' => tr('general.clients.client', [], null, 'dashboard') ?: 'Client',
                            ])
                            ->default('client')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(tr('general.clients.client_code', [], null, 'dashboard') ?: 'Code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_ar')
                    ->label(tr('general.clients.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_en')
                    ->label(tr('general.clients.name_en', [], null, 'dashboard') ?: 'Name (English)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('national_id')
                    ->label(tr('general.clients.national_id', [], null, 'dashboard') ?: 'National ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mobile')
                    ->label(tr('general.clients.mobile', [], null, 'dashboard') ?: 'Mobile')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('classification')
                    ->label(tr('general.clients.classification', [], null, 'dashboard') ?: 'Classification')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'new' => tr('general.clients.new', [], null, 'dashboard') ?: 'New',
                        'vip' => tr('general.clients.vip', [], null, 'dashboard') ?: 'VIP',
                        'blocked' => tr('general.clients.blocked', [], null, 'dashboard') ?: 'Blocked',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'new' => 'info',
                        'vip' => 'success',
                        'blocked' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('city_name')
                    ->label(tr('general.clients.city_name', [], null, 'dashboard') ?: 'City')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.clients.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('classification')
                    ->label(tr('general.clients.classification', [], null, 'dashboard') ?: 'Classification')
                    ->options([
                        'new' => tr('general.clients.new', [], null, 'dashboard') ?: 'New',
                        'vip' => tr('general.clients.vip', [], null, 'dashboard') ?: 'VIP',
                        'blocked' => tr('general.clients.blocked', [], null, 'dashboard') ?: 'Blocked',
                    ]),

                Tables\Filters\Filter::make('city_name')
                    ->form([
                        Forms\Components\TextInput::make('city_name')
                            ->label(tr('general.clients.city_name', [], null, 'dashboard') ?: 'City')
                            ->placeholder(tr('general.clients.city_name', [], null, 'dashboard') ?: 'City'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['city_name'],
                                fn (Builder $query, $city): Builder => $query->where('city_name', 'like', "%{$city}%"),
                            );
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(tr('fields.date_from', [], null, 'dashboard') ?: 'From Date'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(tr('fields.date_to', [], null, 'dashboard') ?: 'To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(tr('general.actions.details', [], null, 'dashboard') ?: 'Details'),
                Tables\Actions\EditAction::make()
                    ->label(tr('general.actions.edit', [], null, 'dashboard') ?: 'Edit')
                    ->visible(fn () => auth()->user()?->can('clients.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->label(tr('general.actions.delete', [], null, 'dashboard') ?: 'Delete')
                    ->visible(fn () => auth()->user()?->can('clients.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('clients.delete') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('clients.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('clients.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        return auth()->user()?->can('clients.view') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('clients.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('clients.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('clients.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
