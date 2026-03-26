<?php

namespace App\Filament\Resources\Housing;

use App\Filament\Resources\Housing\AccommodationEntryResource\Pages;
use App\Models\Client;
use App\Models\Housing\AccommodationEntry;
use App\Models\Housing\Building;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Profession;
use App\Models\Recruitment\RecruitmentContract;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class AccommodationEntryResource extends Resource
{
    protected static ?string $model = AccommodationEntry::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'إيواء الاستقدام';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'إدخال إيواء';
    protected static ?string $pluralModelLabel = 'إدخالات الإيواء';

    public static function getNavigationLabel(): string
    {
        return 'إدخالات الإيواء';
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        return $user && (
            $user->can('housing.accommodation_entries.view') ||
            $user->can('housing.accommodation_entries.create')
        );
    }

    protected static function safeString(mixed $value, string $default = '—'): string
    {
        if ($value === null) {
            return $default;
        }

        if (! is_scalar($value)) {
            return $default;
        }

        $value = (string) $value;

        if ($value === '') {
            return $default;
        }

        if (! mb_check_encoding($value, 'UTF-8')) {
            $value = @mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252, Windows-1256, ASCII');
        }

        $value = @iconv('UTF-8', 'UTF-8//IGNORE', $value) ?: $default;
        $value = preg_replace('/[^\P{C}\n\r\t]+/u', '', $value) ?? $default;
        $value = trim($value);

        return $value !== '' ? $value : $default;
    }

    protected static function clientLabel(?Client $client): string
    {
        if (! $client) {
            return '—';
        }

        $name = static::safeString($client->name_ar, 'بدون اسم');
        $nationalId = static::safeString($client->national_id, '');

        return $nationalId !== '' && $nationalId !== '—'
            ? "{$name} ({$nationalId})"
            : $name;
    }

    protected static function laborerLabel(?Laborer $laborer): string
    {
        if (! $laborer) {
            return '—';
        }

        $name = static::safeString($laborer->name_ar, 'بدون اسم');
        $passport = static::safeString($laborer->passport_number, '-');

        return "{$name} ({$passport})";
    }

    public static function housingStatusOptions(): array
    {
        return [
            'unpaid_salary'        => 'عدم دفع راتب',
            'transfer_sponsorship' => 'نقل كفالة',
            'temporary'            => 'مؤقتة',
            'rental'               => 'إيجار',
            'work_refused'         => 'رفض عمل',
            'runaway'              => 'هروب',
            'ready_for_delivery'   => 'جاهز للتسليم',
            'with_client'          => 'مع العميل',
            'in_accommodation'     => 'في الإيواء',
            'outside_kingdom'      => 'خارج المملكة',
            'ready_for_travel'     => 'جاهزة للتسفير',
        ];
    }

    public static function entryTypeOptions(): array
    {
        return [
            'new_arrival' => 'وافد جديد',
            'return'      => 'استرجاع',
            'transfer'    => 'نقل كفالة',
        ];
    }

    public static function buildFormSchema(bool $readonly = false): array
    {
        return [
            Forms\Components\Section::make('بيانات العاملة والعميل')
                ->schema([
                    Forms\Components\Select::make('contract_no')
                        ->label('رقم العقد')
                        ->options(function (): array {
                            return RecruitmentContract::query()
                                ->with('client:id,name_ar,national_id')
                                ->get()
                                ->mapWithKeys(function ($contract) {
                                    $contractNo = static::safeString($contract->contract_no, '');
                                    if ($contractNo === '') {
                                        return [];
                                    }

                                    $label = $contractNo;

                                    if ($contract->client) {
                                        $label .= ' - ' . static::clientLabel($contract->client);
                                    }

                                    return [$contractNo => static::safeString($label)];
                                })
                                ->toArray();
                        })
                        ->searchable()
                        ->live()
                        ->placeholder('اختر عقدًا أو اتركه فارغًا للاختيار اليدوي')
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->afterStateUpdated(function ($state, callable $set) use ($readonly) {
                            if ($readonly) {
                                return;
                            }

                            if (blank($state)) {
                                $set('laborer_id', null);
                                $set('customer_id', null);
                                return;
                            }

                            $contract = RecruitmentContract::query()
                                ->where('contract_no', $state)
                                ->first();

                            if (! $contract) {
                                return;
                            }

                            $set('laborer_id', $contract->worker_id ?: null);
                            $set('customer_id', $contract->client_id ?: null);
                        })
                        ->columnSpan(2),

                    Forms\Components\Select::make('laborer_id')
                        ->label('العاملة')
                        ->options(function (Forms\Get $get): array {
                            if ($get('contract_no')) {
                                return Laborer::query()
                                    ->orderBy('name_ar')
                                    ->get()
                                    ->mapWithKeys(fn ($worker) => [
                                        $worker->id => static::laborerLabel($worker),
                                    ])
                                    ->toArray();
                            }

                            return Cache::remember('recruitment_accommodation.workers', 21600, function () {
                                return Laborer::query()
                                    ->where('is_available', true)
                                    ->orderBy('name_ar')
                                    ->get()
                                    ->mapWithKeys(fn ($worker) => [
                                        $worker->id => static::laborerLabel($worker),
                                    ])
                                    ->toArray();
                            });
                        })
                        ->required(! $readonly)
                        ->searchable()
                        ->live()
                        ->disabled($readonly || fn (Forms\Get $get): bool => (bool) $get('contract_no'))
                        ->dehydrated(true)
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name_ar')
                                ->label('الاسم (عربي)')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('passport_number')
                                ->label('رقم الجواز')
                                ->required()
                                ->maxLength(255)
                                ->unique(Laborer::class, 'passport_number'),

                            Forms\Components\Select::make('nationality_id')
                                ->label('الجنسية')
                                ->options(fn () => Nationality::query()
                                    ->where('is_active', true)
                                    ->pluck('name_ar', 'id')
                                    ->map(fn ($name) => static::safeString($name))
                                    ->toArray()
                                )
                                ->searchable()
                                ->required(),

                            Forms\Components\Select::make('profession_id')
                                ->label('المهنة')
                                ->options(fn () => Profession::query()
                                    ->where('is_active', true)
                                    ->pluck('name_ar', 'id')
                                    ->map(fn ($name) => static::safeString($name))
                                    ->toArray()
                                )
                                ->searchable()
                                ->required(),

                            Forms\Components\Select::make('gender')
                                ->label('الجنس')
                                ->options([
                                    'male'   => 'ذكر',
                                    'female' => 'أنثى',
                                ])
                                ->nullable(),

                            Forms\Components\TextInput::make('phone_1')
                                ->label('الجوال')
                                ->tel()
                                ->maxLength(50)
                                ->nullable(),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            $laborer = Laborer::query()->create([
                                'name_ar'         => static::safeString($data['name_ar'], ''),
                                'passport_number' => static::safeString($data['passport_number'], ''),
                                'nationality_id'  => $data['nationality_id'],
                                'profession_id'   => $data['profession_id'],
                                'gender'          => $data['gender'] ?? null,
                                'phone_1'         => isset($data['phone_1']) ? static::safeString($data['phone_1'], '') : null,
                                'is_available'    => true,
                            ]);

                            Cache::forget('recruitment_accommodation.workers');

                            return $laborer->id;
                        })
                        ->columnSpan(1),

                    Forms\Components\Select::make('customer_id')
                        ->label('العميل')
                        ->options(function (): array {
                            return Client::query()
                                ->get()
                                ->mapWithKeys(fn ($client) => [
                                    $client->id => static::clientLabel($client),
                                ])
                                ->toArray();
                        })
                        ->searchable()
                        ->disabled($readonly || fn (Forms\Get $get): bool => (bool) $get('contract_no'))
                        ->dehydrated(true)
                        ->columnSpan(1),
                ])
                ->columns(2),

            Forms\Components\Section::make('بيانات الإدخال')
                ->schema([
                    Forms\Components\Select::make('entry_type')
                        ->label('نوع الدخول')
                        ->options(static::entryTypeOptions())
                        ->required(! $readonly)
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->live()
                        ->columnSpan(1),

                    Forms\Components\Select::make('transfer_client_id')
                        ->label('عميل نقل الكفالة')
                        ->options(function (): array {
                            return Client::query()
                                ->get()
                                ->mapWithKeys(fn ($client) => [
                                    $client->id => static::clientLabel($client),
                                ])
                                ->toArray();
                        })
                        ->searchable()
                        ->visible(fn (Forms\Get $get): bool => $get('entry_type') === 'transfer')
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->columnSpan(1),

                    Forms\Components\FileUpload::make('transfer_contract_file')
                        ->label('صورة عقد نقل الكفالة')
                        ->disk('public')
                        ->directory('transfer-contracts')
                        ->acceptedFileTypes([
                            'image/*',
                            'application/pdf',
                        ])
                        ->maxSize(10240)
                        ->visible(fn (Forms\Get $get): bool => $get('entry_type') === 'transfer')
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->columnSpan(2),

                    Forms\Components\DateTimePicker::make('entry_date')
                        ->label('تاريخ الدخول')
                        ->required(! $readonly)
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->native(false)
                        ->columnSpan(1),

                    Forms\Components\DateTimePicker::make('exit_date')
                        ->label('تاريخ الخروج')
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->native(false)
                        ->columnSpan(1),

                    Forms\Components\Select::make('building_id')
                        ->label('المبنى')
                        ->options(function (): array {
                            return Building::query()
                                ->get()
                                ->mapWithKeys(function ($building) {
                                    $name = static::safeString($building->name, 'بدون اسم');
                                    $capacity = static::safeString($building->available_capacity, '0');

                                    return [$building->id => "{$name} ({$capacity} متاح)"];
                                })
                                ->toArray();
                        })
                        ->required(! $readonly)
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->searchable()
                        ->columnSpan(1),
                ])
                ->columns(2),

            Forms\Components\Section::make('سجل الحالات')
                ->schema([
                    Forms\Components\View::make('filament.forms.components.housing-status-table')
                        ->viewData(fn (Forms\Get $get): array => [
                            'statuses'            => static::housingStatusOptions(),
                            'statusDates'         => json_decode($get('all_status_dates') ?? '{}', true) ?: [],
                            'statusDurations'     => [],
                            'currentStatus'       => static::safeString($get('status_key') ?? '', ''),
                            'statusStatePath'     => 'data.status_key',
                            'statusDateStatePath' => 'data.status_date',
                            'readonly'            => $readonly,
                        ])
                        ->columnSpanFull(),

                    Forms\Components\Hidden::make('status_key'),
                    Forms\Components\Hidden::make('status_date')->default(now()->toDateString()),
                    Forms\Components\Hidden::make('all_status_dates')->default('{}'),
                ]),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema(static::buildFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('contract_no')
                    ->label('رقم العقد')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => static::safeString($state))
                    ->default('—'),

                Tables\Columns\TextColumn::make('laborer.name_ar')
                    ->label('العاملة')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => static::safeString($state))
                    ->default('—'),

                Tables\Columns\TextColumn::make('laborer.passport_number')
                    ->label('رقم الجواز')
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => static::safeString($state))
                    ->default('—'),

                Tables\Columns\TextColumn::make('customer.name_ar')
                    ->label('العميل')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => static::safeString($state))
                    ->default('—'),

                Tables\Columns\TextColumn::make('entry_type')
                    ->label('نوع الدخول')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => static::entryTypeOptions()[$state] ?? static::safeString($state))
                    ->color(fn (?string $state): string => match ($state) {
                        'new_arrival' => 'success',
                        'return'      => 'warning',
                        'transfer'    => 'info',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('building.name')
                    ->label('المبنى')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => static::safeString($state))
                    ->default('—'),

                Tables\Columns\TextColumn::make('status_key')
                    ->label('الحالة')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (?string $state): string => static::housingStatusOptions()[$state] ?? static::safeString($state)),

                Tables\Columns\TextColumn::make('entry_date')
                    ->label('تاريخ الدخول')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('exit_date')
                    ->label('تاريخ الخروج')
                    ->date('Y-m-d')
                    ->sortable()
                    ->default('—'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('entry_type')
                    ->label('نوع الدخول')
                    ->options(static::entryTypeOptions()),

                Tables\Filters\SelectFilter::make('status_key')
                    ->label('الحالة')
                    ->options(static::housingStatusOptions()),

                Tables\Filters\SelectFilter::make('nationality')
                    ->label('الجنسية')
                    ->options(fn (): array => Nationality::query()
                        ->where('is_active', true)
                        ->pluck('name_ar', 'id')
                        ->map(fn ($name) => static::safeString($name))
                        ->toArray()
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('laborer', function (Builder $subQuery) use ($data) {
                            $subQuery->where('nationality_id', $data['value']);
                        });
                    }),

                Tables\Filters\Filter::make('active')
                    ->label('داخل فقط (بدون تاريخ خروج)')
                    ->query(fn (Builder $query): Builder => $query->whereNull('exit_date')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('بيانات العاملة والعميل')
                ->schema([
                    Infolists\Components\TextEntry::make('contract_no')
                        ->label('رقم العقد')
                        ->formatStateUsing(fn ($state) => static::safeString($state))
                        ->default('—'),

                    Infolists\Components\TextEntry::make('laborer.name_ar')
                        ->label('اسم العاملة')
                        ->formatStateUsing(fn ($state) => static::safeString($state))
                        ->default('—'),

                    Infolists\Components\TextEntry::make('laborer.passport_number')
                        ->label('رقم الجواز')
                        ->formatStateUsing(fn ($state) => static::safeString($state))
                        ->default('—'),

                    Infolists\Components\TextEntry::make('customer.name_ar')
                        ->label('العميل')
                        ->formatStateUsing(fn ($state) => static::safeString($state))
                        ->default('—'),
                ])
                ->columns(2),

            Infolists\Components\Section::make('بيانات الإدخال')
                ->schema([
                    Infolists\Components\TextEntry::make('entry_type')
                        ->label('نوع الدخول')
                        ->badge()
                        ->formatStateUsing(fn (?string $state): string => static::entryTypeOptions()[$state] ?? static::safeString($state))
                        ->color(fn (?string $state): string => match ($state) {
                            'new_arrival' => 'success',
                            'return'      => 'warning',
                            'transfer'    => 'info',
                            default       => 'gray',
                        }),

                    Infolists\Components\TextEntry::make('entry_date')
                        ->label('تاريخ الدخول')
                        ->date('Y-m-d')
                        ->default('—'),

                    Infolists\Components\TextEntry::make('exit_date')
                        ->label('تاريخ الخروج')
                        ->date('Y-m-d')
                        ->default('—'),

                    Infolists\Components\TextEntry::make('building.name')
                        ->label('المبنى')
                        ->formatStateUsing(fn ($state) => static::safeString($state))
                        ->default('—'),

                    Infolists\Components\TextEntry::make('status_key')
                        ->label('الحالة الحالية')
                        ->badge()
                        ->color('info')
                        ->formatStateUsing(fn (?string $state): string => static::housingStatusOptions()[$state] ?? static::safeString($state)),
                ])
                ->columns(2),

            Infolists\Components\Section::make('بيانات نقل الكفالة')
                ->visible(fn ($record): bool => $record?->entry_type === 'transfer')
                ->schema([
                    Infolists\Components\TextEntry::make('transferData.transferClient.name_ar')
                        ->label('عميل نقل الكفالة')
                        ->formatStateUsing(fn ($state) => static::safeString($state))
                        ->default('—'),

                    Infolists\Components\TextEntry::make('transferData.contract_file_path')
                        ->label('ملف عقد نقل الكفالة')
                        ->formatStateUsing(fn (?string $state): string => $state ? static::safeString(basename($state)) : '—'),
                ])
                ->columns(2),

            Infolists\Components\Section::make('سجل الحالات')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('statusLogs')
                        ->label('')
                        ->schema([
                            Infolists\Components\TextEntry::make('status_key')
                                ->label('الحالة')
                                ->badge()
                                ->color('info')
                                ->formatStateUsing(fn (?string $state): string => static::housingStatusOptions()[$state] ?? static::safeString($state)),

                            Infolists\Components\TextEntry::make('status_date')
                                ->label('التاريخ')
                                ->date('Y-m-d')
                                ->default('—'),
                        ])
                        ->columns(2),
                ]),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'recruitment');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAccommodationEntries::route('/'),
            'create' => Pages\CreateAccommodationEntry::route('/create'),
            'view'   => Pages\ViewAccommodationEntry::route('/{record}'),
            'edit'   => Pages\EditAccommodationEntry::route('/{record}/edit'),
        ];
    }
}
