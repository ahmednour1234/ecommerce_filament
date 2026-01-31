<?php

namespace App\Filament\Resources\Rental;

use App\Filament\Resources\Rental\CancelRefundRequestsResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Rental\RentalCancelRefundRequest;
use App\Services\Rental\FinanceGateway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CancelRefundRequestsResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = RentalCancelRefundRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-x-circle';
    protected static ?string $navigationGroup = 'قسم التأجير';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationTranslationKey = 'navigation.rental_cancel_refund';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('rental_contract_id')
                    ->label('Contract')
                    ->relationship('contract', 'contract_no')
                    ->required()
                    ->searchable(),

                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options([
                        'cancel' => tr('rental.cancel_refund.type.cancel', [], null, 'dashboard') ?: 'Cancel',
                        'refund' => tr('rental.cancel_refund.type.refund', [], null, 'dashboard') ?: 'Refund',
                    ])
                    ->required()
                    ->reactive(),

                Forms\Components\Textarea::make('reason')
                    ->label(tr('rental.cancel_refund.reason', [], null, 'dashboard') ?: 'Reason')
                    ->required()
                    ->rows(3),

                Forms\Components\TextInput::make('refund_amount')
                    ->label(tr('rental.cancel_refund.refund_amount', [], null, 'dashboard') ?: 'Refund Amount')
                    ->numeric()
                    ->default(0)
                    ->step(0.01)
                    ->visible(fn (callable $get) => $get('type') === 'refund'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract.contract_no')
                    ->label('Contract No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'danger' => 'cancel',
                        'warning' => 'refund',
                    ])
                    ->formatStateUsing(fn ($state) => tr("rental.cancel_refund.type.{$state}", [], null, 'dashboard') ?: $state),

                Tables\Columns\TextColumn::make('reason')
                    ->label(tr('rental.cancel_refund.reason', [], null, 'dashboard') ?: 'Reason')
                    ->limit(50),

                Tables\Columns\TextColumn::make('refund_amount')
                    ->label(tr('rental.cancel_refund.refund_amount', [], null, 'dashboard') ?: 'Refund Amount')
                    ->money('SAR')
                    ->visible(fn ($record) => $record->type === 'refund'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('rental.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'cancel' => tr('rental.cancel_refund.type.cancel', [], null, 'dashboard') ?: 'Cancel',
                        'refund' => tr('rental.cancel_refund.type.refund', [], null, 'dashboard') ?: 'Refund',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('rental.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $contract = $record->contract;
                        $financeGateway = app(FinanceGateway::class);

                        if ($record->type === 'cancel') {
                            $contract->update(['status' => 'cancelled']);
                        } elseif ($record->type === 'refund') {
                            $contract->update(['status' => 'cancelled']);
                            if ($record->refund_amount > 0) {
                                $financeGateway->postRefund($contract, $record->refund_amount, [
                                    'note' => "Refund for cancel request: {$record->reason}",
                                ]);
                            }
                        }

                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    })
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user()?->can('rental.cancel_refund.manage')),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    })
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user()?->can('rental.cancel_refund.manage')),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCancelRefundRequests::route('/'),
            'create' => Pages\CreateCancelRefundRequest::route('/create'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.cancel_refund.view_any') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
