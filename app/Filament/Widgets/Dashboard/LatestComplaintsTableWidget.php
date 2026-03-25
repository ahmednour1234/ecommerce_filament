<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\ComplaintResource;
use App\Models\Complaint;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestComplaintsTableWidget extends BaseWidget
{
    protected static ?string $heading = 'آخر 10 شكاوي';
    protected static ?int $sort = 6;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin')
            || $user?->can('complaints.view_any')
            || false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Complaint::query()
                    ->with(['branch', 'assignedUser', 'creator'])
                    ->latest('created_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('complaint_no')
                    ->label('رقم الشكوى')
                    ->searchable()
                    ->url(fn ($record) => ComplaintResource::getUrl('edit', ['record' => $record])),

                TextColumn::make('complaint_description')
                    ->label('الوصف')
                    ->limit(50),

                BadgeColumn::make('problem_type')
                    ->label('نوع المشكلة')
                    ->colors([
                        'warning' => 'salary_issue',
                        'info'    => 'food_issue',
                        'danger'  => 'escape',
                        'gray'    => 'work_refusal',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'salary_issue' => 'مشكلة رواتب',
                        'food_issue'   => 'مشكلة طعام',
                        'escape'       => 'هروب',
                        'work_refusal' => 'رفض عمل',
                        default        => $state ?? '—',
                    }),

                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->color(fn ($state) => match ($state) {
                        'in_progress' => 'info',
                        'resolved'    => 'success',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'in_progress' => 'قيد المعالجة',
                        'resolved'    => 'تم الحل',
                        default       => $state ?? '—',
                    }),

                TextColumn::make('branch.name')
                    ->label('الفرع'),

                TextColumn::make('assignedUser.name')
                    ->label('مكلف به'),

                TextColumn::make('creator.name')
                    ->label('أضافه'),

                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
