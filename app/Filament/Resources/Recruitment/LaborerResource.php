<?php

namespace App\Filament\Resources\Recruitment;

use App\Filament\Forms\Components\FileUpload;
use App\Filament\Resources\Recruitment\LaborerResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\Currency;
use App\Models\Recruitment\Agent;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Profession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class LaborerResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Laborer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'recruitment';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationTranslationKey = 'sidebar.recruitment.laborers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('recruitment.sections.basic_info', [], null, 'dashboard') ?: 'Basic Information')
                    ->schema([
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

                        Forms\Components\TextInput::make('passport_number')
                            ->label(tr('recruitment.fields.passport_number', [], null, 'dashboard') ?: 'Passport Number')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('passport_issue_place')
                            ->label(tr('recruitment.fields.passport_issue_place', [], null, 'dashboard') ?: 'Passport Issue Place')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('passport_issue_date')
                            ->label(tr('recruitment.fields.passport_issue_date', [], null, 'dashboard') ?: 'Passport Issue Date')
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('passport_expiry_date')
                            ->label(tr('recruitment.fields.passport_expiry_date', [], null, 'dashboard') ?: 'Passport Expiry Date')
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('birth_date')
                            ->label(tr('recruitment.fields.birth_date', [], null, 'dashboard') ?: 'Birth Date')
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\Select::make('gender')
                            ->label(tr('recruitment.fields.gender', [], null, 'dashboard') ?: 'Gender')
                            ->options([
                                'male' => tr('recruitment.fields.gender_male', [], null, 'dashboard') ?: 'Male',
                                'female' => tr('recruitment.fields.gender_female', [], null, 'dashboard') ?: 'Female',
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment.sections.classification', [], null, 'dashboard') ?: 'Classification')
                    ->schema([
                        Forms\Components\Select::make('nationality_id')
                            ->label(tr('recruitment.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                            ->relationship('nationality', 'name_en')
                            ->options(Nationality::query()->where('is_active', true)->get()->mapWithKeys(function ($nationality) {
                                $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                                return [$nationality->id => $label . ($nationality->code ? ' (' . $nationality->code . ')' : '')];
                            }))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('profession_id')
                            ->label(tr('recruitment.fields.profession', [], null, 'dashboard') ?: 'Profession')
                            ->relationship('profession', 'name_en')
                            ->options(Profession::query()->where('is_active', true)->get()->mapWithKeys(function ($profession) {
                                $label = app()->getLocale() === 'ar' ? $profession->name_ar : $profession->name_en;
                                return [$profession->id => $label . ($profession->code ? ' (' . $profession->code . ')' : '')];
                            }))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('experience_level')
                            ->label(tr('recruitment.fields.experience_level', [], null, 'dashboard') ?: 'Experience Level')
                            ->options([
                                'gulf_experience' => app()->getLocale() === 'ar' ? 'خبره دول الخليج' : 'Gulf Experience',
                                'new' => app()->getLocale() === 'ar' ? 'جديده' : 'New',
                            ])
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('social_status')
                            ->label(tr('recruitment.fields.social_status', [], null, 'dashboard') ?: 'Social Status')
                            ->maxLength(255)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment.sections.contact', [], null, 'dashboard') ?: 'Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('phone_1')
                            ->label(tr('recruitment.fields.phone_1', [], null, 'dashboard') ?: 'Phone 1')
                            ->tel()
                            ->maxLength(50)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('phone_2')
                            ->label(tr('recruitment.fields.phone_2', [], null, 'dashboard') ?: 'Phone 2')
                            ->tel()
                            ->maxLength(50)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('address')
                            ->label(tr('recruitment.fields.address', [], null, 'dashboard') ?: 'Address')
                            ->rows(2)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('relative_name')
                            ->label(tr('recruitment.fields.relative_name', [], null, 'dashboard') ?: 'Relative Name')
                            ->maxLength(255)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment.sections.agent_country', [], null, 'dashboard') ?: 'Agent & Country')
                    ->schema([
                        Forms\Components\Select::make('agent_id')
                            ->label(tr('recruitment.fields.agent', [], null, 'dashboard') ?: 'Agent')
                            ->relationship('agent', 'code')
                            ->options(Agent::query()->pluck('code', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('country_id')
                            ->label(tr('recruitment.fields.country', [], null, 'dashboard') ?: 'Country')
                            ->relationship('country', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name_text ?? $record->name['en'] ?? '')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment.sections.physical', [], null, 'dashboard') ?: 'Physical Attributes')
                    ->schema([
                        Forms\Components\TextInput::make('height')
                            ->label(tr('recruitment.fields.height', [], null, 'dashboard') ?: 'Height')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('cm')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('weight')
                            ->label(tr('recruitment.fields.weight', [], null, 'dashboard') ?: 'Weight')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('kg')
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('speaks_arabic')
                            ->label(tr('recruitment.fields.speaks_arabic', [], null, 'dashboard') ?: 'Speaks Arabic')
                            ->default(false)
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('speaks_english')
                            ->label(tr('recruitment.fields.speaks_english', [], null, 'dashboard') ?: 'Speaks English')
                            ->default(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment.sections.files', [], null, 'dashboard') ?: 'Files')
                    ->schema([
                        FileUpload::makeImage('personal_image', 'laborers/images')
                            ->label(tr('recruitment.fields.personal_image', [], null, 'dashboard') ?: 'Personal Image')
                            ->columnSpan(1),

                        FileUpload::document('cv_file', 'laborers/cvs')
                            ->label(tr('recruitment.fields.cv_file', [], null, 'dashboard') ?: 'CV File')
                            ->columnSpan(1),

                        FileUpload::any('intro_video', 'laborers/videos')
                            ->label(tr('recruitment.fields.intro_video', [], null, 'dashboard') ?: 'Intro Video')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment.sections.salary', [], null, 'dashboard') ?: 'Salary')
                    ->schema([
                        Forms\Components\TextInput::make('monthly_salary_amount')
                            ->label(tr('recruitment.fields.monthly_salary', [], null, 'dashboard') ?: 'Monthly Salary Amount')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->columnSpan(1),

                        Forms\Components\Select::make('monthly_salary_currency_id')
                            ->label(tr('recruitment.fields.currency', [], null, 'dashboard') ?: 'Currency')
                            ->relationship('salaryCurrency', 'code')
                            ->options(Currency::active()->pluck('code', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment.sections.flags', [], null, 'dashboard') ?: 'Flags')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->label(tr('recruitment.fields.is_available', [], null, 'dashboard') ?: 'Is Available')
                            ->default(true)
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('show_on_website')
                            ->label(tr('recruitment.fields.show_on_website', [], null, 'dashboard') ?: 'Show on Website')
                            ->default(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

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
                Tables\Columns\ImageColumn::make('personal_image')
                    ->label(tr('recruitment.fields.personal_image', [], null, 'dashboard') ?: 'Photo')
                    ->disk('public')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(url('/images/placeholder-user.png'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name_ar')
                    ->label(tr('recruitment.fields.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_en')
                    ->label(tr('recruitment.fields.name_en', [], null, 'dashboard') ?: 'Name (English)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('passport_number')
                    ->label(tr('recruitment.fields.passport_number', [], null, 'dashboard') ?: 'Passport Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nationality.name_' . app()->getLocale())
                    ->label(tr('recruitment.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                    ->formatStateUsing(fn ($state, $record) => $record->nationality
                        ? (app()->getLocale() === 'ar' ? $record->nationality->name_ar : $record->nationality->name_en)
                        : '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('profession.name_' . app()->getLocale())
                    ->label(tr('recruitment.fields.profession', [], null, 'dashboard') ?: 'Profession')
                    ->formatStateUsing(fn ($state, $record) => $record->profession
                        ? (app()->getLocale() === 'ar' ? $record->profession->name_ar : $record->profession->name_en)
                        : '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('agent.code')
                    ->label(tr('recruitment.fields.agent', [], null, 'dashboard') ?: 'Agent')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->label(tr('recruitment.fields.country', [], null, 'dashboard') ?: 'Country')
                    ->formatStateUsing(fn ($state, $record) => $record->country?->name_text ?? ($record->country?->name['en'] ?? ''))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_available')
                    ->label(tr('recruitment.fields.is_available', [], null, 'dashboard') ?: 'Available')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('nationality_id')
                    ->label(tr('recruitment.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                    ->relationship('nationality', 'name_en')
                    ->options(Nationality::query()->where('is_active', true)->get()->mapWithKeys(function ($nationality) {
                        $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                        return [$nationality->id => $label];
                    }))
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('profession_id')
                    ->label(tr('recruitment.fields.profession', [], null, 'dashboard') ?: 'Profession')
                    ->relationship('profession', 'name_en')
                    ->options(Profession::query()->where('is_active', true)->get()->mapWithKeys(function ($profession) {
                        $label = app()->getLocale() === 'ar' ? $profession->name_ar : $profession->name_en;
                        return [$profession->id => $label];
                    }))
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('agent_id')
                    ->label(tr('recruitment.fields.agent', [], null, 'dashboard') ?: 'Agent')
                    ->relationship('agent', 'code')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label(tr('recruitment.fields.is_available', [], null, 'dashboard') ?: 'Available')
                    ->placeholder(tr('general.all', [], null, 'dashboard') ?: 'All')
                    ->trueLabel(tr('status.active', [], null, 'dashboard') ?: 'Available')
                    ->falseLabel(tr('status.inactive', [], null, 'dashboard') ?: 'Not Available'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(tr('general.actions.details', [], null, 'dashboard') ?: 'Details'),
                Tables\Actions\EditAction::make()
                    ->label(tr('general.actions.edit', [], null, 'dashboard') ?: 'Edit')
                    ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('recruitment.laborers.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->label(tr('general.actions.delete', [], null, 'dashboard') ?: 'Delete')
                    ->visible(fn () => auth()->user()?->can('recruitment.laborers.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('recruitment.laborers.delete') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaborers::route('/'),
            'create' => Pages\CreateLaborer::route('/create'),
            'view' => Pages\ViewLaborer::route('/{record}'),
            'edit' => Pages\EditLaborer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['agent', 'country', 'nationality', 'profession', 'salaryCurrency']);
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.laborers.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.laborers.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.laborers.view') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.laborers.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('recruitment.laborers.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
