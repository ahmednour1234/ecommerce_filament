<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Complaint;
use App\Models\MainCore\Branch;
use App\Models\Rental\RentalContract;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\RecruitmentContract;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class ComplaintResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Complaint::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'follow_up';
    protected static ?string $navigationLabel = 'قسم الشكاوي';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'sidebar.complaints.complaints';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('complaint.sections.basic_info', [], null, 'dashboard') ?: 'Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('complaint_no')
                            ->label(tr('complaint.fields.complaint_no', [], null, 'dashboard') ?: 'Complaint No')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\Select::make('contract_type')
                            ->label(tr('complaint.fields.contract_type', [], null, 'dashboard') ?: 'Contract Type')
                            ->options([
                                'App\Models\Rental\RentalContract' => tr('complaint.contract_type.rental', [], null, 'dashboard') ?: 'Rental Contract',
                                'App\Models\Recruitment\RecruitmentContract' => tr('complaint.contract_type.recruitment', [], null, 'dashboard') ?: 'Recruitment Contract',
                            ])
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('contract_id', null))
                            ->columnSpan(1),

                        Forms\Components\Select::make('contract_id')
                            ->label(tr('complaint.fields.contract', [], null, 'dashboard') ?: 'Contract')
                            ->options(function (callable $get) {
                                $contractType = $get('contract_type');
                                if (!$contractType) {
                                    return [];
                                }

                                if ($contractType === 'App\Models\Rental\RentalContract') {
                                    return Cache::remember('complaints.rental_contracts', 21600, function () {
                                        return RentalContract::withTrashed()
                                            ->get()
                                            ->mapWithKeys(function ($contract) {
                                                return [$contract->id => "{$contract->contract_no} - " . ($contract->customer->name ?? 'N/A')];
                                            })
                                            ->toArray();
                                    });
                                }

                                if ($contractType === 'App\Models\Recruitment\RecruitmentContract') {
                                    return Cache::remember('complaints.recruitment_contracts', 21600, function () {
                                        return RecruitmentContract::withTrashed()
                                            ->get()
                                            ->mapWithKeys(function ($contract) {
                                                $clientName = app()->getLocale() === 'ar' ? $contract->client->name_ar : $contract->client->name_en;
                                                return [$contract->id => "{$contract->contract_no} - {$clientName}"];
                                            })
                                            ->toArray();
                                    });
                                }

                                return [];
                            })
                            ->nullable()
                            ->searchable()
                            ->visible(fn (callable $get) => !empty($get('contract_type')))
                            ->columnSpan(1),

                        Forms\Components\Select::make('problem_type')
                            ->label(tr('complaint.fields.problem_type', [], null, 'dashboard') ?: 'نوع المشكلة')
                            ->options([
                                'salary_issue' => tr('complaint.problem_type.salary_issue', [], null, 'dashboard') ?: 'مشكلة رواتب',
                                'food_issue' => tr('complaint.problem_type.food_issue', [], null, 'dashboard') ?: 'مشكلة طعام',
                                'escape' => tr('complaint.problem_type.escape', [], null, 'dashboard') ?: 'هروب',
                                'work_refusal' => tr('complaint.problem_type.work_refusal', [], null, 'dashboard') ?: 'رفض عمل',
                            ])
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('phone_number')
                            ->label(tr('complaint.fields.phone_number', [], null, 'dashboard') ?: 'رقم التليفون')
                            ->tel()
                            ->maxLength(50)
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('nationality_id')
                            ->label(tr('complaint.fields.nationality', [], null, 'dashboard') ?: 'الجنسية')
                            ->relationship('nationality', 'name_ar')
                            ->options(function () {
                                return Cache::remember('complaints.nationalities', 21600, function () {
                                    return Nationality::where('is_active', true)
                                        ->get()
                                        ->mapWithKeys(function ($nationality) {
                                            return [$nationality->id => app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en];
                                        })
                                        ->toArray();
                                });
                            })
                            ->nullable()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('subject')
                            ->label(tr('complaint.fields.subject', [], null, 'dashboard') ?: 'Subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label(tr('complaint.fields.description', [], null, 'dashboard') ?: 'Description')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('complaint.sections.assignment', [], null, 'dashboard') ?: 'Assignment')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label(tr('complaint.fields.branch', [], null, 'dashboard') ?: 'Branch')
                            ->options(function () {
                                return Cache::remember('complaints.branches', 21600, function () {
                                    return Branch::active()->get()->pluck('name', 'id')->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('assigned_to')
                            ->label(tr('complaint.fields.assigned_to', [], null, 'dashboard') ?: 'Assigned To')
                            ->options(function () {
                                return Cache::remember('complaints.users', 21600, function () {
                                    return User::all()->pluck('name', 'id')->toArray();
                                });
                            })
                            ->nullable()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('priority')
                            ->label(tr('complaint.fields.priority', [], null, 'dashboard') ?: 'Priority')
                            ->options([
                                'low' => tr('complaint.priority.low', [], null, 'dashboard') ?: 'Low',
                                'medium' => tr('complaint.priority.medium', [], null, 'dashboard') ?: 'Medium',
                                'high' => tr('complaint.priority.high', [], null, 'dashboard') ?: 'High',
                                'urgent' => tr('complaint.priority.urgent', [], null, 'dashboard') ?: 'Urgent',
                            ])
                            ->required()
                            ->default('medium')
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label(tr('complaint.fields.status', [], null, 'dashboard') ?: 'Status')
                            ->options([
                                'pending' => tr('complaint.status.pending', [], null, 'dashboard') ?: 'Pending',
                                'in_progress' => tr('complaint.status.in_progress', [], null, 'dashboard') ?: 'In Progress',
                                'resolved' => tr('complaint.status.resolved', [], null, 'dashboard') ?: 'Resolved',
                                'closed' => tr('complaint.status.closed', [], null, 'dashboard') ?: 'Closed',
                            ])
                            ->required()
                            ->default('pending')
                            ->reactive()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('complaint.sections.resolution', [], null, 'dashboard') ?: 'Resolution')
                    ->schema([
                        Forms\Components\Textarea::make('resolution_notes')
                            ->label(tr('complaint.fields.resolution_notes', [], null, 'dashboard') ?: 'Resolution Notes')
                            ->rows(4)
                            ->visible(fn (callable $get) => in_array($get('status'), ['resolved', 'closed']))
                            ->columnSpanFull(),

                        Forms\Components\DateTimePicker::make('resolved_at')
                            ->label(tr('complaint.fields.resolved_at', [], null, 'dashboard') ?: 'Resolved At')
                            ->visible(fn (callable $get) => in_array($get('status'), ['resolved', 'closed']))
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),
                    ])
                    ->visible(fn (callable $get) => in_array($get('status'), ['resolved', 'closed']))
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('complaint_no')
                    ->label(tr('complaint.fields.complaint_no', [], null, 'dashboard') ?: 'Complaint No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label(tr('complaint.fields.subject', [], null, 'dashboard') ?: 'Subject')
                    ->searchable()
                    ->limit(50)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('problem_type')
                    ->label(tr('complaint.fields.problem_type', [], null, 'dashboard') ?: 'نوع المشكلة')
                    ->colors([
                        'warning' => 'salary_issue',
                        'info' => 'food_issue',
                        'danger' => 'escape',
                        'gray' => 'work_refusal',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'salary_issue' => tr('complaint.problem_type.salary_issue', [], null, 'dashboard') ?: 'مشكلة رواتب',
                        'food_issue' => tr('complaint.problem_type.food_issue', [], null, 'dashboard') ?: 'مشكلة طعام',
                        'escape' => tr('complaint.problem_type.escape', [], null, 'dashboard') ?: 'هروب',
                        'work_refusal' => tr('complaint.problem_type.work_refusal', [], null, 'dashboard') ?: 'رفض عمل',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label(tr('complaint.fields.phone_number', [], null, 'dashboard') ?: 'رقم التليفون')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('nationality.name_ar')
                    ->label(tr('complaint.fields.nationality', [], null, 'dashboard') ?: 'الجنسية')
                    ->formatStateUsing(fn ($state, $record) => $record->nationality ? (app()->getLocale() === 'ar' ? $record->nationality->name_ar : $record->nationality->name_en) : '-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('contract_info')
                    ->label(tr('complaint.fields.contract', [], null, 'dashboard') ?: 'Contract')
                    ->formatStateUsing(function ($record) {
                        if (!$record->contract_type || !$record->contract_id) {
                            return '-';
                        }

                        if ($record->contract_type === 'App\Models\Rental\RentalContract') {
                            $contract = RentalContract::find($record->contract_id);
                            return $contract ? $contract->contract_no : '-';
                        }

                        if ($record->contract_type === 'App\Models\Recruitment\RecruitmentContract') {
                            $contract = RecruitmentContract::find($record->contract_id);
                            return $contract ? $contract->contract_no : '-';
                        }

                        return '-';
                    })
                    ->searchable(false)
                    ->sortable(false),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('complaint.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => tr("complaint.status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label(tr('complaint.fields.priority', [], null, 'dashboard') ?: 'Priority')
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        'low' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => tr("complaint.priority.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label(tr('complaint.fields.assigned_to', [], null, 'dashboard') ?: 'Assigned To')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('complaint.fields.branch', [], null, 'dashboard') ?: 'Branch')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('common.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('complaint.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'pending' => tr('complaint.status.pending', [], null, 'dashboard') ?: 'Pending',
                        'in_progress' => tr('complaint.status.in_progress', [], null, 'dashboard') ?: 'In Progress',
                        'resolved' => tr('complaint.status.resolved', [], null, 'dashboard') ?: 'Resolved',
                        'closed' => tr('complaint.status.closed', [], null, 'dashboard') ?: 'Closed',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->label(tr('complaint.fields.priority', [], null, 'dashboard') ?: 'Priority')
                    ->options([
                        'low' => tr('complaint.priority.low', [], null, 'dashboard') ?: 'Low',
                        'medium' => tr('complaint.priority.medium', [], null, 'dashboard') ?: 'Medium',
                        'high' => tr('complaint.priority.high', [], null, 'dashboard') ?: 'High',
                        'urgent' => tr('complaint.priority.urgent', [], null, 'dashboard') ?: 'Urgent',
                    ]),

                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('complaint.fields.branch', [], null, 'dashboard') ?: 'Branch')
                    ->options(function () {
                        return Branch::active()->get()->pluck('name', 'id')->toArray();
                    }),

                Tables\Filters\SelectFilter::make('contract_type')
                    ->label(tr('complaint.fields.contract_type', [], null, 'dashboard') ?: 'Contract Type')
                    ->options([
                        'App\Models\Rental\RentalContract' => tr('complaint.contract_type.rental', [], null, 'dashboard') ?: 'Rental Contract',
                        'App\Models\Recruitment\RecruitmentContract' => tr('complaint.contract_type.recruitment', [], null, 'dashboard') ?: 'Recruitment Contract',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(tr('common.date_from', [], null, 'dashboard') ?: 'From Date'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(tr('common.date_to', [], null, 'dashboard') ?: 'To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListComplaints::route('/'),
            'create' => Pages\CreateComplaint::route('/create'),
            'view' => Pages\ViewComplaint::route('/{record}'),
            'edit' => Pages\EditComplaint::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('complaints.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('complaints.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('complaints.view') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('complaints.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('complaints.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
