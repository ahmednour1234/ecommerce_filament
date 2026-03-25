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

    // ── Housing status options ─────────────────────────────────────────────

    public static function housingStatusOptions(): array
    {
        return [
            'unpaid_salary'        => 'عدم دفع راتب',
            'transfer_sponsorship' => 'نقل كفاله',
            'temporary'            => 'مؤقته',
            'rental'               => 'ايجار',
            'work_refused'         => 'رفض عمل',
            'runaway'              => 'هروب',
            'ready_for_delivery'   => 'جاهز للتسليم',
            'with_client'          => 'مع العميل',
            'in_accommodation'     => 'في الايواء',
            'outside_kingdom'      => 'خارج المملكه',
            'ready_for_travel'     => 'جاهزه للتسفير',
        ];
    }

    // ── Shared form schema ─────────────────────────────────────────────────

    public static function buildFormSchema(bool $readonly = false): array
    {
        return [
            Forms\Components\Section::make('بيانات العاملة والعميل')
                ->schema([
                    Forms\Components\Select::make('contract_no')
                        ->label('رقم العقد')
                        ->options(fn () => RecruitmentContract::query()
                            ->with('client')
                            ->get()
                            ->mapWithKeys(fn ($c) => [
                                $c->contract_no => $c->contract_no . ($c->client ? ' — ' . $c->client->name_ar : ''),
                            ])->toArray()
                        )
                        ->searchable()
                        ->live()
                        ->placeholder('اختر عقداً أو اتركه فارغاً للاختيار اليدوي')
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->afterStateUpdated(function ($state, callable $set) use ($readonly) {
                            if ($readonly) return;
                            if ($state) {
                                $contract = RecruitmentContract::where('contract_no', $state)->first();
                                if ($contract) {
                                    if ($contract->worker_id) {
                                        $set('laborer_id', $contract->worker_id);
                                    }
                                    if ($contract->client_id) {
                                        $set('customer_id', $contract->client_id);
                                    }
                                }
                            } else {
                                $set('laborer_id', null);
                                $set('customer_id', null);
                            }
                        })
                        ->columnSpan(2),

                    Forms\Components\Select::make('laborer_id')
                        ->label('العاملة')
                        ->options(fn (Forms\Get $get) => $get('contract_no')
                            ? Laborer::orderBy('name_ar')->get()
                                ->mapWithKeys(fn ($w) => [$w->id => "{$w->name_ar} ({$w->passport_number})"])
                                ->toArray()
                            : Cache::remember('recruitment_accommodation.workers', 21600, fn () =>
                                Laborer::where('is_available', true)->get()
                                    ->mapWithKeys(fn ($w) => [$w->id => "{$w->name_ar} ({$w->passport_number})"])
                                    ->toArray()
                              )
                        )
                        ->required(!$readonly)
                        ->searchable()
                        ->live()
                        ->disabled($readonly || fn (Forms\Get $get) => (bool) $get('contract_no'))
                        ->dehydrated(true)
                        ->when(! $readonly, fn ($field) => $field
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name_ar')
                                    ->label('الاسم (عربي)')->required()->maxLength(255),
                                Forms\Components\TextInput::make('passport_number')
                                    ->label('رقم الجواز')->required()->maxLength(255)
                                    ->unique(Laborer::class, 'passport_number'),
                                Forms\Components\Select::make('nationality_id')
                                    ->label('الجنسية')
                                    ->options(fn () => Nationality::where('is_active', true)
                                        ->whereIn('name_ar', ['الفلبين', 'بنغلادش', 'سريلانكا', 'اثيوبيا', 'اوغندا', 'كينيا', 'بورندي'])
                                        ->get()->mapWithKeys(fn ($n) => [$n->id => $n->name_ar])
                                    )->searchable()->required(),
                                Forms\Components\Select::make('profession_id')
                                    ->label('المهنة')
                                    ->options(fn () => Profession::where('is_active', true)->get()
                                        ->mapWithKeys(fn ($p) => [$p->id => $p->name_ar])
                                    )->searchable()->required(),
                                Forms\Components\Select::make('gender')
                                    ->label('الجنس')
                                    ->options(['male' => 'ذكر', 'female' => 'أنثى'])->nullable(),
                                Forms\Components\TextInput::make('phone_1')
                                    ->label('الجوال')->tel()->maxLength(50)->nullable(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $laborer = Laborer::create([
                                    'name_ar'         => $data['name_ar'],
                                    'passport_number' => $data['passport_number'],
                                    'nationality_id'  => $data['nationality_id'],
                                    'profession_id'   => $data['profession_id'],
                                    'gender'          => $data['gender'] ?? null,
                                    'phone_1'         => $data['phone_1'] ?? null,
                                    'is_available'    => true,
                                ]);
                                Cache::forget('recruitment_accommodation.workers');
                                return $laborer->id;
                            })
                        )
                        ->columnSpan(1),

                    Forms\Components\Select::make('customer_id')
                        ->label('العميل')
                        ->options(fn () => Client::query()->get()
                            ->mapWithKeys(fn ($c) => [
                                $c->id => $c->name_ar . ($c->national_id ? ' (' . $c->national_id . ')' : ''),
                            ])->toArray()
                        )
                        ->searchable()
                        ->disabled($readonly || fn (Forms\Get $get) => (bool) $get('contract_no'))
                        ->dehydrated(true)
                        ->columnSpan(1),
                ])
                ->columns(2),

            Forms\Components\Section::make('بيانات الإدخال')
                ->schema([
                    Forms\Components\Select::make('entry_type')
                        ->label('نوع الدخول')
                        ->options([
                            'new_arrival' => 'وافد جديد',
                            'return'      => 'استرجاع',
                            'transfer'    => 'نقل كفالة',
                        ])
                        ->required(!$readonly)
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->live()
                        ->columnSpan(1),

                    Forms\Components\Select::make('transfer_client_id')
                        ->label('عميل نقل كفالة')
                        ->options(fn () => Client::query()->get()
                            ->mapWithKeys(fn ($c) => [
                                $c->id => $c->name_ar . ($c->national_id ? ' (' . $c->national_id . ')' : ''),
                            ])->toArray()
                        )
                        ->searchable()
                        ->visible(fn (Forms\Get $get) => $get('entry_type') === 'transfer')
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->when(! $readonly, fn ($f) => $f
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name_ar')
                                    ->label('الاسم (عربي)')->required()->maxLength(255),
                                Forms\Components\TextInput::make('national_id')
                                    ->label('رقم الهوية')->maxLength(50)->nullable(),
                                Forms\Components\TextInput::make('mobile')
                                    ->label('الجوال')->tel()->maxLength(20)->nullable(),
                                Forms\Components\TextInput::make('email')
                                    ->label('البريد الإلكتروني')->email()->maxLength(255)->nullable(),
                            ])
                            ->createOptionUsing(fn (array $data) => Client::create([
                                'name_ar'     => $data['name_ar'],
                                'national_id' => $data['national_id'] ?? null,
                                'mobile'      => $data['mobile'] ?? null,
                                'email'       => $data['email'] ?? null,
                            ])->id)
                        )
                        ->columnSpan(1),

                    Forms\Components\FileUpload::make('transfer_contract_file')
                        ->label('صورة عقد نقل الكفالة')
                        ->disk('public')
                        ->directory('transfer-contracts')
                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                        ->maxSize(10240)
                        ->visible(fn (Forms\Get $get) => $get('entry_type') === 'transfer')
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->columnSpan(2),

                    Forms\Components\DateTimePicker::make('entry_date')
                        ->label('تاريخ الدخول')
                        ->required(!$readonly)
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->native(false)
                        ->columnSpan(1),

                    Forms\Components\DateTimePicker::make('exit_date')
                        ->label('تاريخ خروج')
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->native(false)
                        ->columnSpan(1),

                    Forms\Components\Select::make('building_id')
                        ->label('المبنى')
                        ->options(fn () => Building::query()->get()
                            ->mapWithKeys(fn ($b) => [
                                $b->id => $b->name . ' (' . $b->available_capacity . ' متاح)',
                            ])->toArray()
                        )
                        ->required(!$readonly)
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->searchable()
                        ->columnSpan(1),
                ])
                ->columns(2),

            Forms\Components\Section::make('سجل الحالات')
                ->schema([
                    \Filament\Forms\Components\View::make('filament.forms.components.housing-status-table')
                        ->viewData(fn (Forms\Get $get) => [
                            'statuses'            => static::housingStatusOptions(),
                            'statusDates'         => json_decode($get('all_status_dates') ?? '{}', true) ?? [],
                            'statusDurations'     => [],
                            'currentStatus'       => $get('status_key') ?? '',
                            'statusStatePath'     => 'data.status_key',
                            'statusDateStatePath' => 'data.status_date',
                            'readonly'            => $readonly,
                        ])
                        ->columnSpanFull(),

                    Forms\Components\Hidden::make('status_key'),
                    Forms\Components\Hidden::make('status_date')->default(now()->toDateString()),
                    Forms\Components\Hidden::make('all_status_dates')->default('{}'),
                ])
                ->columns(1),
        ];
    }

    // ── Filament Resource hooks ────────────────────────────────────────────

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
                    ->default('—'),

                Tables\Columns\TextColumn::make('laborer.name_ar')
                    ->label('العاملة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('laborer.passport_number')
                    ->label('رقم الجواز')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('customer.name_ar')
                    ->label('العميل')
                    ->searchable()
                    ->default('—'),

                Tables\Columns\TextColumn::make('entry_type')
                    ->label('نوع الدخول')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'new_arrival' => 'وافد جديد',
                        'return'      => 'استرجاع',
                        'transfer'    => 'نقل كفالة',
                        default       => $state ?? '—',
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'new_arrival' => 'success',
                        'return'      => 'warning',
                        'transfer'    => 'info',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('building.name')
                    ->label('المبنى')
                    ->sortable()
                    ->default('—'),

                Tables\Columns\TextColumn::make('status_key')
                    ->label('الحالة')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (?string $state) => static::housingStatusOptions()[$state] ?? ($state ?: '—')),

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
                    ->options([
                        'new_arrival' => 'وافد جديد',
                        'return'      => 'استرجاع',
                        'transfer'    => 'نقل كفالة',
                    ]),
                Tables\Filters\SelectFilter::make('status_key')
                    ->label('الحالة')
                    ->options(static::housingStatusOptions()),
                Tables\Filters\SelectFilter::make('nationality')
                    ->label('الجنسية')
                    ->options(fn () => Nationality::where('is_active', true)
                        ->get()
                        ->mapWithKeys(fn ($n) => [$n->id => $n->name_ar])
                        ->toArray()
                    )
                    ->query(fn (Builder $query, array $data) =>
                        $data['value']
                            ? $query->whereHas('laborer', fn ($q) => $q->where('nationality_id', $data['value']))
                            : $query
                    ),
                Tables\Filters\Filter::make('active')
                    ->label('داخل فقط (بدون تاريخ خروج)')
                    ->query(fn (Builder $query) => $query->whereNull('exit_date')),
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
                        ->label('رقم العقد')->default('—'),
                    Infolists\Components\TextEntry::make('laborer.name_ar')
                        ->label('اسم العاملة')->default('—'),
                    Infolists\Components\TextEntry::make('laborer.passport_number')
                        ->label('رقم الجواز')->default('—'),
                    Infolists\Components\TextEntry::make('customer.name_ar')
                        ->label('العميل')->default('—'),
                ])
                ->columns(2),

            Infolists\Components\Section::make('بيانات الإدخال')
                ->schema([
                    Infolists\Components\TextEntry::make('entry_type')
                        ->label('نوع الدخول')
                        ->badge()
                        ->formatStateUsing(fn (?string $state) => match ($state) {
                            'new_arrival' => 'وافد جديد',
                            'return'      => 'استرجاع',
                            'transfer'    => 'نقل كفالة',
                            default       => $state ?? '—',
                        })
                        ->color(fn (?string $state) => match ($state) {
                            'new_arrival' => 'success',
                            'return'      => 'warning',
                            'transfer'    => 'info',
                            default       => 'gray',
                        }),
                    Infolists\Components\TextEntry::make('entry_date')
                        ->label('تاريخ الدخول')->date('Y-m-d')->default('—'),
                    Infolists\Components\TextEntry::make('exit_date')
                        ->label('تاريخ الخروج')->date('Y-m-d')->default('—'),
                    Infolists\Components\TextEntry::make('building.name')
                        ->label('المبنى')->default('—'),
                    Infolists\Components\TextEntry::make('status_key')
                        ->label('الحالة الحالية')
                        ->badge()
                        ->color('info')
                        ->formatStateUsing(fn (?string $state) => static::housingStatusOptions()[$state] ?? ($state ?: '—')),
                ])
                ->columns(2),

            Infolists\Components\Section::make('بيانات نقل الكفالة')
                ->visible(fn ($record) => $record?->entry_type === 'transfer')
                ->schema([
                    Infolists\Components\TextEntry::make('transferData.transferClient.name_ar')
                        ->label('عميل نقل كفالة')->default('—'),
                    Infolists\Components\TextEntry::make('transferData.contract_file_path')
                        ->label('ملف عقد نقل الكفالة')
                        ->formatStateUsing(fn (?string $state) => $state ? basename($state) : '—'),
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
                                ->formatStateUsing(fn (?string $state) => static::housingStatusOptions()[$state] ?? ($state ?: '—')),
                            Infolists\Components\TextEntry::make('status_date')
                                ->label('التاريخ')->date('Y-m-d')->default('—'),
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
