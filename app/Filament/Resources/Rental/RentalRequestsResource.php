<?php

namespace App\Filament\Resources\Rental;

use App\Filament\Resources\Rental\RentalRequestsResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Rental\RentalContractRequest;
use App\Services\Rental\RentalContractService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class RentalRequestsResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = RentalContractRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'rental';
    protected static ?string $navigationLabel = 'طلبات التأجير';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('branch_id')
                    ->label(tr('rental.fields.branch', [], null, 'dashboard') ?: 'Branch')
                    ->options(function () {
                        return Cache::remember('rental.branches', 21600, function () {
                            return \App\Models\MainCore\Branch::active()->get()->pluck('name', 'id')->toArray();
                        });
                    })
                    ->searchable(),

                Forms\Components\Select::make('customer_id')
                    ->label(tr('rental.fields.customer', [], null, 'dashboard') ?: 'Customer')
                    ->options(function () {
                        return Cache::remember('rental.customers', 21600, function () {
                            return \App\Models\Sales\Customer::active()->get()->pluck('name', 'id')->toArray();
                        });
                    })
                    ->required()
                    ->searchable(),

                Forms\Components\Select::make('desired_package_id')
                    ->label(tr('rental.fields.package', [], null, 'dashboard') ?: 'Package')
                    ->options(function () {
                        return Cache::remember('rental.packages', 21600, function () {
                            return \App\Models\Package::where('type', 'rental')->where('status', 'active')->get()->pluck('name', 'id')->toArray();
                        });
                    })
                    ->searchable(),

                Forms\Components\Select::make('desired_country_id')
                    ->label(tr('rental.fields.country', [], null, 'dashboard') ?: 'Country')
                    ->options(function () {
                        return Cache::remember('rental.countries', 21600, function () {
                            return \App\Models\MainCore\Country::where('is_active', true)->get()->pluck('name_text', 'id')->toArray();
                        });
                    })
                    ->searchable(),

                Forms\Components\Select::make('profession_id')
                    ->label(tr('rental.fields.profession', [], null, 'dashboard') ?: 'Profession')
                    ->options(function () {
                        return Cache::remember('rental.professions', 21600, function () {
                            return \App\Models\Recruitment\Profession::where('is_active', true)->get()->pluck('name_ar', 'id')->toArray();
                        });
                    })
                    ->searchable(),

                Forms\Components\Select::make('worker_gender')
                    ->label(tr('rental.fields.worker_gender', [], null, 'dashboard') ?: 'Worker Gender')
                    ->options([
                        'male' => tr('rental.fields.worker_gender.male', [], null, 'dashboard') ?: 'Male',
                        'female' => tr('rental.fields.worker_gender.female', [], null, 'dashboard') ?: 'Female',
                    ]),

                Forms\Components\DatePicker::make('start_date')
                    ->label(tr('rental.fields.start_date', [], null, 'dashboard') ?: 'Start Date')
                    ->required()
                    ->default(now()),

                Forms\Components\Select::make('duration_type')
                    ->label(tr('rental.fields.duration_type', [], null, 'dashboard') ?: 'Duration Type')
                    ->options([
                        'day' => tr('rental.duration_type.day', [], null, 'dashboard') ?: 'Day',
                        'month' => tr('rental.duration_type.month', [], null, 'dashboard') ?: 'Month',
                        'year' => tr('rental.duration_type.year', [], null, 'dashboard') ?: 'Year',
                    ])
                    ->required()
                    ->default('month'),

                Forms\Components\TextInput::make('duration')
                    ->label(tr('rental.fields.duration', [], null, 'dashboard') ?: 'Duration')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1),

                Forms\Components\Select::make('status')
                    ->label(tr('rental.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'pending' => tr('rental.requests.status.pending', [], null, 'dashboard') ?: 'Pending',
                        'under_review' => tr('rental.requests.status.under_review', [], null, 'dashboard') ?: 'Under Review',
                        'approved' => tr('rental.requests.status.approved', [], null, 'dashboard') ?: 'Approved',
                        'rejected' => tr('rental.requests.status.rejected', [], null, 'dashboard') ?: 'Rejected',
                        'converted' => tr('rental.requests.status.converted', [], null, 'dashboard') ?: 'Converted',
                    ])
                    ->default('pending'),

                Forms\Components\Textarea::make('admin_note')
                    ->label(tr('rental.fields.admin_note', [], null, 'dashboard') ?: 'Admin Note')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request_no')
                    ->label(tr('rental.fields.request_no', [], null, 'dashboard') ?: 'Request No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label(tr('rental.fields.customer', [], null, 'dashboard') ?: 'Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('desiredPackage.name')
                    ->label(tr('rental.fields.package', [], null, 'dashboard') ?: 'Package')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(tr('rental.fields.start_date', [], null, 'dashboard') ?: 'Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label(tr('rental.fields.duration', [], null, 'dashboard') ?: 'Duration')
                    ->formatStateUsing(fn ($record) => "{$record->duration} " . tr("rental.duration_type.{$record->duration_type}", [], null, 'dashboard')),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('rental.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'under_review',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'gray' => 'converted',
                    ])
                    ->formatStateUsing(fn ($state) => tr("rental.requests.status.{$state}", [], null, 'dashboard') ?: $state),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('rental.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'pending' => tr('rental.requests.status.pending', [], null, 'dashboard') ?: 'Pending',
                        'under_review' => tr('rental.requests.status.under_review', [], null, 'dashboard') ?: 'Under Review',
                        'approved' => tr('rental.requests.status.approved', [], null, 'dashboard') ?: 'Approved',
                        'rejected' => tr('rental.requests.status.rejected', [], null, 'dashboard') ?: 'Rejected',
                        'converted' => tr('rental.requests.status.converted', [], null, 'dashboard') ?: 'Converted',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label(tr('rental.requests.approve', [], null, 'dashboard') ?: 'Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);
                    })
                    ->visible(fn ($record) => $record->status === 'pending' || $record->status === 'under_review')
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('reject')
                    ->label(tr('rental.requests.reject', [], null, 'dashboard') ?: 'Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);
                    })
                    ->visible(fn ($record) => $record->status === 'pending' || $record->status === 'under_review')
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('convert')
                    ->label(tr('rental.requests.convert', [], null, 'dashboard') ?: 'Convert to Contract')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('info')
                    ->action(function ($record) {
                        $service = app(RentalContractService::class);
                        $service->convertRequestToContract($record);
                    })
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user()?->can('rental.requests.convert')),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentalRequests::route('/'),
            'view' => Pages\ViewRentalRequest::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.requests.view_any') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
