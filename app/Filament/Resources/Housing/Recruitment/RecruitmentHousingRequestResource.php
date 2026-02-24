<?php

namespace App\Filament\Resources\Housing\Recruitment;

use App\Enums\HousingRequestStatus;
use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingRequestResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Client;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Profession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class RecruitmentHousingRequestResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = \App\Models\Housing\HousingRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'housing';
    protected static ?string $navigationLabel = 'طلبات الإيواء';
    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->recruitment();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('housing.requests.pending', [], null, 'dashboard') ?: 'Pending Requests')
                    ->schema([
                        Forms\Components\TextInput::make('order_no')
                            ->label(tr('housing.requests.order_no', [], null, 'dashboard') ?: 'Order No')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('contract_no')
                            ->label(tr('housing.requests.contract_no', [], null, 'dashboard') ?: 'Contract No')
                            ->columnSpan(1),

                        Forms\Components\Select::make('client_id')
                            ->label(tr('housing.requests.client', [], null, 'dashboard') ?: 'Client')
                            ->relationship('client', 'name_ar')
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name_ar')
                                    ->label(tr('general.clients.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('name_en')
                                    ->label(tr('general.clients.name_en', [], null, 'dashboard') ?: 'Name (English)')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('national_id')
                                    ->label(tr('general.clients.national_id', [], null, 'dashboard') ?: 'National ID')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Client::class, 'national_id'),
                                Forms\Components\TextInput::make('mobile')
                                    ->label(tr('general.clients.mobile', [], null, 'dashboard') ?: 'Mobile')
                                    ->required()
                                    ->tel()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('email')
                                    ->label(tr('general.clients.email', [], null, 'dashboard') ?: 'Email')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('birth_date')
                                    ->label(tr('general.clients.birth_date', [], null, 'dashboard') ?: 'تاريخ الميلاد')
                                    ->required()
                                    ->placeholder('هـ / / ')
                                    ->helperText('أدخل التاريخ الهجري بصيغة: يوم/شهر/سنة (مثال: 15/03/1445)'),
                                Forms\Components\Radio::make('marital_status')
                                    ->label(tr('general.clients.marital_status', [], null, 'dashboard') ?: 'Marital Status')
                                    ->required()
                                    ->options([
                                        'single' => tr('general.clients.single', [], null, 'dashboard') ?: 'Single',
                                        'married' => tr('general.clients.married', [], null, 'dashboard') ?: 'Married',
                                        'divorced' => tr('general.clients.divorced', [], null, 'dashboard') ?: 'Divorced',
                                        'widowed' => tr('general.clients.widowed', [], null, 'dashboard') ?: 'Widowed',
                                    ]),
                                Forms\Components\Radio::make('classification')
                                    ->label(tr('general.clients.classification', [], null, 'dashboard') ?: 'Classification')
                                    ->required()
                                    ->options([
                                        'new' => tr('general.clients.new', [], null, 'dashboard') ?: 'New',
                                        'vip' => tr('general.clients.vip', [], null, 'dashboard') ?: 'VIP',
                                        'blocked' => tr('general.clients.blocked', [], null, 'dashboard') ?: 'Blocked',
                                    ])
                                    ->default('new'),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $client = Client::create($data);
                                return $client->id;
                            })
                            ->columnSpan(1),

                        Forms\Components\Select::make('laborer_id')
                            ->label(tr('housing.requests.laborer', [], null, 'dashboard') ?: 'Laborer')
                            ->relationship('laborer', 'name_ar')
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name_ar')
                                    ->label(tr('recruitment.fields.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('name_en')
                                    ->label(tr('recruitment.fields.name_en', [], null, 'dashboard') ?: 'Name (English)')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('passport_number')
                                    ->label(tr('recruitment.fields.passport_number', [], null, 'dashboard') ?: 'Passport Number')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Laborer::class, 'passport_number'),
                                Forms\Components\Select::make('nationality_id')
                                    ->label(tr('recruitment.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                                    ->options(function () {
                                        return Nationality::where('is_active', true)
                                            ->get()
                                            ->mapWithKeys(function ($nationality) {
                                                $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                                                return [$nationality->id => $label];
                                            });
                                    })
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Select::make('profession_id')
                                    ->label(tr('recruitment.fields.profession', [], null, 'dashboard') ?: 'Profession')
                                    ->options(function () {
                                        return Profession::where('is_active', true)
                                            ->get()
                                            ->mapWithKeys(function ($profession) {
                                                $label = app()->getLocale() === 'ar' ? $profession->name_ar : $profession->name_en;
                                                return [$profession->id => $label];
                                            });
                                    })
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Select::make('gender')
                                    ->label(tr('recruitment.fields.gender', [], null, 'dashboard') ?: 'Gender')
                                    ->options([
                                        'male' => tr('recruitment_contract.gender.male', [], null, 'dashboard') ?: 'Male',
                                        'female' => tr('recruitment_contract.gender.female', [], null, 'dashboard') ?: 'Female',
                                    ])
                                    ->nullable(),
                                Forms\Components\TextInput::make('phone_1')
                                    ->label(tr('recruitment.fields.phone_1', [], null, 'dashboard') ?: 'Phone 1')
                                    ->tel()
                                    ->maxLength(50)
                                    ->nullable(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $laborer = Laborer::create([
                                    'name_ar' => $data['name_ar'],
                                    'name_en' => $data['name_en'],
                                    'passport_number' => $data['passport_number'],
                                    'nationality_id' => $data['nationality_id'],
                                    'profession_id' => $data['profession_id'],
                                    'gender' => $data['gender'] ?? null,
                                    'phone_1' => $data['phone_1'] ?? null,
                                    'is_available' => true,
                                ]);
                                return $laborer->id;
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('passport_no')
                            ->label(tr('housing.requests.passport_no', [], null, 'dashboard') ?: 'رقم الجواز')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('sponsor_name')
                            ->label(tr('housing.requests.sponsor_name', [], null, 'dashboard') ?: 'اسم الكفيل')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('transferred_sponsor_name')
                            ->label(tr('housing.requests.transferred_sponsor_name', [], null, 'dashboard') ?: 'اسم الكفيل المنقول له')
                            ->columnSpan(1),

                        Forms\Components\Select::make('request_type')
                            ->label(tr('housing.requests.type', [], null, 'dashboard') ?: 'Type')
                            ->options([
                                'delivery' => tr('housing.requests.type.delivery', [], null, 'dashboard') ?: 'تسليم',
                                'return' => tr('housing.requests.type.return', [], null, 'dashboard') ?: 'استرجاع',
                                'new_arrival' => tr('housing.requests.type.new_arrival', [], null, 'dashboard') ?: 'وافد جديد',
                            ])
                            ->required()
                            ->default('delivery')
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('request_date')
                            ->label(tr('housing.requests.request_date', [], null, 'dashboard') ?: 'Request Date')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label(tr('housing.requests.status', [], null, 'dashboard') ?: 'الحالة')
                            ->options(HousingRequestStatus::options())
                            ->searchable()
                            ->placeholder(tr('housing.requests.select_status', [], null, 'dashboard') ?: 'Select option / اختر')
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\Hidden::make('housing_type')
                            ->default('recruitment'),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('housing.requests.notes', [], null, 'dashboard') ?: 'Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\CheckboxColumn::make('selected')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('order_no')
                    ->label(tr('housing.requests.order_no', [], null, 'dashboard') ?: 'Order No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contract_no')
                    ->label(tr('housing.requests.contract_no', [], null, 'dashboard') ?: 'Contract No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.name_ar')
                    ->label(tr('housing.requests.client', [], null, 'dashboard') ?: 'Client')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('laborer.name_ar')
                    ->label(tr('housing.requests.laborer', [], null, 'dashboard') ?: 'Laborer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('request_type')
                    ->label(tr('housing.requests.type', [], null, 'dashboard') ?: 'Type')
                    ->color(fn (string $state): string => match ($state) {
                        'delivery' => 'success',
                        'return' => 'warning',
                        'new_arrival' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => tr("housing.requests.type.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('housing.requests.status', [], null, 'dashboard') ?: 'الحالة')
                    ->color(fn ($state) => $state ? HousingRequestStatus::getColor($state) : 'gray')
                    ->formatStateUsing(fn ($state) => $state ? HousingRequestStatus::getLabel($state) : '-')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('request_date')
                    ->label(tr('housing.requests.request_date', [], null, 'dashboard') ?: 'Request Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label(tr('housing.requests.notes', [], null, 'dashboard') ?: 'Notes')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('actions')
                    ->label(tr('housing.requests.actions', [], null, 'dashboard') ?: 'Actions')
                    ->formatStateUsing(fn () => '')
                    ->view('filament.tables.columns.housing-action-button'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('request_type')
                    ->label(tr('housing.requests.type', [], null, 'dashboard') ?: 'Type')
                    ->options([
                        'delivery' => tr('housing.requests.type.delivery', [], null, 'dashboard') ?: 'تسليم',
                        'return' => tr('housing.requests.type.return', [], null, 'dashboard') ?: 'استرجاع',
                        'new_arrival' => tr('housing.requests.type.new_arrival', [], null, 'dashboard') ?: 'وافد جديد',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('housing.requests.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options(HousingRequestStatus::options()),

                Tables\Filters\Filter::make('request_date')
                    ->form([
                        Forms\Components\DatePicker::make('request_from')
                            ->label(tr('housing.dashboard.from_date', [], null, 'dashboard') ?: 'From Date'),
                        Forms\Components\DatePicker::make('request_until')
                            ->label(tr('housing.dashboard.to_date', [], null, 'dashboard') ?: 'To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['request_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('request_date', '>=', $date),
                            )
                            ->when(
                                $data['request_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('request_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => \App\Enums\HousingRequestStatus::COMPLETED])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('request_date', 'desc');
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
            'index' => Pages\ListRecruitmentHousingRequests::route('/'),
            'create' => Pages\CreateRecruitmentHousingRequest::route('/create'),
            'edit' => Pages\EditRecruitmentHousingRequest::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('housing.requests.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('housing.requests.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('housing.requests.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('housing.requests.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
