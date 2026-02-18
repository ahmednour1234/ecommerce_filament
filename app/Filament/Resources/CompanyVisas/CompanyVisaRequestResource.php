<?php

namespace App\Filament\Resources\CompanyVisas;

use App\Filament\Resources\CompanyVisas\CompanyVisaRequestResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use Modules\CompanyVisas\Entities\CompanyVisaRequest;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Profession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class CompanyVisaRequestResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = CompanyVisaRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'company_visas';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'sidebar.company_visas.requests';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('company_visas.sections.basic_data', [], null, 'dashboard') ?: 'البيانات الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(tr('company_visas.fields.code', [], null, 'dashboard') ?: 'رمز الطلب')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('request_date')
                            ->label(tr('company_visas.fields.request_date', [], null, 'dashboard') ?: 'تاريخ الطلب')
                            ->required()
                            ->default(now())
                            ->columnSpan(1),

                        Forms\Components\Select::make('nationality_id')
                            ->label(tr('company_visas.fields.nationality', [], null, 'dashboard') ?: 'الجنسية')
                            ->options(function () {
                                return Cache::remember('company_visas.nationalities', 21600, function () {
                                    return Nationality::where('is_active', true)->get()->mapWithKeys(function ($nationality) {
                                        $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                                        return [$nationality->id => $label];
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

                        Forms\Components\Radio::make('gender')
                            ->label(tr('company_visas.fields.gender', [], null, 'dashboard') ?: 'الجنس')
                            ->options([
                                'male' => tr('company_visas.gender.male', [], null, 'dashboard') ?: 'ذكر',
                                'female' => tr('company_visas.gender.female', [], null, 'dashboard') ?: 'أنثى',
                            ])
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('workers_count')
                            ->label(tr('company_visas.fields.workers_count', [], null, 'dashboard') ?: 'عدد العمالة')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('visa_number')
                            ->label(tr('company_visas.fields.visa_number', [], null, 'dashboard') ?: 'رقم التأشيرة')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label(tr('company_visas.fields.status', [], null, 'dashboard') ?: 'الحالة')
                            ->options([
                                'draft' => tr('company_visas.status.draft', [], null, 'dashboard') ?: 'مسودة',
                                'paid' => tr('company_visas.status.paid', [], null, 'dashboard') ?: 'مسدد',
                                'completed' => tr('company_visas.status.completed', [], null, 'dashboard') ?: 'مكتمل',
                                'rejected' => tr('company_visas.status.rejected', [], null, 'dashboard') ?: 'مرفوض',
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

                Tables\Columns\TextColumn::make('code')
                    ->label(tr('company_visas.fields.code', [], null, 'dashboard') ?: 'رمز الطلب')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('request_date')
                    ->label(tr('company_visas.fields.request_date', [], null, 'dashboard') ?: 'تاريخ الطلب')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('profession.name_' . app()->getLocale())
                    ->label(tr('company_visas.fields.profession', [], null, 'dashboard') ?: 'المهنة')
                    ->formatStateUsing(fn ($state, $record) => $record->profession
                        ? (app()->getLocale() === 'ar' ? $record->profession->name_ar : $record->profession->name_en)
                        : '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nationality.name_' . app()->getLocale())
                    ->label(tr('company_visas.fields.nationality', [], null, 'dashboard') ?: 'الجنسية')
                    ->formatStateUsing(fn ($state, $record) => $record->nationality
                        ? (app()->getLocale() === 'ar' ? $record->nationality->name_ar : $record->nationality->name_en)
                        : '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('gender')
                    ->label(tr('company_visas.fields.gender', [], null, 'dashboard') ?: 'الجنس')
                    ->formatStateUsing(fn ($state) => $state === 'male'
                        ? (tr('company_visas.gender.male', [], null, 'dashboard') ?: 'ذكر')
                        : (tr('company_visas.gender.female', [], null, 'dashboard') ?: 'أنثى'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('workers_count')
                    ->label(tr('company_visas.fields.workers_count', [], null, 'dashboard') ?: 'عدد العمالة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('used_remaining')
                    ->label(tr('company_visas.fields.used_remaining', [], null, 'dashboard') ?: 'الرصيد')
                    ->formatStateUsing(fn ($record) => "{$record->used_count} / {$record->remaining_count}")
                    ->badge()
                    ->color(fn ($record) => $record->remaining_count > 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('visa_number')
                    ->label(tr('company_visas.fields.visa_number', [], null, 'dashboard') ?: 'رقم التأشيرة')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('company_visas.fields.status', [], null, 'dashboard') ?: 'الحالة')
                    ->colors([
                        'success' => ['completed', 'paid'],
                        'danger' => 'rejected',
                        'gray' => 'draft',
                    ])
                    ->formatStateUsing(fn ($state) => tr("company_visas.status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('request_date')
                    ->form([
                        Forms\Components\DatePicker::make('request_from')
                            ->label(tr('common.date_from', [], null, 'dashboard') ?: 'من تاريخ'),
                        Forms\Components\DatePicker::make('request_until')
                            ->label(tr('common.date_to', [], null, 'dashboard') ?: 'إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['request_from'], fn ($q, $date) => $q->whereDate('request_date', '>=', $date))
                            ->when($data['request_until'], fn ($q, $date) => $q->whereDate('request_date', '<=', $date));
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('company_visas.fields.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options([
                        'draft' => tr('company_visas.status.draft', [], null, 'dashboard') ?: 'مسودة',
                        'paid' => tr('company_visas.status.paid', [], null, 'dashboard') ?: 'مسدد',
                        'completed' => tr('company_visas.status.completed', [], null, 'dashboard') ?: 'مكتمل',
                        'rejected' => tr('company_visas.status.rejected', [], null, 'dashboard') ?: 'مرفوض',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisaRequests::route('/'),
            'create' => Pages\CreateVisaRequest::route('/create'),
            'view' => Pages\ViewVisaRequest::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('company_visas.view_requests') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('company_visas.create_requests') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        return auth()->user()?->can('company_visas.view_requests') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
