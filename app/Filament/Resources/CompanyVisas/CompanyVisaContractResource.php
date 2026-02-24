<?php

namespace App\Filament\Resources\CompanyVisas;

use App\Filament\Resources\CompanyVisas\CompanyVisaContractResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use Modules\CompanyVisas\Entities\CompanyVisaContract;
use Modules\CompanyVisas\Entities\CompanyVisaRequest;
use App\Models\Recruitment\Agent;
use App\Models\Recruitment\Profession;
use App\Models\MainCore\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class CompanyVisaContractResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = CompanyVisaContract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'company_visas';
    protected static ?string $navigationLabel = 'عقود استقدام الشركة';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('company_visas.sections.basic_data', [], null, 'dashboard') ?: 'البيانات الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('contract_no')
                            ->label(tr('company_visas.fields.contract_no', [], null, 'dashboard') ?: 'رقم العقد')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('contract_date')
                            ->label(tr('company_visas.fields.contract_date', [], null, 'dashboard') ?: 'تاريخ العقد')
                            ->required()
                            ->default(now())
                            ->columnSpan(1),

                        Forms\Components\Select::make('visa_request_id')
                            ->label(tr('company_visas.fields.visa_request', [], null, 'dashboard') ?: 'طلب التأشيرة')
                            ->options(function () {
                                return Cache::remember('company_visas.visa_requests', 3600, function () {
                                    return CompanyVisaRequest::where('remaining_count', '>', 0)
                                        ->get()
                                        ->mapWithKeys(function ($request) {
                                            return [$request->id => $request->code . ' (' . $request->remaining_count . ' متبقي)'];
                                        })->toArray();
                                });
                            })
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state) {
                                    $request = CompanyVisaRequest::find($state);
                                    if ($request) {
                                        $set('workers_required', min($get('workers_required') ?? 0, $request->remaining_count));
                                    }
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\Select::make('agent_id')
                            ->label(tr('company_visas.fields.agent', [], null, 'dashboard') ?: 'الوكيل')
                            ->options(function () {
                                return Cache::remember('company_visas.agents', 21600, function () {
                                    return Agent::all()->mapWithKeys(function ($agent) {
                                        return [$agent->id => $agent->code . ' - ' . ($agent->name_ar ?? $agent->name_en)];
                                    })->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('profession_id')
                            ->label(tr('company_visas.fields.profession', [], null, 'dashboard') ?: 'المهنة')
                            ->options(function () {
                                return Cache::remember('company_visas.professions', 21600, function () {
                                    return Profession::where('is_active', true)->get()->mapWithKeys(function ($profession) {
                                        $label = app()->getLocale() === 'ar' ? $profession->name_ar : $profession->name_en;
                                        return [$profession->id => $label];
                                    })->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('country_id')
                            ->label(tr('company_visas.fields.country', [], null, 'dashboard') ?: 'الدولة')
                            ->options(function () {
                                return Cache::remember('company_visas.countries', 21600, function () {
                                    return Country::all()->mapWithKeys(function ($country) {
                                        $name = $country->name_text ?? ($country->name['en'] ?? '');
                                        return [$country->id => $name];
                                    })->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('workers_required')
                            ->label(tr('company_visas.fields.workers_required', [], null, 'dashboard') ?: 'عدد العمالة المطلوبة')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->helperText(function (Forms\Get $get) {
                                if ($get('visa_request_id')) {
                                    $request = CompanyVisaRequest::find($get('visa_request_id'));
                                    if ($request) {
                                        return tr('company_visas.helpers.workers_required', [], null, 'dashboard')
                                            ?: "يجب ألا يتجاوز الرصيد المتبقي من التأشيرة ({$request->remaining_count})";
                                    }
                                }
                                return null;
                            })
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label(tr('company_visas.fields.status', [], null, 'dashboard') ?: 'الحالة')
                            ->options([
                                'draft' => tr('company_visas.status.draft', [], null, 'dashboard') ?: 'مسودة',
                                'active' => tr('company_visas.status.active', [], null, 'dashboard') ?: 'نشط',
                                'completed' => tr('company_visas.status.completed', [], null, 'dashboard') ?: 'مكتمل',
                                'cancelled' => tr('company_visas.status.cancelled', [], null, 'dashboard') ?: 'ملغي',
                            ])
                            ->default('draft')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('company_visas.fields.notes', [], null, 'dashboard') ?: 'ملاحظات')
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
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('contract_no')
                    ->label(tr('company_visas.fields.contract_no', [], null, 'dashboard') ?: 'رقم العقد')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('visaRequest.code')
                    ->label(tr('company_visas.fields.visa_request', [], null, 'dashboard') ?: 'طلب التأشيرة')
                    ->url(fn ($record) => $record->visaRequest
                        ? CompanyVisaRequestResource::getUrl('view', ['record' => $record->visaRequest])
                        : null)
                    ->openUrlInNewTab()
                    ->sortable(),

                Tables\Columns\TextColumn::make('agent.code')
                    ->label(tr('company_visas.fields.agent', [], null, 'dashboard') ?: 'الوكيل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('profession.name_' . app()->getLocale())
                    ->label(tr('company_visas.fields.profession', [], null, 'dashboard') ?: 'المهنة')
                    ->formatStateUsing(fn ($state, $record) => $record->profession
                        ? (app()->getLocale() === 'ar' ? $record->profession->name_ar : $record->profession->name_en)
                        : '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->label(tr('company_visas.fields.country', [], null, 'dashboard') ?: 'الدولة')
                    ->formatStateUsing(fn ($state, $record) => $record->country?->name_text ?? ($record->country?->name['en'] ?? ''))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('workers_required')
                    ->label(tr('company_visas.fields.workers_required', [], null, 'dashboard') ?: 'عدد العمالة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('contract_date')
                    ->label(tr('company_visas.fields.contract_date', [], null, 'dashboard') ?: 'تاريخ العقد')
                    ->date()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('company_visas.fields.status', [], null, 'dashboard') ?: 'الحالة')
                    ->colors([
                        'success' => ['active', 'completed'],
                        'danger' => 'cancelled',
                        'gray' => 'draft',
                    ])
                    ->formatStateUsing(fn ($state) => tr("company_visas.status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('contract_date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(tr('common.date_from', [], null, 'dashboard') ?: 'من تاريخ'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label(tr('common.date_to', [], null, 'dashboard') ?: 'إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'], fn ($q, $date) => $q->whereDate('contract_date', '>=', $date))
                            ->when($data['date_until'], fn ($q, $date) => $q->whereDate('contract_date', '<=', $date));
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('company_visas.fields.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options([
                        'draft' => tr('company_visas.status.draft', [], null, 'dashboard') ?: 'مسودة',
                        'active' => tr('company_visas.status.active', [], null, 'dashboard') ?: 'نشط',
                        'completed' => tr('company_visas.status.completed', [], null, 'dashboard') ?: 'مكتمل',
                        'cancelled' => tr('company_visas.status.cancelled', [], null, 'dashboard') ?: 'ملغي',
                    ]),

                Tables\Filters\SelectFilter::make('agent_id')
                    ->label(tr('company_visas.fields.agent', [], null, 'dashboard') ?: 'الوكيل')
                    ->relationship('agent', 'code')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'view' => Pages\ViewContract::route('/{record}'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('company_visas.view_contracts') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('company_visas.create_contracts') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        return auth()->user()?->can('company_visas.view_contracts') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('company_visas.update_contracts') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
