<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\Finance\BranchTransactionResource;
use App\Models\Finance\BranchTransaction;
use App\Models\User;
use Filament\Forms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class FinancePendingApprovalTableWidget extends BaseWidget
{
    protected static ?string $navigationGroup = 'قسم المالية';
    protected static ?string $heading = 'آخر 5 معاملات تنتظر الموافقة';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        // Only show for users of type 'صاحب الشركة' (company owner)
        return $user->type === User::TYPE_COMPANY_OWNER;
    }

    protected function getTableQuery(): Builder
    {
        $q = BranchTransaction::query()
            ->with(['branch', 'currency', 'financeType'])
            ->pending();

        $user = auth()->user();
        if ($user && ! $user->hasRole('super_admin') && ! $user->can('finance.view_all_branches')) {
            $branchIds = $user->branches()->pluck('branches.id')->toArray();
            if (!empty($user->branch_id)) {
                $branchIds[] = (int) $user->branch_id;
            }
            $branchIds = array_values(array_unique(array_filter($branchIds)));
            if (! empty($branchIds)) {
                $q->whereIn('branch_id', $branchIds);
            }
        }

        return $q->latest('trx_date')->limit(5);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('trx_date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->sortable(),
                TextColumn::make('financeType.name_text')
                    ->label('النوع')
                    ->getStateUsing(fn ($record) => $record->financeType?->name_text ?? '—'),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->formatStateUsing(fn ($state, $record) => number_format((float) $state, 2) . ' ' . ($record->currency?->code ?? '')),
                TextColumn::make('reference_no')
                    ->label('المرجع')
                    ->placeholder('—'),
            ])
            ->emptyStateHeading('لا توجد معاملات تنتظر الموافقة')
            ->paginated(false)
            ->headerActions([
                \Filament\Tables\Actions\Action::make('view_all')
                    ->label('عرض الكل')
                    ->url(BranchTransactionResource::getUrl('index', ['tableFilters' => ['status' => ['value' => 'pending']]])),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->action(function (BranchTransaction $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('تمت الموافقة')
                            ->send();
                    })
                    ->visible(fn (BranchTransaction $record) => $record->status === 'pending'
                        && (auth()->user()?->hasRole('super_admin') || (auth()->user()?->can('finance.approve_transactions') ?? false))),
                \Filament\Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->rows(2),
                    ])
                    ->action(function (BranchTransaction $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejected_by' => auth()->id(),
                            'rejected_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('تم الرفض')
                            ->send();
                    })
                    ->visible(fn (BranchTransaction $record) => $record->status === 'pending'
                        && (auth()->user()?->hasRole('super_admin') || (auth()->user()?->can('finance.reject_transactions') ?? false))),
                \Filament\Tables\Actions\Action::make('edit')
                    ->label('تعديل')
                    ->url(fn (BranchTransaction $record) => BranchTransactionResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
