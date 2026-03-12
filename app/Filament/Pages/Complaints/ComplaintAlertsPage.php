<?php

namespace App\Filament\Pages\Complaints;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\ComplaintResource;
use App\Models\Complaint;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ComplaintAlertsPage extends Page implements HasTable
{
    use TranslatableNavigation;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationGroup = 'قسم الشكاوي';
    protected static ?string $navigationLabel = 'تنبيهات الشكاوي';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.complaints.complaint-alerts';

    public function getTitle(): string|Htmlable
    {
        return tr('complaint.alerts.title', [], null, 'dashboard') ?: 'تنبيهات الشكاوي';
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('complaints.view_any') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return tr('complaint.alerts.navigation', [], null, 'dashboard') ?: 'تنبيهات الشكاوي';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Complaint::query()
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Complaint::query()
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->with(['branch', 'assignedUser'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('complaint_no')
                    ->label(tr('complaint.fields.complaint_no', [], null, 'dashboard') ?: 'رقم الشكوى')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('complaint_description')
                    ->label(tr('complaint.fields.complaint_description', [], null, 'dashboard') ?: 'وصف الشكوى')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label(tr('complaint.fields.priority', [], null, 'dashboard') ?: 'الأولوية')
                    ->formatStateUsing(fn ($state) => tr("complaint.priority.{$state}", [], null, 'dashboard') ?: $state)
                    ->colors(['danger' => 'very_high', 'warning' => 'high', 'info' => 'medium', 'gray' => 'low']),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('complaint.fields.status', [], null, 'dashboard') ?: 'الحالة')
                    ->formatStateUsing(fn ($state) => tr("complaint.status.{$state}", [], null, 'dashboard') ?: $state)
                    ->colors(['warning' => 'pending', 'info' => 'in_progress']),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('complaint.fields.branch', [], null, 'dashboard') ?: 'الفرع')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('common.created_at', [], null, 'dashboard') ?: 'تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(tr('common.view', [], null, 'dashboard') ?: 'عرض')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Complaint $record) => ComplaintResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading(tr('complaint.alerts.empty', [], null, 'dashboard') ?: 'لا توجد تنبيهات');
    }
}
